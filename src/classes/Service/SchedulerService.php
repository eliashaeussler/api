<?php
/**
 * Copyright (c) 2019 Elias Häußler <elias@haeussler.dev>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Service;

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

use Doctrine\DBAL\ParameterType;
use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Exception\InvalidClassException;
use EliasHaeussler\Api\Exception\InvalidExecutionTimeException;
use EliasHaeussler\Api\Exception\InvalidMethodSignatureException;
use EliasHaeussler\Api\Task\AbstractTask;
use EliasHaeussler\Api\Utility\GeneralUtility;
use EliasHaeussler\Api\Utility\LocalizationUtility;

/**
 * Task scheduler service.
 *
 * This class provides a service to schedule and execute tasks. Tasks are working processes
 * which need to be executed at a given time in the future. If the execution time was met,
 * the scheduler service can be used to execute the tasks. Each task needs to extend the
 * {@see AbstractTask} class in order to serve as scheduler task.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0+
 */
class SchedulerService
{
    /** @var string Method used to execute a scheduled task */
    const TASK_METHOD = 'run';

    /**
     * Execute a scheduled task.
     *
     * Runs a scheduled task if it meets the requirements. These are the following:
     * - Task must extend the {@see AbstractTask} class
     * - Arguments must match the task method signature
     * - Execution time must be in the past or equal the current time
     *
     * @param string    $className     Name of the class to be used to execute the task
     * @param array     $arguments     Arguments which will be passed to the task method
     * @param \DateTime $executionTime Scheduled task execution time
     *
     * @throws \ReflectionException  if the task class or method does not exist
     * @throws InvalidClassException if the class for the scheduled task is not available
     *
     * @return bool `true` if the task was successfully executed, `false` otherwise
     */
    public static function executeTask(string $className, array $arguments, \DateTime $executionTime, bool $strict = true): bool
    {
        if (!class_exists($className) || !in_array(self::TASK_METHOD, get_class_methods($className))) {
            throw new InvalidClassException(
                LocalizationUtility::localize('exception.1555447804', 'sys', null, $className),
                1555447804
            );
        }

        if ($strict && $executionTime > new \DateTime()) {
            LogService::log(
                'Skipped execution of scheduled task, reason: Scheduled execution time was not reached.',
                LogService::WARNING
            );

            return false;
        }

        $reflectionMethod = new \ReflectionMethod($className, self::TASK_METHOD);
        $reflectionParameters = $reflectionMethod->getParameters();
        $sortedParameters = [];

        foreach ($reflectionParameters as $reflectionParameter) {
            $parameterName = $reflectionParameter->getName();
            if (array_key_exists($parameterName, $arguments)) {
                $sortedParameters[] = $arguments[$parameterName];
            } else {
                LogService::log(
                    sprintf('Parameter "%s" is missing for task "%s".', $parameterName, $className),
                    LogService::WARNING
                );

                $sortedParameters[] = $reflectionParameter->getDefaultValue();
            }
        }

        // Execute task
        $classObject = new $className();

        return $classObject->{self::TASK_METHOD}(...$sortedParameters);
    }

