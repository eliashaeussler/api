<?php
/**
 * Copyright (c) 2019 Elias Häußler <elias@haeussler.dev>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Service;

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaDiff;
use Doctrine\DBAL\Schema\TableDiff;
use EliasHaeussler\Api\Exception\DatabaseException;
use EliasHaeussler\Api\Exception\FileNotFoundException;
use EliasHaeussler\Api\Exception\InvalidFileException;
use EliasHaeussler\Api\Utility\ConsoleUtility;
use EliasHaeussler\Api\Utility\GeneralUtility;
use EliasHaeussler\Api\Utility\LocalizationUtility;
use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Statements\CreateStatement;

/**
 * Database connection service.
 *
 * This class provides a service to connect and interact with the database. A connection will automatically tried to be
 * established if any class instance is constructed.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0+
 */
class ConnectionService
{
    /** @var string Location of SQL database schema files */
    const SCHEMA_PATH = SOURCE_PATH . "/schemas";

    /** @var string File pattern of SQL schema files */
    const SCHEMA_FILE_PATTERN = "*.sql";

    /** @var string Table name prefix for temporary tables */
    const TEMPORARY_TABLE_PREFIX = "zzz___";

    /** @var Connection Database connection */
    protected $database;

    /**
     * Initialize connection service.
     *
     * Initializes the connection service by connecting to the database. Database credentials need to be defined as
     * global environment variables.
     *
     * @throws DBALException if the database connection cannot be established
     */
    public function __construct()
    {
        $this->connect();
    }

    /**
     * Connect to database.
     *
     * Note that it's not possible to pass custom database credentials. You need to define them in your global
     * environment. This can be done using a .env file. Requested environment variables:
     *
     * DB_HOST => Database host (defaults to `localhost`)
     * DB_USER => Database user
     * DB_PASS => Password for database user
     * DB_NAME => Database name
     * DB_PORT => Database port (defaults to `3306`)
     *
     * @throws DBALException if the database connection cannot be established
     */
    public function connect()
    {
        $parameters = [
            "host" => GeneralUtility::getEnvironmentVariable("DB_HOST", "localhost"),
            "user" => GeneralUtility::getEnvironmentVariable("DB_USER"),
            "password" => GeneralUtility::getEnvironmentVariable("DB_PASSWORD"),
            "dbname" => GeneralUtility::getEnvironmentVariable("DB_NAME"),
            "port" => GeneralUtility::getEnvironmentVariable("DB_PORT", 3306),
            "driver" => "pdo_mysql",
        ];
        $this->database = $this->establishConnection($parameters);
    }

    /**
     * Create table schema.
     *
     * Creates or modifies the table schemas for a given set of API controllers or all available schema files. For this,
     * the appropriate table will be marked as temporary, then the new table schema will be added to the database.
     * Finally, data from the temporary table will be inserted in the new table and the temporary table will be removed.
     * Note that this only takes place if the schema files contain `CREATE TABLE` statements. In case of an error the
     * old table schema will be restored and an exception will be thrown.
     *
     * @param string|array|null $controllers Name of one or more API controllers which will be used to identity the schema file
     *
     * @throws DBALException         if the database connection cannot be established
     * @throws FileNotFoundException if a table schema file is not available
     */
    public function createSchema($controllers = null)
    {
        if (!$this->database) {
            $this->connect();
        }

        // Get database schema manager and query builder
        $db = $this->database;
        $schemaManager = $db->getSchemaManager();
        $queryBuilder = $db->createQueryBuilder();

        // Get list of schema files
        $schemaFiles = $this->getListOfSchemaFiles($controllers);
        if (!$schemaFiles) {
            return;
        }

        foreach ($schemaFiles as $schemaFile) {
            // Get contents of schema file
            $schemaCount = $this->readContentsOfSchemaFile($schemaFile, $definedSchemas);

            if ($schemaCount === false || $schemaCount == 0) {
                continue;
            }

            // Create table schemas
            foreach ($definedSchemas as $definedSchema) {
                // Reset query builder
                $queryBuilder->resetQueryParts();

                // Get table name
                $definedQuery = $definedSchema[0];
                $definedTableName = trim($definedSchema[1], " `");
                $tempTableName = self::TEMPORARY_TABLE_PREFIX . $definedTableName;

                // Create table schema
                if (!$schemaManager->tablesExist([$definedTableName])) {
                    // Create table if not exists yet
                    $db->exec($definedQuery);
                } else {
                    // Fetch all data from table
                    $currentDataSet = $queryBuilder->select("*")
                        ->from($definedTableName)
                        ->execute()
                        ->fetchAll();

                    try {
                        // Mark table as temporary
                        $schemaManager->renameTable($definedTableName, $tempTableName);

                        // Re-create table with given schema
                        $db->exec($definedQuery);

                        // Restore result set
                        if (!empty($currentDataSet)) {
                            $definedTable = $schemaManager->listTableDetails($definedTableName);
                            $tempTable = $schemaManager->listTableDetails($tempTableName);

                            // Re-create missing columns
                            foreach ($tempTable->getColumns() as $tempTableColumn) {
                                if ($definedTable->hasColumn($tempTableColumn->getName())) {
                                    continue;
                                }

                                $tableDiff = new TableDiff($definedTableName, [$tempTableColumn]);
                                $schemaManager->alterTable($tableDiff);
                            }

                            // Insert result set
                            foreach ($currentDataSet as $dataSet) {
                                $db->insert($definedTableName, $dataSet);
                            }
                        }

                        // Drop temporary table
                        $schemaManager->dropTable($tempTableName);
                    } catch (DBALException $e) {
                        // Restore new table with temporary table if an error occurs
                        $schemaManager->dropTable($definedTableName);
                        $schemaManager->renameTable($tempTableName, $definedTableName);

                        throw $e;
                    }
                }
            }
        }
    }

