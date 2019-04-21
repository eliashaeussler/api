<?php
/**
 * Copyright (c) 2019 Elias Häußler <elias@haeussler.dev>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Task;

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

use EliasHaeussler\Api\Controller\BaseController;
use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Exception\InvalidControllerException;
use EliasHaeussler\Api\Service\SchedulerService;
use EliasHaeussler\Api\Utility\GeneralUtility;
use EliasHaeussler\Api\Utility\LocalizationUtility;

/**
 * Abstract scheduler task class.
 *
 * Abstract class of a scheduler task which can be used to execute specific functions related to a controller at a given
 * time in the future. Each scheduler task must extend this class and can then be used together with the
 * {@see SchedulerService} class for scheduling and executing the tasks.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0+
 */
abstract class AbstractTask
{
    /** @var BaseController Controller to be used for executing tasks */
    protected $controller;

    /** @var string Class name of the appropriate controller */
    protected $controllerClassName = BaseController::class;

    /**
     * Initialize task.
     *
     * Initializes the current task by injecting the appropriate controller. This means, in fact, that the
     * controller on which the task is executed is being instantiated with a pre-defined CLI route. This
     * way, controllers are able to handle CLI requests differently than default requests.
     *
     * @throws ClassNotFoundException     if the controller class is not available
     * @throws \ReflectionException       if the controller class is not available
     * @throws InvalidControllerException if the controller class could not be verified
     */
    final public function __construct()
    {
        if ($this->verifyController()) {
            $this->controller = GeneralUtility::makeInstance($this->controllerClassName, BaseController::ROUTE_CLI);
        } else {
            throw new InvalidControllerException(
                LocalizationUtility::localize(
                    'exception.1555877278',
                    'sys',
                    null,
                    $this->controllerClassName,
                    static::class
                ),
                1555877278
            );
        }
    }

    /**
     * Run a scheduled task.
     *
     * Executes a scheduled task at a given time of execution.
     *
     * @return bool `true` if the execution was successful, `false` otherwise
     */
    abstract public function run(): bool;

    /**
     * Verify declared controller class name.
     *
     * Checks if the declared controller class name can be verified. This means, in fact, that the controller
     * must be set and should extend the {@see BaseController} class.
     *
     * @throws \ReflectionException if the controller class is not available
     *
     * @return bool `true` if the controller class could be verified, `false` otherwise
     */
    final protected function verifyController(): bool
    {
        if (empty($this->controllerClassName)) {
            return false;
        }

        $reflectionClass = new \ReflectionClass($this->controllerClassName);

        return $reflectionClass->isSubclassOf(BaseController::class);
    }
}