    /**
     * Schedule a specific task.
     *
     * Stores a specific task in the database in order to make them execute at the given
     * execution time. It's important that the task meets the requirements, such as
     * extending the {@see AbstractTask} class and serving the correct arguments needed for
     * executing the appropriate task method. Also, the execution time has to be in the
     * future.
     *
     * @param string    $className Name of the class to be used to execute the task
     * @param \DateTime $execution Scheduled task execution time
     * @param array     $arguments Arguments which will be passed to the task method
     *
     * @throws ClassNotFoundException          if the {@see ConnectionService} class is not available
     * @throws InvalidClassException           if the task class does not extend the {@see AbstractTask} class
     * @throws InvalidExecutionTimeException   if the scheduled execution time is not in the future
     * @throws InvalidMethodSignatureException if the number of arguments does not match the number of task method arguments
     * @throws \ReflectionException            if the task class does not exist
     * @throws \Exception                      if the {@see DateTime} object cannot be instantiated
     *
     * @return bool `true` if the task was successfully scheduled, `false` otherwise
     */
    public static function scheduleTask(string $className, \DateTime $execution, array $arguments = []): bool
    {
        $reflectionClass = new \ReflectionClass($className);
        $reflectionParameters = $reflectionClass->getMethod(self::TASK_METHOD)->getParameters();

        // Check if class implements task interface
        if (!$reflectionClass->isSubclassOf(AbstractTask::class)) {
            throw new InvalidClassException(
                LocalizationUtility::localize('exception.1555447804', 'sys', null, $className),
                1555447804
            );
        }

        // Check if given number of arguments matches the required number of arguments
        if (count($reflectionParameters) > count($arguments)) {
            throw new InvalidMethodSignatureException(
                LocalizationUtility::localize('exception.1555753795', 'sys', null, $className),
                1555753795
            );
        }
        if (count($reflectionParameters) < count($arguments)) {
            throw new InvalidMethodSignatureException(
                LocalizationUtility::localize('exception.1555753824', 'sys', null, $className),
                1555753824
            );
        }

        // Check if all required parameters are given
        array_walk($reflectionParameters, function (\ReflectionParameter &$parameter) {
            $parameter = $parameter->getName();
        });
        $filteredParameters = array_diff($reflectionParameters, array_keys($arguments));
        if (count($filteredParameters) > 0) {
            throw new \InvalidArgumentException(
                LocalizationUtility::localize('exception.1555448279', 'sys', null, current($filteredParameters), $className),
                1555448279
            );
        }

        // Check if execution time is scheduled for the future
        if ($execution < (new \DateTime('now', $execution->getTimezone()))) {
            throw new InvalidExecutionTimeException(
                LocalizationUtility::localize('exception.1555448628', 'sys'),
                1555448628
            );
        }

        return self::persistTaskForSchedule($className, $execution, $arguments);
    }

