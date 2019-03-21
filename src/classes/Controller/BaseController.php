<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Controller;

use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Exception\InvalidParameterException;
use EliasHaeussler\Api\Exception\NoMappingDefinedException;
use EliasHaeussler\Api\Frontend\Message;
use EliasHaeussler\Api\Routing\BaseRoute;
use EliasHaeussler\Api\Service\LogService;
use EliasHaeussler\Api\Utility\GeneralUtility;
use EliasHaeussler\Api\Utility\LocalizationUtility;

/**
 * Base API controller.
 *
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
 */
abstract class BaseController
{
    /** @var string Regex pattern for matching HTTP prefix in request header */
    const REQUEST_HEADER_PREFIX_PATTERN = "/^(\\s?HTTP_)/";

    /** @var array Classes for each available route */
    const ROUTE_MAPPINGS = [];

    /** @var string API request body */
    protected $requestBody;

    /** @var array Additional HTTP headers of API request */
    protected $requestHeaders = [];

    /** @var array API request parameters */
    protected $requestParameters = [];

    /** @var string API request route */
    protected $route;

    /**
     * Initialize API request for selected controller.
     *
     * Initializes the current API request for the selected controller by reading the request body and HTTP headers and
     * defining the selected route. Finally, the concrete controller initialization will be processed. This is done by
     * calling {@see initializeRequest()}.
     *
     * @param string $route API request route
     */
    final public function __construct(string $route)
    {
        $this->setRoute($route);

        $this->readRequestBody();
        $this->readRequestHeaders();
        $this->readRequestParameters();
        $this->initializeEnvironment();
        $this->initializeRequest();
    }

    /**
     * Process API request.
     *
     * This methods calls a concrete routing class to process the current API request. It's necessary to check both the
     * validity and authenticity inside this method as it differs from different requests.
     *
     * @throws ClassNotFoundException    if the routing class is not available
     * @throws NoMappingDefinedException if no route mapping is defined for the current route
     */
    public function call()
    {
        if (!array_key_exists($this->route, $this::ROUTE_MAPPINGS)) {
            throw new NoMappingDefinedException(
                LocalizationUtility::localize("exception.1552821753", null, "", $this->route),
                1552821753
            );
        }

        // Get routing method
        $route_method = $this::ROUTE_MAPPINGS[$this->route];

        LogService::log(
            sprintf("Routing to \"%s\" from controller \"%s\"", $route_method, static::class),
            LogService::DEBUG
        );

        /** @var BaseRoute $method */
        $method = GeneralUtility::makeInstance($route_method, $this);
        $method->processRequest();
    }

    /**
     * Build message for Frontend.
     *
     * Calls the appropriate method in {@see Message} class to build a message in the Frontend. Note that it's required
     * to pass all necessary arguments to this method if they are required by the appropriate method in {@see Message}
     * class. The first parameter `$type` defines the name of the method to be used for building the message.
     *
     * @param string $type Message type, will be used to call the appropriate method in {@see Message} class
     * @param mixed  $arg1 First argument to be passed to appropriate method in {@see Message} class. Required.
     * @param array  $_    More arguments to be passed to appropriate method in {@see Message} class. Optional.
     *
     * @throws ClassNotFoundException if the {@see Message} class is not available
     *
     * @return string The generated message, if successful, or an empty string, if the build process failed
     */
    public function buildMessage(string $type, $arg1, ...$_): string
    {
        // Define class instance, method and arguments
        $object = GeneralUtility::makeInstance(Message::class);
        $method = strtolower($type);
        $arguments = array_merge([$arg1], $_);

        // Check if selected method exists in `Message` class
        $availableMethods = get_class_methods(Message::class);

        // Define fallback method and arguments if requested method is not available
        if (!in_array($method, $availableMethods)) {
            $method = "message";

            // Ensure that header and body arguments are always set
            if (count($arguments) < 2) {
                $messageParts = explode("\r\n", $arguments[0]);
                $header = $messageParts[0];
                $body = count($messageParts) > 1 ? $messageParts[1] : "";
                $arguments = [$header, $body, $type ?: Message::MESSAGE_TYPE_NOTICE];
            }
        }

        return call_user_func([$object, $method], ...$arguments) ?: "";
    }

