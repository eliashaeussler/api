<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Command;

use EliasHaeussler\Api\Service\LogService;
use EliasHaeussler\Api\Utility\LocalizationUtility;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Log file clear console command.
 *
 * This command allows removing old log files.
 *
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
 */
class LogfileClearCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        // Base configuration
        $this->setName("logfile:clear")
            ->setDescription(LocalizationUtility::localize("logfile.clear.description", "console"))
            ->setHelp(LocalizationUtility::localize("logfile.clear.help", "console"));

        // Options
        $this->addOption(
            "keep-current",
            null,
            InputOption::VALUE_NONE,
            LocalizationUtility::localize("logfile.clear.option_keep-current", "console")
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Clear log files
        $result = LogService::clearLogFiles($input->getOption("keep-current"));

        // Show result messages
        if ($result) {
            foreach ($result as $log_file => $state) {
                if ($state) {
                    $this->io->success(
                        LocalizationUtility::localize("logfile.clear.success_file", "console", null, $log_file)
                    );
                } else {
                    $this->io->warning(
                        LocalizationUtility::localize("logfile.clear.warning_file", "console", null, $log_file)
                    );
                }
            }
        } else {
            $this->io->success(LocalizationUtility::localize("logfile.clear.noFilesRemoved", "console"));
        }
    }
}
