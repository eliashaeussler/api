<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Command;

use Doctrine\DBAL\DBALException;
use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Exception\FileNotFoundException;
use EliasHaeussler\Api\Service\ConnectionService;
use EliasHaeussler\Api\Utility\GeneralUtility;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Database schema console command.
 *
 * This command makes it possible to run several actions according database schema.
 *
 * @package EliasHaeussler\Api\Command
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
 */
class DatabaseSchemaCommand extends BaseCommand
{
    /** @var string Update command action */
    const ACTION_UPDATE = "update";

    /** @var string Drop command action */
    const ACTION_DROP = "drop";

    /** @var array Available command actions */
    const AVAILABLE_ACTIONS = [
        self::ACTION_UPDATE,
        self::ACTION_DROP,
    ];


    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        // Base configuration
        $this->setName("database:schema")
            ->setDescription("Modify database schema")
            ->setHelp("This command allows you to maintain the database schema.");

        // Arguments
        $this->addArgument(
            "action",
            InputArgument::REQUIRED,
            sprintf("Action, can be one of `%s`.",  implode("`, `", self::AVAILABLE_ACTIONS))
        );

        // Options
        $this->addOption(
            "schema",
            "s",
            InputOption::VALUE_OPTIONAL,
            "Database schema to be used for updating the schema or dropping unused components"
        );
        $this->addOption(
            "force",
            null,
            InputOption::VALUE_NONE,
            sprintf("Force dropping of unused database components when using `%s` action", self::ACTION_DROP)
        );
        $this->addOption(
            "fields",
            null,
            InputOption::VALUE_OPTIONAL,
            sprintf("Explicitly drop unused database fields when using `%s` action", self::ACTION_DROP),
            false
        );
        $this->addOption(
            "tables",
            null,
            InputOption::VALUE_OPTIONAL,
            sprintf("Explicitly drop unused database tables when using `%s` action", self::ACTION_DROP),
            false
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws ClassNotFoundException if the {@see ConnectionService} class is not available
     * @throws DBALException if the database connection cannot be established
     * @throws FileNotFoundException if a table schema file is not available
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ConnectionService $connectionService */
        $connectionService = GeneralUtility::makeInstance(ConnectionService::class);

        switch ($input->getArgument("action")) {

            //
            // Update database schema
            //
            case self::ACTION_UPDATE:

                // Update database schema
                $connectionService->createSchema($input->getOption("schema"));

                // Show success message
                $this->io->success("Successfully updated database schemas.");
                break;

            //
            // Drop fields and/or tables in database schema
            //
            case self::ACTION_DROP:

                // Check which database components should be dropped
                $dropFields = $input->getOption("fields") !== false;
                $dropTables = $input->getOption("tables") !== false;
                if (!$dropFields && !$dropTables) {
                    $dropFields = true;
                    $dropTables = true;
                }
                $dropComponentsString = implode(" ", array_filter([
                    $dropFields ? "fields" : "",
                    $dropFields && $dropTables ? "and" : "",
                    $dropTables ? "tables" : ""
                ]));

                // Ask to drop components for security reasons
                if (!$input->getOption("force")) {
                    $question = sprintf("Really drop unused database %s?", $dropComponentsString);
                    if (!$this->io->confirm($question, false)) {
                        return;
                    }
                }

                // Drop database components
                $connectionService->dropUnusedComponents($dropFields, $dropTables, $input->getOption("schema"));

                // Show success message
                $this->io->success(sprintf("Successfully dropped unused database %s.", $dropComponentsString));
                break;
        }
    }
}
