<?php
/**
 * Copyright (c) 2018 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Controller;

/**
 * @todo documentation
 *
 * @package EliasHaeussler\Api\Controller
 * @author Elias Häußler <mail@elias-haeussler.de>
 */
abstract class BaseController
{
    /**
     * @var string
     */
    protected $requestBody;

    /**
     * @var array
     */
    protected $requestHeaders = [];

    /**
     * @var string|null
     */
    protected $route;


    /**
     * @todo add doc
     *
     * @param string $route
     */
    final public function __construct(string $route)
    {
        $this->readRequestBody();
        $this->readRequestHeaders();
        $this->setRoute($route);

        $this->initializeRequest();
    }

    /**
     * @todo add doc
     */
    protected function readRequestBody()
    {
        $this->requestBody = file_get_contents('php://input');
    }

    /**
     * @todo add doc
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
            $header = preg_replace("/^(\s?HTTP_)/", "", $header);
            $headers[$header] = $value;
        }, ARRAY_FILTER_USE_KEY);

        $this->requestHeaders = $headers;
    }

    /**
     * @todo add doc
     */
    protected function initializeRequest() {}

    /**
     * @todo add doc
     *
     * @param string $route
     * @return bool
     */
    protected function matchesRoute(string $route)
    {
        return trim(strtolower($route)) == $this->route;
    }

    /**
     * @todo add doc
     *
     * @return mixed
     */
    abstract public function call();

    /**
     * @return string
     */
    public function getRequestBody(): string
    {
        return $this->requestBody;
    }

    /**
     * @return array
     */
    public function getRequestHeaders(): array
    {
        return $this->requestHeaders;
    }

    /**
     * @todo add doc
     *
     * @param string $header
     * @return string
     */
    public function getRequestHeader(string $header): string
    {
        $normalizedHeader = str_replace("-", "_", strtoupper($header));
        return $this->requestHeaders[$normalizedHeader] ?? "";
    }

    /**
     * @param array $requestHeaders
     */
    public function setRequestHeaders(array $requestHeaders)
    {
        $this->requestHeaders = $requestHeaders;
    }

    /**
     * @return string|null
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * @param string|null $route
     */
    public function setRoute(string $route)
    {
        $this->route = trim(strtolower($route));
    }
}