    /**
     * Drop unused components from database schema.
     *
     * Allows dropping of unused components such as fields and tables from the current database schema. Dropping these
     * components is based on the given schema files. If dropping tables is enabled, all tables which are not present
     * in the schema files will be dropped. Dropping fields compares the current database schema with the given schema
     * from the schema files and drops each field which is not defined in the schema files. For this, a temporary table
     * based on the defined schema files will be created and dropped after execution.
     *
     * CAUTION: FIELDS AND TABLES WILL BE COMPLETELY DROPPED WHICH WILL CAUSE THEM TO LOSE ALL CONNECTED DATA AS WELL!
     *
     * @param bool              $dropFields  Define whether to drop unused fields from current database schema
     * @param bool              $dropTables  Define whether to drop unused tables from current database schema
     * @param string|array|null $controllers Name of one or more API controllers which will be used to identity the schema file
     * @param bool              $dryRun      Define whether to perform a dry run without changing the database schema
     *
     * @throws DBALException         if the database connection cannot be established
     * @throws FileNotFoundException if a table schema file is not available
     *
     * @return array Report of dropped tables and fields
     */
    public function dropUnusedComponents(
        bool $dropFields = true,
        bool $dropTables = false,
        $controllers = null,
        bool $dryRun = true
    ): array {
        $report = [
            "tables" => [],
            "fields" => [],
        ];

        if ($dropFields || $dropTables) {
            // Ensure established database connection
            if (!$this->database) {
                $this->connect();
            }

            // Get database schema manager
            $db = $this->database;
            $schemaManager = $db->getSchemaManager();

            // Get list of schema files
            $schemaFiles = $this->getListOfSchemaFiles($controllers);

            if ($schemaFiles) {
                foreach ($schemaFiles as $schemaFile) {
                    // Get contents of schema file
                    $schemaCount = $this->readContentsOfSchemaFile($schemaFile, $definedSchemas);

                    if ($schemaCount === false || $schemaCount == 0) {
                        continue;
                    }

                    // Normalize schemas
                    array_walk($definedSchemas, function (&$schema) {
                        $table = strtolower(trim($schema[1], " `"));
                        $schema[1] = $table;
                    });

                    // Drop tables
                    if ($dropTables) {
                        foreach ($schemaManager->listTables() as $currentTable) {
                            $currentTableName = $currentTable->getName();
                            $normalizedTableName = strtolower(trim($currentTableName));

                            // Drop table if it's not listed in the defined schemas
                            if (!in_array($normalizedTableName, array_column($definedSchemas, 1))) {
                                if (!$dryRun) {
                                    $schemaManager->dropTable($currentTableName);
                                }
                                $report["tables"][] = $currentTable;
                            }
                        }
                    }

                    // Drop fields
                    if ($dropFields) {
                        foreach ($definedSchemas as $definedSchema) {
                            // Get table name
                            $definedQuery = $definedSchema[0];
                            $definedTableName = $definedSchema[1];
                            $tempTableName = self::TEMPORARY_TABLE_PREFIX . $definedTableName;

                            // Do not continue if tables does not exist currently
                            if (!$schemaManager->tablesExist([$definedTableName])) {
                                continue;
                            }

                            // Get SQL parser
                            $parser = new Parser($definedQuery);

                            // Allow only CREATE statements
                            if (empty($parser->statements) || !$parser->statements[0] instanceof CreateStatement) {
                                continue;
                            }

                            // Get parsed statement
                            /** @var CreateStatement $statement */
                            $statement = $parser->statements[0];

                            // Rename table name in query to create temporary table
                            $statement->name->table = $tempTableName;
                            $statement->name->expr = str_replace($definedTableName, $tempTableName, $statement->name->expr);
                            $definedQuery = $statement->build();

                            // Create temporary table
                            $db->exec($definedQuery);

                            // Define schemas
                            $currentTable = $schemaManager->listTableDetails($definedTableName);
                            $definedTempTable = $schemaManager->listTableDetails($tempTableName);
                            $currentSchema = new Schema([$currentTable]);
                            $definedSchema = new Schema([$definedTempTable]);
                            $definedSchema->renameTable($tempTableName, $definedTableName);

                            // Compare tables
                            $comparator = new Comparator();
                            $schemaDiff = $comparator->compare($currentSchema, $definedSchema);

                            // Remove fields
                            $tableDiff = new TableDiff($definedTableName);
                            foreach ($schemaDiff->changedTables as $key => $currentSchema) {
                                if ($currentSchema->name == $definedTableName) {
                                    $tableDiff->removedColumns = $currentSchema->removedColumns;

                                    $report["fields"][] = [
                                        "table" => $currentTable,
                                        "fields" => $currentSchema->removedColumns,
                                    ];
                                }
                            }
                            $schemaDiff = new SchemaDiff();
                            $schemaDiff->changedTables[$definedTableName] = $tableDiff;
                            $sql = $schemaDiff->toSql($db->getDatabasePlatform());

                            if (!$dryRun && $sql) {
                                foreach ($sql as $definedQuery) {
                                    $db->exec($definedQuery);
                                }
                            }

                            // Remove temporary table
                            $schemaManager->dropTable($tempTableName);
                        }
                    }
                }
            }
        }

        return $report;
    }

