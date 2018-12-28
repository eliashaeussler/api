<?php
/**
 * Copyright (c) 2018 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Utility;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Dotenv\Dotenv;
use EliasHaeussler\Api\Controller\BaseController;
use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Exception\EmptyControllerException;
use EliasHaeussler\Api\Exception\EmptyParametersException;
use EliasHaeussler\Api\Exception\InvalidControllerException;

/**
 * API request routing utility.
 *
 * This class controls the routing of each API request. It serves as entry point and handles all API requests. Each
 * request will be analyzed and a concrete API controller will be initialized, if available. After that, the API
 * request will be processed within the API controller.
 *
 * @package EliasHaeussler\Api\Utility
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
 */
class RoutingUtility
{
    /** @var int Bot as access device type of current request */
    const ACCESS_TYPE_BOT = 0;

    /** @var int Browser as access device type of current request */
    const ACCESS_TYPE_BROWSER = 1;

    /** @var int Current access type */
    protected static $access = self::ACCESS_TYPE_BROWSER;

    /** @var Connection Database connection */
    protected $database;

    /** @var string Plain request URI without query string */
    protected $uri;

    /** @var string Namespace of request (first part of URI) */
    protected $namespace;

    /** @var string Parameters of request (second part of URI) */
    protected $parameters;

    /** @var BaseController API Controller instance for request */
    protected $controller;


    /**
     * Initialize routing.
     *
     * Analyzes the current request by loading the environment, reading the request URI and initializing the concrete
     * API controller class. If the URI does not provide any API controller or controller parameters, initializing an
     * instance of this class will throw an error. This will also be the case if the provided API controller cannot be
     * resolved to a concrete API controller class.
     *
     * @throws ClassNotFoundException if either the `Database` class or API controller class is not available
     * @throws DBALException if the database connection cannot be established
     * @throws EmptyControllerException if no API controller has been provided
     * @throws EmptyParametersException if not API controller parameters have been provided
     * @throws InvalidControllerException if the requested API controller class could not be found
     */
    public function __construct()
    {
        $this->loadEnvironment();
        $this->initializeDatabase();
        $this->analyzeRequestUri();
        $this->initializeController();
    }

    /**
     * Load API environment.
     *
     * Reads the environment variables of the current environment in order to use them in the API request.
     */
    protected function loadEnvironment()
    {
        $loader = new Dotenv(ROOT_PATH);
        $loader->load();
    }

    /**
     * Initialize database connection.
     *
     * @throws DBALException if the database connection cannot be established
     */
    protected function initializeDatabase()
    {
        $parameters = [
            "host" => GeneralUtility::getEnvironmentVariable("DB_HOST", "localhost"),
            "user" => GeneralUtility::getEnvironmentVariable("DB_USER"),
            "password" => GeneralUtility::getEnvironmentVariable("DB_PASS"),
            "dbname" => GeneralUtility::getEnvironmentVariable("DB_NAME"),
            "port" => (int) GeneralUtility::getEnvironmentVariable("DB_PORT", 3306),
            "driver" => GeneralUtility::getEnvironmentVariable("DB_DRIVER", "pdo_mysql"),
        ];

        $this->database = DriverManager::getConnection($parameters);
    }

    /**
     * Analyze API request URI.
     *
     * Analyzes the request URI of the current API request by splitting them into URI parts and defining controller
     * and controller parameters based on the request URI. The first part of the request URI should always match the
     * API controller class name (case insensitive) while the second part should provide controller parameters. This
     * method will throw exceptions if at least one of these components are not set.
     *
     * @throws EmptyControllerException if no API controller has been provided
     * @throws EmptyParametersException if not API controller parameters have been provided
     */
    protected function analyzeRequestUri()
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
     * Initialize API controller class.
     *
     * Creates an instance of the API controller class, if available, and stores them in the class instance.
     *
     * @throws ClassNotFoundException if the API controller class is not available
     * @throws InvalidControllerException if the requested API controller class could not be found
     */
    protected function initializeController()
    {
        // Generate controller class name
        $controllerName = ucfirst($this->namespace) . "Controller";
        $controllerClass = "EliasHaeussler\\Api\\Controller\\" . $controllerName;

        // Check if controller class is available
        if (!class_exists($controllerClass)) {
            throw new InvalidControllerException(sprintf(
                "Requested controller \"%s\" could not be found.",
                $controllerName
            ), 1543532513);
        }

        $this->controller = GeneralUtility::makeInstance($controllerClass, $this->parameters);
    }

    /**
     * Route current request through the API controller to the routing class.
     *
     * This method calls the API controller class to route the current request to the concrete routing class where the
     * request can be processed.
     *
     * @throws ClassNotFoundException if the routing class is not available
     */
    public function route()
    {
        if ($this->controller) {
            $this->controller->call();
        }
    }

    /**
     * Get database connection
     *
     * @return Connection Database connection
     */
    public function getDatabase(): Connection
    {
        return $this->database;
    }

    /**
     * Get current access type.
     *
     * @return int Current access type
     */
    public static function getAccess(): int
    {
        return self::$access;
    }

    /**
     * Set current access type.
     *
     * @param int $access Access type to be set as current access type
     */
    public static function setAccess(int $access)
    {
        self::$access = $access;
    }

    /**
     * Get API controller instance.
     *
     * @return BaseController API controller instance for current request
     */
    public function getController(): BaseController
    {
        return $this->controller;
    }
}
