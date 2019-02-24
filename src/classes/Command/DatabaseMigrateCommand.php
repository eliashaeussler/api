<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Command;

use Doctrine\DBAL\DBALException;
use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Exception\DatabaseException;
use EliasHaeussler\Api\Exception\FileNotFoundException;
use EliasHaeussler\Api\Exception\InvalidFileException;
use EliasHaeussler\Api\Service\ConnectionService;
use EliasHaeussler\Api\Utility\GeneralUtility;
use EliasHaeussler\Api\Utility\LocalizationUtility;
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
class DatabaseMigrateCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        // Base configuration
        $this->setName("database:migrate")
            ->setDescription(LocalizationUtility::localize("database.migrate.description", "console"))
            ->setHelp(LocalizationUtility::localize("database.migrate.help", "console"));

        // Arguments
        $this->addArgument(
            "file",
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            LocalizationUtility::localize("database.migrate.argument_file", "console")
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws ClassNotFoundException if the {@see ConnectionService} class is not available
     * @throws InvalidFileException if no files are provided for migration
     * @throws FileNotFoundException if any of the specified files does not exist
     * @throws DBALException if any database connection cannot be established
     * @throws DatabaseException if connection to any database was not successful
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Update database schema
        /** @var ConnectionService $connectionService */
        $connectionService = GeneralUtility::makeInstance(ConnectionService::class);
        $connectionService->migrate($input->getArgument("file"));

        // Show success message
        $this->io->success(LocalizationUtility::localize("database.migrate.success", "console"));
    }
}
