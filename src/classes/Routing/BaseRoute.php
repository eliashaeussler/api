<?php
/**
 * Copyright (c) 2019 Elias Häußler <elias@haeussler.dev>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Routing;

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
use EliasHaeussler\Api\Service\LogService;

/**
 * Base request router.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0+
 */
abstract class BaseRoute
{
    /** @var BaseController Controller */
    protected $controller;

    /** @var array Data to be send in request */
    protected $requestData;

    /**
     * Initialize request router for current API controller.
     *
     * Initializes the router for the current API request with the appropriate API controller. This includes
     * processing the concrete initialization by calling {@see initializeRequest()}.
     *
     * @param BaseController $controller API controller for current route
     */
    final public function __construct(BaseController $controller)
    {
        $this->controller = $controller;

        LogService::log(sprintf('Initializing request for route "%s"', static::class), LogService::DEBUG);

        $this->initializeRequest();
    }

    /**
     * Process routing of API request.
     *
     * This method processes the concrete API request and prints the result of it.
     */
    abstract public function processRequest();

    /**
     * Initialize routing for API request.
     *
     * Defines necessary variables for the routing of the API request and ensures that it can be processed without errors.
     */
    abstract protected function initializeRequest();
}
