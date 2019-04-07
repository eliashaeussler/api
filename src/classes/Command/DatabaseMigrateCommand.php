<?php
/**
 * Copyright (c) 2019 Elias Häußler <elias@haeussler.dev>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Command;

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
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0+
 */
class DatabaseMigrateCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        // Base configuration
        $this->setName('database:migrate')
            ->setDescription(LocalizationUtility::localize('database.migrate.description', 'console'))
            ->setHelp(LocalizationUtility::localize('database.migrate.help', 'console'));

        // Arguments
        $this->addArgument(
            'file',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            LocalizationUtility::localize('database.migrate.argument_file', 'console')
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws ClassNotFoundException if the {@see ConnectionService} class is not available
     * @throws InvalidFileException   if no files are provided for migration
     * @throws FileNotFoundException  if any of the specified files does not exist
     * @throws DBALException          if any database connection cannot be established
     * @throws DatabaseException      if connection to any database was not successful
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Update database schema
        /** @var ConnectionService $connectionService */
        $connectionService = GeneralUtility::makeInstance(ConnectionService::class);
        $connectionService->migrate($input->getArgument('file'));

        // Show success message
        $this->io->success(LocalizationUtility::localize('database.migrate.success', 'console'));
    }
}