    /**
     * Migrate legacy SQLite database files to current MySQL database.
     *
     * @param array|string $files The database files to be used for migration
     *
     * @throws InvalidFileException  if no files are provided for migration
     * @throws FileNotFoundException if any of the specified files does not exist
     * @throws DBALException         if any database connection cannot be established
     * @throws DatabaseException     if connection to any database was not successful
     */
    public function migrate($files)
    {
        if (!$files) {
            throw new InvalidFileException(
                LocalizationUtility::localize("exception.1546890034"),
                1546890034
            );
        }

        // Normalize files to array
        if (!is_array($files)) {
            $files = [$files];
        }

        // Create database schema first
        $this->createSchema();

        // Migrate SQLite databases
        foreach ($files as $file) {
            // Get full path
            $path = realpath($file);

            if ($path === false) {
                throw new FileNotFoundException(
                    LocalizationUtility::localize("exception.1546890434", null, null, $file),
                    1546890434
                );
            }

            // Connect to database
            $con = $this->establishConnection([
                "path" => $path,
                "driver" => "pdo_sqlite",
            ]);

            if (!$con->isConnected()) {
                throw new DatabaseException(
                    LocalizationUtility::localize("exception.1546890846", null, null, $path),
                    1546890846
                );
            }

            // Get database schema manager of current database
            $scm = $this->database->getSchemaManager();

            // Migrate data
            foreach ($con->getSchemaManager()->listTables() as $table) {
                // Create table in new database if it does not exist yet
                if (!$scm->tablesExist([$table->getName()])) {
                    $scm->createTable($table);
                }

                // Fetch all data
                $queryBuilder = $con->createQueryBuilder();
                $result = $queryBuilder->select("*")
                    ->from($table->getName())
                    ->execute()
                    ->fetchAll();

                if (!$result) {
                    continue;
                }

                // Insert data into current database
                foreach ($result as $row) {
                    $insQueryBuilder = $this->database->createQueryBuilder();
                    array_walk($row, function (&$v) {
                        $v = $this->database->quote($v);
                    });
                    $insResult = $insQueryBuilder->insert($table->getName())
                        ->values($row)
                        ->execute();

                    if (!$insResult) {
                        throw new DatabaseException(
                            LocalizationUtility::localize("exception.1546892040", null, null, $path),
                            1546892040
                        );
                    }
                }
            }
        }
    }

