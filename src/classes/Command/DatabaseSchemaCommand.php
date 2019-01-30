<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Command;

use Doctrine\DBAL\DBALException;
use EliasHaeussler\Api\Service\ConnectionService;
use EliasHaeussler\Api\Utility\GeneralUtility;
use Symfony\Component\Console\Command\Command;
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
class DatabaseSchemaCommand extends Command
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
            "Database schema to be updated"
        );
        $this->addOption(
            "fields",
            null,
            InputOption::VALUE_OPTIONAL,
            sprintf("Define fields to drop when using `%s` action", self::ACTION_DROP),
            false
        );
        $this->addOption(
            "tables",
            null,
            InputOption::VALUE_OPTIONAL,
            sprintf("Define tables to drop when using `%s` action", self::ACTION_DROP),
            false
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            /** @var ConnectionService $connectionService */
            $connectionService = GeneralUtility::makeInstance(ConnectionService::class);

            switch ($input->getArgument("action")) {

                //
                // Update database schema
                //
                case self::ACTION_UPDATE:

                    // Update database schema
                    $connectionService->createSchema($input->getOption("schema") ?: "");

                    // Show success message
                    $output->write("<info>");
                    $output->writeln([
                        "Database schemas updated successfully.",
                    ]);
                    $output->write("</info>");
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

                    // Drop database components
                    $connectionService->dropUnusedComponents($dropFields, $dropTables);
                    break;
            }
        } catch (DBALException $e) {
            $output->write("<error>");
            $output->writeln([
                "There was a problem with the database connection:",
                $e->getMessage(),
                $e->getTraceAsString(),
            ]);
            $output->write("</error>");

        } catch (\Exception $e) {
            $output->write("<error>");
            $output->writeln([
                "There was a problem during the command execution:",
                $e->getMessage(),
            ]);
            $output->write("</error>");
        }
    }
}
