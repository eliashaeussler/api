<?php
/**
 * Copyright (c) 2018 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */

namespace EliasHaeussler\Api\Routing;

use EliasHaeussler\Api\Controller\BaseController;
use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Exception\EmptyControllerException;
use EliasHaeussler\Api\Exception\EmptyFunctionException;
use EliasHaeussler\Api\Exception\InvalidControllerException;
use EliasHaeussler\Api\Utility\GeneralUtility;

/**
 * @todo add documentation here
 *
 * @package EliasHaeussler\Api
 * @author Elias Häußler <mail@elias-haeussler.de>
 */
class PageRouter
{
    /**
     * @var string
     */
    protected $uri;

    /**
     * @var string|null
     */
    protected $namespace;

    /**
     * @var string|null
     */
    protected $function;

    /**
     * @var BaseController|null
     */
    protected $controller;


    /**
     * @todo documentation needed
     *
     * @throws ClassNotFoundException
     * @throws EmptyControllerException
     * @throws InvalidControllerException
     * @throws EmptyFunctionException
     */
    public function __construct()
    {
        $this->getRequest();
        $this->mapController();
    }

    /**
     * @todo documentation needed
     *
     * @throws EmptyControllerException
     * @throws EmptyFunctionException
     * @internal
     */
    protected function getRequest()
    {
        $uriComponents = GeneralUtility::trimExplode('/', $_SERVER['REQUEST_URI']);
        $this->uri = implode('/', $uriComponents);

        if (empty($uriComponents[0])) {
            throw new EmptyControllerException("No controller given. Please provide a valid controller.", 1543532177);
        } else {
            $this->namespace = $uriComponents[0];
        }

        if (empty($uriComponents[1])) {
            throw new EmptyFunctionException(sprintf(
                "No controller function given. Please provide a valid function for the controller \"%s\".",
                $this->namespace
            ));
        } else {
            $this->function = $uriComponents[1];
        }
    }

    /**
     * @todo documentation needed
     *
     * @throws EmptyControllerException
     * @throws InvalidControllerException
     * @throws ClassNotFoundException
     * @throws EmptyFunctionException
     * @internal
     */
    protected function mapController()
    {
        if (!$this->namespace) {
            throw new EmptyControllerException("No controller given. Please provide a valid controller.", 1543532177);
        }
        if (!$this->function) {
            throw new EmptyFunctionException(sprintf(
                "No controller function given. Please provide a valid function for the controller \"%s\".",
                $this->namespace
            ));
        }

        // Generate controller class name
        $controllerName = ucfirst($this->namespace) . "Controller";
        $controllerClass = "EliasHaeussler\\Api\\Controller\\" . $controllerName;

        if (!class_exists($controllerClass)) {
            throw new InvalidControllerException(sprintf("No controller \"%s\" could be found.", $controllerName), 1543532513);
        }

        $this->controller = GeneralUtility::makeInstance($controllerClass, $this->function);
    }

    /**
     * @todo add doc
     */
    public function route()
    {
        if ($this->controller) {
            $this->controller->call();
        }
    }
}