    /**
     * Export database.
     *
     * Creates a database dump for the current database and writes the dumped contents to stdin. Note that this
     * method only writes the raw result of `mysqldump` to stdin and does NOT return or save it to a file. You
     * need to handle the passed through contents by your own.
     */
    public function export(): void
    {
        // Define script name and parameters
        $scriptName = "mysqldump";
        $parameters = [
            "host" => $this->database->getHost(),
            "user" => $this->database->getUsername(),
            "password" => $this->database->getPassword(),
            $this->database->getDatabase(),
            "default-character-set" => "utf8",
        ];

        // Build and execute command with parameters
        $command = ConsoleUtility::buildCommand($scriptName, $parameters);
        passthru($command);
    }

    /**
     * Get database connection.
     *
     * @return Connection Database connection
     */
    public function getDatabase(): Connection
    {
        return $this->database;
    }

    /**
     * Establish database connection with given parameters.
     *
     * Tries to establish a database connection with the given parameters. Configuration of the parameters must follow
     * DBAL requirements.
     *
     * @param array $parameters Database connection parameters
     *
     * @throws DBALException if the database connection cannot be established
     *
     * @return Connection The established database connection
     *
     * @see https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html
     */
    protected function establishConnection(array $parameters): Connection
    {
        LogService::log(
            sprintf("Trying to connect to database \"%s\" on host \"%s\"", $parameters["dbname"], $parameters["host"]),
            LogService::DEBUG
        );

        $con = DriverManager::getConnection($parameters);
        $con->connect();

        return $con;
    }

    /**
     * Get list of schema files.
     *
     * Returns an array including the file names of available schema files. The search mode for schema files can be
     * modified by providing a list of controller class names. If set, only the appropriate schema files will be returned.
     *
     * @param array|string|null $controllers Name of one or more API controllers which will be used to identity the schema file
     *
     * @return array List of schema files
     */
    protected function getListOfSchemaFiles($controllers = null): array
    {
        $files = [];

        // Get schema files for specified controllers
        if ($controllers) {
            $files = is_array($controllers) ? $controllers : [$controllers];
            array_walk($files, function (&$controller) {
                $file = strtolower(GeneralUtility::getControllerName($controller));
                $fileName = GeneralUtility::replaceFirst(self::SCHEMA_FILE_PATTERN, "*", $file);
                $controller = self::SCHEMA_PATH . "/" . $fileName;
            });

        // Get all available schema files
        } else {
            if (($allFiles = glob(self::SCHEMA_PATH . "/" . self::SCHEMA_FILE_PATTERN)) !== false) {
                $files = $allFiles;
            }
        }

        return $files;
    }

    /**
     * Read contents of database schema file.
     *
     * Reads the contents of a database schema file and returns the number of `CREATE TABLE` statements within it.
     * The `CREATE TABLE` statements will be stored in a by-reference variable which can then be accessed after
     * calling this method.
     *
     * @param string     $file    Schema file
     * @param array|null $schemas Result of {@see preg_match_all} containing `CREATE TABLE` statements
     *
     * @throws FileNotFoundException if a table schema file is not available
     *
     * @return int Number of `CREATE TABLE` statements inside schema file
     */
    protected function readContentsOfSchemaFile(string $file, ?array &$schemas): int
    {
        $contents = @file_get_contents($file);
        if (!$contents) {
            throw new FileNotFoundException(
                LocalizationUtility::localize("exception.1546889136", null, null, $file),
                1546889136
            );
        }

        return preg_match_all("/CREATE TABLE(.*?)\\(\n(?:.*?)\\);/ims", $contents, $schemas, PREG_SET_ORDER);
    }
}
