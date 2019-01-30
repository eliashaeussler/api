<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Command;

use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Service\ConnectionService;
use EliasHaeussler\Api\Utility\GeneralUtility;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Database export console command.
 *
 * This command makes it possible to export the database.
 *
 * @package EliasHaeussler\Api\Command
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
            ->setDescription("Export the database")
            ->setHelp("This command allows you to export the currently used database.");
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
        $exportedSql = $connectionService->export();

        // Print dump to console
        $this->io->text($exportedSql);
    }
}
