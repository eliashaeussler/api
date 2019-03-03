<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Routing;

use EliasHaeussler\Api\Controller\BaseController;
use EliasHaeussler\Api\Service\LogService;

/**
 * Base request router.
 *
 * @package EliasHaeussler\Api\Routing
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
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

        LogService::log(sprintf("Initializing request for route \"%s\"", static::class), LogService::DEBUG);

        $this->initializeRequest();
    }

    /**
     * Initialize routing for API request.
     *
     * Defines necessary variables for the routing of the API request and ensures that it can be processed without errors.
     */
    abstract protected function initializeRequest();

    /**
     * Process routing of API request.
     *
     * This method processes the concrete API request and prints the result of it.
     */
    abstract public function processRequest();
}
