<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Command;

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
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
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