    /**
     * Get currently scheduled tasks.
     *
     * Returns tasks which are currently scheduled for execution. Depending on the provided arguments,
     * this method either returns all tasks or only tasks matching the given uid and/or the given
     * class name. The class name can either be a FQN or the full class name relative to the
     * `EliasHaeussler\Api\Task` namespace.
     *
     * @param string|null $className Class name (either FQN or task class name) whose tasks should be returned
     * @param int|null    $uid       Uid of a specific task to be returned
     * @param int         $limit     Maximum numbers of tasks to be executed at this iteration
     *
     * @throws ClassNotFoundException if the {@see ConnectionService} class is not available
     *
     * @return array Result set of the tasks which are scheduled for execution
     */
    public static function getScheduledTasks(?string $className = null, ?int $uid = null, int $limit = 20, bool $ignoreExecutionTime = false): array
    {
        if ($className && strpos($className, 'EliasHaeussler\\Api') !== 0) {
            $className = 'EliasHaeussler\\Api\\Task\\' . ltrim($className, '\\');
        }

        // Get database connection and query builder
        $db = GeneralUtility::makeInstance(ConnectionService::class)->getDatabase();
        $queryBuilder = $db->createQueryBuilder()
            ->select('*')
            ->from('sys_scheduled_tasks');

        // Set uid and class name constraints, if set
        if ($uid) {
            $queryBuilder->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, ParameterType::INTEGER))
            );
        }
        if ($className) {
            $classNameExpr = $queryBuilder->expr()->eq('task', $queryBuilder->createNamedParameter($className));
            if ($queryBuilder->getQueryPart('where')) {
                $queryBuilder->andWhere($classNameExpr);
            } else {
                $queryBuilder->where($classNameExpr);
            }
        }

        // Set execution time constraint
        if (!$ignoreExecutionTime) {
            $execTimeExpr = $queryBuilder->expr()->lte(
                'scheduled_execution', $queryBuilder->createNamedParameter(new \DateTime(), 'datetime')
            );
            if ($queryBuilder->getQueryPart('where')) {
                $queryBuilder->andWhere($execTimeExpr);
            } else {
                $queryBuilder->where($execTimeExpr);
            }
        }

        // Set status constraint
        $statusExpr = $queryBuilder->expr()->eq(
            'status', $queryBuilder->createNamedParameter(0, ParameterType::INTEGER)
        );
        if ($queryBuilder->getQueryPart('where')) {
            $queryBuilder->andWhere($statusExpr);
        } else {
            $queryBuilder->where($statusExpr);
        }

        // Set ordering and limitation
        $queryBuilder->orderBy('scheduled_execution');
        $queryBuilder->setMaxResults($limit);

        // Get scheduled tasks
        $tasks = $queryBuilder->execute()->fetchAll();

        // Convert parameters to their appropriate types
        return array_map(function ($task) {
            $task['uid'] = (int) $task['uid'];
            $task['arguments'] = unserialize($task['arguments']);
            $task['scheduled_execution'] = new \DateTime($task['scheduled_execution']);
            $task['last_execution_time'] = new \DateTime($task['last_execution_time']);
            $task['status'] = (int) $task['status'];

            return $task;
        }, $tasks);
    }

    /**
     * Finalize executed task in database.
     *
     * Writes the result of a executed task to the database.
     *
     * @param int  $uid             Uid of the executed task
     * @param bool $executionResult Result of the executed task
     *
     * @throws ClassNotFoundException if the {@see ConnectionService} class is not available
     */
    public static function finalizeExecution(int $uid, bool $executionResult): void
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionService::class)->getDatabase()->createQueryBuilder();
        $queryBuilder->update('sys_scheduled_tasks')
            ->set('status', $queryBuilder->createNamedParameter($executionResult, ParameterType::BOOLEAN))
            ->set('last_execution_time', $queryBuilder->createNamedParameter(new \DateTime(), 'datetime'))
            ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, ParameterType::INTEGER)))
            ->execute();
    }

    /**
     * Cancel scheduled tasks.
     *
     * Cancels scheduled tasks by class name and optional argument constraints. Only tasks which haven't been
     * executed will be canceled.
     *
     * @param string $className           Class name of the tasks to be canceled
     * @param array  $argumentConstraints Optional argument constraints, will be passed as serialized value to the database
     *
     * @throws ClassNotFoundException if the {@see ConnectionService} class is not available
     */
    public static function cancelScheduledTasks(string $className, array $argumentConstraints = []): void
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionService::class)->getDatabase()->createQueryBuilder();

        // Serialize arguments in order to compare them within SQL
        if ($argumentConstraints) {
            array_walk($argumentConstraints, function (&$value, $key) use ($queryBuilder) {
                $value = $queryBuilder->expr()->like(
                    'arguments',
                    $queryBuilder->createNamedParameter('%' . serialize($key) . serialize($value) . '%')
                );
            });
        }

        // Cancel scheduled tasks in database
        $queryBuilder->update('sys_scheduled_tasks')
            ->set('status', $queryBuilder->createNamedParameter(-1, ParameterType::INTEGER))
            ->where($queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq('task', $queryBuilder->createNamedParameter($className)),
                $queryBuilder->expr()->eq('status', $queryBuilder->createNamedParameter(0, ParameterType::INTEGER)),
                ...array_values($argumentConstraints)
            ))
            ->execute();
    }

    /**
     * Log message for a given task.
     *
     * Appends the log of a given task with the given log message.
     *
     * @param int    $taskUid Uid of the task whose log should be appended by the given log message
     * @param string $message Log message to be appended to the log of the given task
     *
     * @throws ClassNotFoundException if the {@see ConnectionService} class is not available
     */
    public static function log(int $taskUid, string $message): void
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionService::class)->getDatabase()->createQueryBuilder();
        $queryBuilder->update('sys_scheduled_tasks')
            ->set('log', 'CONCAT(log, :message)')
            ->where($queryBuilder->expr()->eq('uid', ':uid'))
            ->setParameter('message', $message)
            ->setParameter('uid', $taskUid, ParameterType::INTEGER)
            ->execute();
    }

    /**
     * Persist a scheduled task in the database.
     *
     * Writes a scheduled task to the database.
     *
     * @param string    $className Name of the task class, must extend the {@see AbstractTask} class
     * @param \DateTime $execution Time of scheduled execution
     * @param array     $arguments Arguments passed to the task method
     *
     * @throws ClassNotFoundException if the {@see ConnectionService} class is not available
     *
     * @return bool `true` if persistence was successful, `false` otherwise
     */
    protected static function persistTaskForSchedule(string $className, \DateTime $execution, array $arguments): bool
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionService::class)->getDatabase()->createQueryBuilder();

        return $queryBuilder->insert('sys_scheduled_tasks')
            ->values([
                'task' => $queryBuilder->createNamedParameter($className),
                'arguments' => $queryBuilder->createNamedParameter($arguments, 'array'),
                'scheduled_execution' => $queryBuilder->createNamedParameter($execution, 'datetime'),
            ])
            ->execute() > 0;
    }
}
