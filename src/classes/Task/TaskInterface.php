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

use EliasHaeussler\Api\Service\SchedulerService;

/**
 * Scheduler task interface.
 *
 * Interface of a scheduler task which can be used to execute specific functions related to a controller at a given
 * time in the future. Each scheduler task must implement this interface and can then be used together with the
 * {@see SchedulerService} class for scheduling and executing the tasks.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0+
 */
interface TaskInterface
{
    /**
     * Run a scheduled task.
     *
     * Executes a scheduled task at a given time of execution.
     *
     * @return bool `true` if the execution was successful, `false` otherwise
     */
    public static function run(): bool;
}
