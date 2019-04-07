<?php
/**
 * Copyright (c) 2019 Elias Häußler <elias@haeussler.dev>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Controller;

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

use EliasHaeussler\Api\Utility\GeneralUtility;

/**
 * Set requirement of user environment for specific API controllers.
 *
 * This trait can be used to load the user environment for a specific API controller. When using it, the environment
 * will be initialized right before the request is handled.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0+
 */
trait UserEnvironmentRequired
{
    /**
     * {@inheritdoc}
     */
    protected function initializeEnvironment()
    {
        $envFile = strtolower(sprintf("%s.env", GeneralUtility::getControllerName(static::class)));
        GeneralUtility::loadEnvironment($envFile);
    }
}
