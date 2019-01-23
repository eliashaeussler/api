<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Service;

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

/**
 * Database connection service.
 *
 * This class provides a service to connect and interact with the database. A connection will automatically tried to be
 * established if any class instance is constructed.
 *
 * @package EliasHaeussler\Api\Service
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
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
     * Establish database connection with given parameters.
     *
     * Tries to establish a database connection with the given parameters. Configuration of the parameters must follow
     * DBAL requirements.
     *
     * @param array $parameters Database connection parameters
     * @return Connection The established database connection
     * @throws DBALException if the database connection cannot be established
     * @see https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html
     */
    protected function establishConnection(array $parameters): Connection
    {
        $con = DriverManager::getConnection($parameters);
        $con->connect();

        return $con;
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
     * @param string|array $controllers Name of one or more API controllers which will be used to identity the schema file
     * @param bool $strictMode `true` if values of fields differing from the new schema should not be inserted, `false` otherwise
     * @throws DBALException if the database connection cannot be established
     * @throws FileNotFoundException if a table schema file is not available
     */
    public function createSchema($controllers = "", bool $strictMode = true)
    {
        if (!$this->database) {
            $this->connect();
        }

        // Get database schema manager
        $db = $this->database;
        $schemaManager = $db->getSchemaManager();

        // Get list of schema files
        $files = $this->getListOfSchemaFiles($controllers);
        if (!$files) {
            return;
        }

        foreach ($files as $schemaFile)
        {
            // Get contents of schema file
            $schemaCount = $this->readContentsOfSchemaFile($schemaFile, $schemas);

            if ($schemaCount === false || $schemaCount == 0) {
                continue;
            }

            // Create table schemas
            foreach ($schemas as $currentSchema)
            {
                $queryBuilder = $db->createQueryBuilder();

                // Get table name
                $query = $currentSchema[0];
                $tableName = trim($currentSchema[1], " `");
                $tempTableName = self::TEMPORARY_TABLE_PREFIX . $tableName;

                // Create table schema
                if (!$schemaManager->tablesExist([$tableName])) {

                    // Create table if not exists yet
                    $db->exec($query);

                } else {

                    // Fetch all data from table
                    $resultSet = $queryBuilder->select("*")
                        ->from($tableName)
                        ->execute()
                        ->fetchAll();

                    try {

                        // Mark table as temporary
                        $schemaManager->renameTable($tableName, $tempTableName);

                        // Re-create table with given schema
                        $db->exec($query);

                        // Restore result set
                        if ($resultSet)
                        {
                            $newTable = $schemaManager->listTableDetails($tableName);
                            $tempTable = $schemaManager->listTableDetails($tempTableName);

                            // Re-create missing columns
                            foreach (array_keys($resultSet[0]) as $columnName)
                            {
                                if ($newTable->hasColumn($columnName)) {
                                    continue;
                                }

                                $column = $tempTable->getColumn($columnName);
                                $tableDiff = new TableDiff($tableName, [$column]);
                                $schemaManager->alterTable($tableDiff);
                            }

                            // Insert result set
                            foreach ($resultSet as $result)
                            {
                                if (!$strictMode) {
                                    array_walk($result, function (&$value, $field) use ($newTable, $db) {
                                        try {
                                            $value = $newTable->getColumn($field)
                                                ->getType()
                                                ->convertToPHPValue($value, $db->getDatabasePlatform());
                                        } catch (DBALException $e) {
                                            $value = null;
                                        }
                                    });
                                }

                                $db->insert($tableName, $result);
                            }
                        }

                        // Drop temporary table
                        $schemaManager->dropTable($tempTableName);

                    } catch (DBALException $e) {

                        // Restore new table with temporary table if an error occurs
                        $schemaManager->dropTable($tableName);
                        $schemaManager->renameTable($tempTableName, $tableName);

                        throw $e;

                    }
                }
            }
        }
    }

    /**
     * @todo add doc
     *
     * @param bool $dropFields
     * @param bool $dropTables
     * @param string $controllers
     * @throws DBALException
     * @throws FileNotFoundException
     */
    public function dropUnusedComponents(bool $dropFields = true, bool $dropTables = false, $controllers = "")
    {
        if (!$this->database) {
            $this->connect();
        }

        // Get database schema manager
        $db = $this->database;
        $schemaManager = $db->getSchemaManager();

        // Get list of schema files
        $files = $this->getListOfSchemaFiles($controllers);
        if (!$files) {
            return;
        }

        foreach ($files as $schemaFile)
        {
            // Get contents of schema file
            $schemaCount = $this->readContentsOfSchemaFile($schemaFile, $schemas);

            if ($schemaCount === false || $schemaCount == 0) {
                continue;
            }

            // Normalize schemas
            array_walk($schemas, function (&$schema) {
                $table = strtolower(trim($schema[1], " `"));
                $schema[1] = $table;
            });

            // Drop tables
            if ($dropTables) {
                foreach ($schemaManager->listTables() as $table) {
                    $normalizedTableName = strtolower(trim($table->getName()));
                    if (!in_array($normalizedTableName, array_column($schemas, 1))) {
                        $schemaManager->dropTable($table->getName());
                    }
                }
            }

            if (!$dropFields) {
                return;
            }

            // Drop fields
            foreach ($schemas as $schema)
            {
                // Get table name
                $query = $schema[0];
                $tableName = $schema[1];
                $tempTableName = self::TEMPORARY_TABLE_PREFIX . $tableName;

                // Rename table name in query to create temporary table
                $query = preg_replace(
                    sprintf("/(CREATE TABLE.*?)%s/", $tableName),
                    "$1" . $tempTableName,
                    $query
                );

                // Create temporary table
                $db->exec($query);

                // Define schemas
                $currentTable = $schemaManager->listTableDetails($tableName);
                $definedTable = $schemaManager->listTableDetails($tempTableName);
                $currentSchema = new Schema([$currentTable]);
                $definedSchema = new Schema([$definedTable]);
                $definedSchema->renameTable($tempTableName, $tableName);

                // Compare tables
                $comparator = new Comparator();
                $schemaDiff = $comparator->compare($currentSchema, $definedSchema);

                // Remove fields
                $tableDiff = new TableDiff($tableName);
                foreach ($schemaDiff->changedTables as $key => $currentSchema) {
                    if ($currentSchema->name == $tableName) {
                        $tableDiff->removedColumns = $currentSchema->removedColumns;
                    }
                }
                $schemaDiff = new SchemaDiff();
                $schemaDiff->changedTables[$tableName] = $tableDiff;
                $sql = $schemaDiff->toSql($db->getDatabasePlatform());

                if ($sql) {
                    foreach ($sql as $query) {
                        $db->exec($query);
                    }
                }

                // Remove temporary table
                $schemaManager->dropTable($tempTableName);
            }
        }
    }

    /**
     * Migrate legacy SQLite database files to current MySQL database.
     *
     * @param array|string $files The database files to be used for migration
     * @throws InvalidFileException if no files are provided for migration
     * @throws FileNotFoundException if any of the specified files does not exist
     * @throws DBALException if any database connection cannot be established
     * @throws DatabaseException if connection to any database was not successful
     */
    public function migrate($files)
    {
        if (!$files) {
            throw new InvalidFileException(
                "No files provided for migration. You must at least provide one database file.",
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
        foreach ($files as $file)
        {
            // Get full path
            $path = realpath($file);

            if ($path === false) {
                throw new FileNotFoundException(
                    sprintf("The database file \"%s\" does not exist.", $path),
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
                    sprintf("Could not connect to database file \"%s\".", $path),
                    1546890846
                );
            }

            // Get database schema manager of current database
            $scm = $this->database->getSchemaManager();

            // Migrate data
            foreach($con->getSchemaManager()->listTables() as $table)
            {
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
                            sprintf("Failed to insert data from database file \"%s\".", $path),
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
     * Creates a database dump for the current database and returns the dumped contents. Note that this
     * method only returns the raw result of `mysqldump`. It does not save the contents to a file. This
     * needs to be done by your own after calling the method.
     *
     * @return string The dumped database contents
     */
    public function export(): string
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

        // Build command with parameters
        $command = ConsoleUtility::buildCommand($scriptName, $parameters);

        exec($command, $result);
        return implode("\n", $result);
    }

    /**
     * Get list of schema files.
     *
     * Returns an array including the file names of available schema files. The search mode for schema files can be
     * modified by providing a list of controller class names. If set, only the appropriate schema files will be returned.
     *
     * @param array|string $controllers Name of one or more API controllers which will be used to identity the schema file
     * @return array List of schema files
     */
    protected function getListOfSchemaFiles($controllers = ""): array
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
        } else if (($allFiles = glob(self::SCHEMA_PATH . "/" . self::SCHEMA_FILE_PATTERN)) !== false) {
            $files = $allFiles;
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
     * @param string $file Schema file
     * @param array|null $schemas Result of {@see preg_match_all} containing `CREATE TABLE` statements
     * @return int Number of `CREATE TABLE` statements inside schema file
     * @throws FileNotFoundException if a table schema file is not available
     */
    protected function readContentsOfSchemaFile(string $file, ?array &$schemas): int
    {
        $contents = @file_get_contents($file);
        if (!$contents) {
            throw new FileNotFoundException(
                sprintf("The schema file \"%s\" is not available.", $file),
                1546889136
            );
        }

        return preg_match_all("/CREATE TABLE(.*?)\(\n(?:.*?)\);/ims", $contents, $schemas, PREG_SET_ORDER);
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
}
