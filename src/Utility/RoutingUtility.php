<?php
/**
 * Copyright (c) 2018 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Utility;

use Dotenv\Dotenv;
use EliasHaeussler\Api\Controller\BaseController;
use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Exception\EmptyControllerException;
use EliasHaeussler\Api\Exception\EmptyParametersException;
use EliasHaeussler\Api\Exception\InvalidControllerException;

/**
 * @todo add documentation here
 *
 * @package EliasHaeussler\Api\Utility
 * @author Elias Häußler <mail@elias-haeussler.de>
 */
class RoutingUtility
{
    /** @var int CLI as access device type of current request */
    const ACCESS_TYPE_CLI = 0;

    /** @var int Browser as access device type of current request */
    const ACCESS_TYPE_BROWSER = 1;

    /**
     * @var int Current access type
     */
    protected static $access = self::ACCESS_TYPE_BROWSER;

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
    protected $parameters;

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
     * @throws EmptyParametersException
     */
    public function __construct()
    {
        $this->loadEnvironment();
        $this->getRequest();
        $this->initializeController();
    }

    /**
     * @todo add doc
     */
    protected function loadEnvironment()
    {
        $loader = new Dotenv(ROOT_PATH);
        $loader->load();
    }

    /**
     * @todo documentation needed
     *
     * @throws EmptyControllerException
     * @throws EmptyParametersException
     * @internal
     */
    protected function getRequest()
    {
        $plainUri = strtok($_SERVER['REQUEST_URI'], "?");
        $uriComponents = GeneralUtility::trimExplode('/', $plainUri);
        $this->uri = implode('/', $uriComponents);

        if (empty($uriComponents[0])) {
            throw new EmptyControllerException("No controller given. Please provide a valid controller.", 1543532177);
        } else {
            $this->namespace = $uriComponents[0];
        }

        if (empty($uriComponents[1])) {
            throw new EmptyParametersException(sprintf(
                "No controller parameters given. Please provide valid parameters for the controller \"%s\".",
                $this->namespace
            ));
        } else {
            $this->parameters = $uriComponents[1];
        }
    }

    /**
     * @todo documentation needed
     *
     * @throws EmptyControllerException
     * @throws InvalidControllerException
     * @throws ClassNotFoundException
     * @throws EmptyParametersException
     * @internal
     */
    protected function initializeController()
    {
        if (!$this->namespace) {
            throw new EmptyControllerException(
                "No controller given. Please provide a valid controller.",
                1543532177
            );
        }

        if (!$this->parameters) {
            throw new EmptyParametersException(sprintf(
                "No controller parameters given. Please provide valid parameters for the controller \"%s\".",
                $this->namespace
            ), 1544226733);
        }

        // Generate controller class name
        $controllerName = ucfirst($this->namespace) . "Controller";
        $controllerClass = "EliasHaeussler\\Api\\Controller\\" . $controllerName;

        if (!class_exists($controllerClass)) {
            throw new InvalidControllerException(sprintf(
                "Requested controller \"%s\" could not be found.",
                $controllerName
            ), 1543532513);
        }

        $this->controller = GeneralUtility::makeInstance($controllerClass, $this->parameters);
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

    /**
     * @return int
     */
    public static function getAccess(): int
    {
        return self::$access;
    }

    /**
     * @param int $access
     */
    public static function setAccess(int $access)
    {
        self::$access = $access;
    }

    /**
     * @return BaseController|null
     */
    public function getController(): BaseController
    {
        return $this->controller;
    }

    /**
     * @param BaseController|null $controller
     */
    public function setController(BaseController $controller)
    {
        $this->controller = $controller;
    }
}
