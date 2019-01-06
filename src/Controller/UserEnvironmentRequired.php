<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Controller;

use EliasHaeussler\Api\Utility\GeneralUtility;

/**
 * Set requirement of user environment for specific API controllers.
 *
 * This trait can be used to load the user environment for a specific API controller. When using it, the environment
 * will be initialized right before the request is handled.
 *
 * @package EliasHaeussler\Api\Controller
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
 */
trait UserEnvironmentRequired
{
    /**
     * {@inheritdoc}
     */
    protected function initializeEnvironment()
    {
        $envFile = sprintf("%s.env", GeneralUtility::getControllerName(static::class));
        GeneralUtility::loadEnvironment($envFile);
    }
}