    /**
     * Get body of current API request.
     *
     * @return string API request body
     */
    public function getRequestBody(): string
    {
        return $this->requestBody;
    }

    /**
     * Get HTTP headers of current API request.
     *
     * @return array Additional HTTP headers of API request
     */
    public function getRequestHeaders(): array
    {
        return $this->requestHeaders;
    }

    /**
     * Get value of given HTTP header.
     *
     * Returns the value of a given HTTP header of the current API request. Note that you shouldn't provide the "HTTP_"
     * prefix in the `$header` argument. Also note that hyphens will be replaced by underscores. This can be disabled
     * by setting `$useRawInput` to `true`.
     *
     * @param string $header      Name of the HTTP header to return
     * @param bool   $useRawInput Define whether the raw name of the given HTTP header should be used
     *
     * @return string Value of the given HTTP header
     */
    public function getRequestHeader(string $header, bool $useRawInput = false): string
    {
        $normalizedHeader = strtoupper($header);
        $normalizedHeader = preg_replace($this::REQUEST_HEADER_PREFIX_PATTERN, "", $normalizedHeader);
        if (!$useRawInput) {
            $normalizedHeader = str_replace("-", "_", $normalizedHeader);
        }

        return $this->requestHeaders[$normalizedHeader] ?? "";
    }

    /**
     * Get API request parameters.
     *
     * @return array API request parameters
     */
    public function getRequestParameters(): array
    {
        return $this->requestParameters;
    }

    /**
     * Get value of given API request parameters.
     *
     * @param string $key Name of the API request parameter to return
     *
     * @return mixed|null API request parameters
     */
    public function getRequestParameter(string $key)
    {
        return isset($this->requestParameters[$key]) ? $this->requestParameters[$key] : null;
    }

    /**
     * Set current API request route.
     *
     * @param string $route
     */
    public function setRoute(string $route)
    {
        $this->route = trim(strtolower($route));
    }

    /**
     * Initialize API request.
     *
     * Defines necessary variables for the API request and ensures that it can be processed without errors. This method
     * can also be used to pre-check API conditions and user authentications.
     */
    abstract protected function initializeRequest();

    /**
     * Read body of current API request.
     *
     * Reads the raw body of the current API request and stores it in the current object.
     */
    protected function readRequestBody()
    {
        $this->requestBody = file_get_contents('php://input');
    }

    /**
     * Rad HTTP headers of current API request.
     *
     * Reads all HTTP headers of the current API request and stores them in the current object. Note that this method
     * will only read headers starting with "HTTP_" as they can be customized by the sender. The prefix will be removed
     * from the header name.
     */
    protected function readRequestHeaders()
    {
        $headers = [];

        // Get HTTP headers
        $allHeaders = $_SERVER;
        $httpHeaders = array_filter($allHeaders, function ($header) {
            return stripos(trim($header), "HTTP_") === 0;
        }, ARRAY_FILTER_USE_KEY);

        // Normalize HTTP headers by removing "HTTP_" prefix
        array_walk($httpHeaders, function (&$value, $header) use (&$headers) {
            $header = preg_replace($this::REQUEST_HEADER_PREFIX_PATTERN, "", $header);
            $headers[$header] = $value;
        }, ARRAY_FILTER_USE_KEY);

        $this->requestHeaders = $headers;
    }

    /**
     * Read GET and POST parameters of current API request.
     *
     * Reads the GET and POST parameters of the current API request and stores them together in the current object.
     */
    protected function readRequestParameters()
    {
        $parameters = array_merge($_GET, $_POST);
        array_walk_recursive($parameters, function (&$value) {
            $value = urldecode($value);
        });
        $this->requestParameters = $parameters;
    }

    /**
     * Initialize user environment.
     */
    protected function initializeEnvironment()
    {
    }

    /**
     * Check if given route matches the current route.
     *
     * @param string $route Route to be checked against the current route
     *
     * @return bool `true` if the given route matches the current route, `false` otherwise
     */
    protected function matchesRoute(string $route)
    {
        return trim(strtolower($route)) == $this->route;
    }
}
