<?php
/**
 * Copyright (c) 2018 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Routing;

use EliasHaeussler\Api\Controller\BaseController;

/**
 * @todo add doc
 *
 * @package EliasHaeussler\Api\Routing
 * @author Elias Häußler <mail@elias-haeussler.de>
 */
abstract class BaseRoute
{
    /**
     * @var BaseController Controller
     */
    protected $controller;

    /**
     * @var array Data to be send in request
     */
    protected $requestData;


    /**
     * @todo add doc
     *
     * @param $controller
     */
    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @todo add doc
     */
    abstract public function processRequest();
}
