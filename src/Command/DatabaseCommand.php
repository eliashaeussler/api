<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Command;

use Doctrine\DBAL\DBALException;
use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Service\ConnectionService;
use EliasHaeussler\Api\Utility\GeneralUtility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Database console command.
 *
 * This command makes it possible to run several database actions including updating the database schemas.
 *
 * @package EliasHaeussler\Api\Command
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
 */
class DatabaseCommand extends Command
{
    /** @var array Available command actions */
    const AVAILABLE_ACTIONS = [
        "schema:update",
    ];


    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        // Base configuration
        $this->setName("database")
            ->setDescription("Execute database commands")
            ->setHelp("This command allows you to maintain the database by executing necessary commands.");

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
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        switch ($input->getArgument("action")) {

            //
            // Update database schema
            //
            case "schema:update":
                try {

                    // Update database schema
                    /** @var ConnectionService $connectionService */
                    $connectionService = GeneralUtility::makeInstance(ConnectionService::class);
                    $connectionService->createSchema($input->getOption("schema") ?: "");

                    // Show success message
                    $output->write("<info>");
                    $output->writeln([
                        "Database schemas updated successfully.",
                    ]);
                    $output->write("</info>");

                } catch (DBALException $e) {

                    $output->write("<error>");
                    $output->writeln([
                        "There was a problem with the database connection:",
                        $e->getMessage(),
                        $e->getTraceAsString(),
                    ]);
                    $output->write("</error>");

                } catch (ClassNotFoundException $e) {

                    $output->write("<error>");
                    $output->writeln([
                        "There was a problem initializing the Connection service class:",
                        $e->getMessage(),
                    ]);
                    $output->write("</error>");

                }
                break;
        }
    }
}
