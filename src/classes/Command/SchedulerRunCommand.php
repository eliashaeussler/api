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
use EliasHaeussler\Api\Exception\InvalidClassException;
use EliasHaeussler\Api\Exception\MissingParameterException;
use EliasHaeussler\Api\Service\SchedulerService;
use EliasHaeussler\Api\Utility\LocalizationUtility;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Run scheduler console command.
 *
 * This command executes scheduled tasks.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0+
 */
class SchedulerRunCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        // Base configuration
        $this->setName('scheduler:run')
            ->setDescription(LocalizationUtility::localize('scheduler.run.description', 'console'))
            ->setHelp(LocalizationUtility::localize('scheduler.run.help', 'console'));

        // Options
        $this->addOption(
            'task',
            't',
            InputOption::VALUE_OPTIONAL,
            LocalizationUtility::localize('scheduler.run.option_task', 'console')
        );
        $this->addOption(
            'uid',
            'u',
            InputOption::VALUE_OPTIONAL,
            LocalizationUtility::localize('scheduler.run.option_uid', 'console')
        );
        $this->addOption(
            'limit',
            'l',
            InputOption::VALUE_OPTIONAL,
            LocalizationUtility::localize('scheduler.run.option_limit', 'console'),
            20
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws ClassNotFoundException    if the {@see ConnectionService} class is not available
     * @throws InvalidClassException     if the class for the scheduled task is not available
     * @throws MissingParameterException if a necessary method parameter was not registered within the task
     * @throws \ReflectionException      if the task class or method does not exist
     * @throws \Exception                if the {@see DateTime} object cannot be instantiated
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limit = (int) $input->getOption('limit');

        if ($limit < 1) {
            $this->io->error(
                LocalizationUtility::localize('scheduler.run.error_noLimit', 'console')
            );

            return;
        }

        $tasks = SchedulerService::getScheduledTasks(
            (int) $input->getOption('uid'),
            $input->getOption('task'),
            $limit
        );

        if (count($tasks) > 0) {
            $successfulTasks = [];
            $failedTasks = [];

            // Execute tasks
            foreach ($tasks as $task) {
                $result = SchedulerService::executeTask(
                    $task['task'],
                    unserialize($task['arguments']),
                    new \DateTime($task['scheduled_execution'])
                );

                if ($result) {
                    $successfulTasks[] = $task;
                } else {
                    $failedTasks[] = $task;
                }

                SchedulerService::finalizeExecution((int) $task['uid'], $result);
            }

            // Show success or error messages and list executed tasks
            if (count($successfulTasks) + count($failedTasks) > 0) {
                if (count($successfulTasks) > 0) {
                    $resultSet = array_map(function ($task) {
                        /** @noinspection RequiredAttributes */
                        return sprintf('%s [<param>%s</>]', $task['task'], $task['uid']);
                    }, $successfulTasks);

                    $this->io->title(LocalizationUtility::localize('scheduler.run.successfulTasks_title', 'console'));
                    $this->io->listing($resultSet);
                }

                if (count($failedTasks) > 0) {
                    $resultSet = array_map(function ($task) {
                        /** @noinspection RequiredAttributes */
                        return sprintf('%s [<param>%s</>]', $task['task'], $task['uid']);
                    }, $failedTasks);

                    $this->io->title(LocalizationUtility::localize('scheduler.run.failedTasks_title', 'console'));
                    $this->io->listing($resultSet);

                    $this->io->error(LocalizationUtility::localize('scheduler.run.error', 'console'));
                } else {
                    $this->io->success(
                        LocalizationUtility::localize('scheduler.run.success', 'console', null, count($successfulTasks))
                    );
                }
            }
        } else {
            $this->io->success(LocalizationUtility::localize('scheduler.run.noTasksFound', 'console'));
        }
    }
}
