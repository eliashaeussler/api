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
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Database migrate console command.
 *
 * This command makes it possible to run several actions according database migration.
 *
 * @package EliasHaeussler\Api\Command
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
 */
class DatabaseMigrateCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        // Base configuration
        $this->setName("database:migrate")
            ->setDescription("Migrate SQLite to MySQL database")
            ->setHelp("This command allows you to migrate legacy SQLite databases to the new MySQL database.");

        // Arguments
        $this->addArgument(
            "file",
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            "SQLite database files (relative to current path)"
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {

            // Update database schema
            /** @var ConnectionService $connectionService */
            $connectionService = GeneralUtility::makeInstance(ConnectionService::class);
            $connectionService->migrate($input->getArgument("file"));

            // Show success message
            $output->write("<info>");
            $output->writeln([
                "Database successfully migrated.",
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
