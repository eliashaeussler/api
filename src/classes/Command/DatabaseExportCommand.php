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

use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Service\ConnectionService;
use EliasHaeussler\Api\Utility\GeneralUtility;
use EliasHaeussler\Api\Utility\LocalizationUtility;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Database export console command.
 *
 * This command makes it possible to export the database.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0+
 */
class DatabaseExportCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        // Base configuration
        $this->setName("database:export")
            ->setDescription(LocalizationUtility::localize("database.export.description", "console"))
            ->setHelp(LocalizationUtility::localize("database.export.help", "console"));
    }

    /**
     * {@inheritdoc}
     *
     * @throws ClassNotFoundException if the {@see ConnectionService} class is not available
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Create database dump
        /** @var ConnectionService $connectionService */
        $connectionService = GeneralUtility::makeInstance(ConnectionService::class);
        $connectionService->export();
    }
}
