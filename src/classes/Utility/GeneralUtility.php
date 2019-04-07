<?php
/**
 * Copyright (c) 2019 Elias Häußler <elias@haeussler.dev>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Utility;

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

use Dotenv\Dotenv;
use Dotenv\Loader;
use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Service\LogService;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

/**
 * General utility functions.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0+
 */
class GeneralUtility
{
    /** @var array Class instances, ordered by class name */
    private static $instances = [];

    /** @var Dotenv[] Loader for environment variables */
    private static $dotenv = [];

    /**
     * Get new or existing instance of a given class.
     *
     * Instantiates or loads an existing instance of a given class with given constructor arguments and returns it.
     *
     * @param string $className               Name of the class whose instance should be returned
     * @param mixed  ...$constructorArguments Optional arguments for the appropriate class constructor
     *
     * @throws ClassNotFoundException if the requested class is not available
     *
     * @return object A concrete instance of `$className`
     */
    public static function makeInstance(string $className, ...$constructorArguments)
    {
        if (class_exists($className)) {
            if (!isset(self::$instances[$className])) {
                LogService::log(sprintf('Initializing class "%s" the first time', $className), LogService::DEBUG);

                self::$instances[$className] = new $className(...$constructorArguments);
            }

            return self::$instances[$className];
        }
        throw new ClassNotFoundException(
            LocalizationUtility::localize('exception.1543534319', null, null, $className),
            1543534319
        );
    }

    /**
     * Explode string by given delimiter and trim all resulting array components.
     *
     * @param string $delimiter      Boundary string
     * @param string $string         Input string
     * @param string $trimCharacters Trim characters to be passed to {@see trim()}
     * @param int    $limit          Limit to be passed to {@see explode()}
     *
     * @return array Array with string components
     */
    public static function trimExplode(
        string $delimiter,
        string $string,
        string $trimCharacters = " \t\n\r\0\x0B",
        int $limit = PHP_INT_MAX
    ) {
        $values = explode($delimiter, $string, $limit);

        if ($values !== false) {
            $result = [];
            array_walk($values, function ($value) use ($trimCharacters, &$result) {
                $trimmedValue = trim($value, $trimCharacters);
                if (!empty($trimmedValue)) {
                    $result[] = $trimmedValue;
                }
            });

            return $result;
        }

        return $values;
    }

    /**
     * Replace first occurrence of search string with replacement string.
     *
     * @param string $haystack    The input string
     * @param string $needle      The search pattern
     * @param string $replacement The replacement string
     *
     * @return string The replaced input string
     */
    public static function replaceFirst(string $haystack, string $needle, string $replacement): string
    {
        if (($pos = strpos($haystack, $needle)) !== false) {
            return substr_replace($haystack, $replacement, $pos, strlen($needle));
        }

        return $haystack;
    }

    /**
     * Split string into single characters.
     *
     * Splits a given string into its single characters and returns them as an array. Optionally, it's possible to set
     * a maximum number of characters to be returned.
     *
     * @param string   $string String to be split into characters
     * @param int|null $max    Maximum number of characters to be returned
     *
     * @return array Single characters of given string
     */
    public static function splitIntoCharacters(string $string, int $max = null): array
    {
        return array_slice(str_split($string), 0, $max);
    }

    /**
     * Convert array to its string representation.
     *
     * Recursively converts an array to its appropriate string representation, separated by the given separator.
     *
     * @param array  $array     The array to be converted into string
     * @param string $separator String separating array elements from each other
     *
     * @return string The converted string representation of the array
     */
    public static function convertArrayToString(array $array, string $separator = PHP_EOL): string
    {
        $result = '';
        array_walk_recursive($array, function ($value, $key) use ($separator, &$result) {
            $result .= sprintf('%s => %s', $key, $value) . $separator;
        });

        return substr_replace($result, '', -strlen($separator));
    }

    /**
     * Get normalized name of current API controller without namespace and class suffix.
     *
     * @param string $class Class name of the API controller
     *
     * @return string Normalized name of current API controller
     */
    public static function getControllerName(string $class)
    {
        $controller = ($pos = strrpos($class, '\\')) ? substr($class, $pos + 1) : $pos;
        if ($controller === false) {
            return $class;
        }

        return implode('', preg_split('/Controller$/', $controller, -1, PREG_SPLIT_NO_EMPTY));
    }

    /**
     * Get components of given uri.
     *
     * Returns the components of a given uri. This contains all path components without GET parameters.
     *
     * @param string $uri Uri which components should be returned
     *
     * @return array The uri components of the given uri
     */
    public static function getUriComponents(string $uri = ''): array
    {
        if (empty($uri)) {
            $uri = $_SERVER['REQUEST_URI'];
        }

        return self::trimExplode('/', strtok($uri, '?'));
    }

    /**
     * Get current server name from environment variable or server.
     *
     * Returns the server name by reading the appropriate environment variable. If it is not set,
     * the default server name, provided by the server itself, will be returned.
     *
     * @return string The current server name
     */
    public static function getServerName(): string
    {
        return self::getEnvironmentVariable('SERVER_NAME', $_SERVER['HTTP_HOST']);
    }

    /**
     * Load API environment.
     *
     * Reads the environment variables of the current environment in order to use them in the API request.
     *
     * @param string $file   File name of the .env file
     * @param bool   $silent Define whether to not throw errors if .env file could not be found
     */
    public static function loadEnvironment(string $file = '.env', bool $silent = false)
    {
        $path = ROOT_PATH . '/' . $file;

        if (isset(self::$dotenv[$path])) {
            $loader = self::$dotenv[$path];
        } else {
            LogService::log(sprintf('Loading environment file "%s/%s" the first time', ROOT_PATH, $file), LogService::DEBUG);

            $loader = new Dotenv(ROOT_PATH, $file);
            self::$dotenv[$path] = $loader;
        }

        $loader->{$silent ? 'safeLoad' : 'load'}();
    }

    /**
     * Get value of an environment variable.
     *
     * @param string $name    Name of the environment variable
     * @param mixed  $default Default variable if environment variable is not available
     *
     * @return mixed The value of the given environment variable
     */
    public static function getEnvironmentVariable(string $name, $default = '')
    {
        $loader = new Loader(ROOT_PATH);

        return $loader->getEnvironmentVariable($name) ?? $default;
    }

    /**
     * Get all loaded environment variables.
     *
     * @return array Array of loaded environment variables
     */
    public static function getEnvironmentVariableNames(): array
    {
        if (empty(self::$dotenv)) {
            self::loadEnvironment();
        }

        $variables = [];
        foreach (self::$dotenv as $dotenv) {
            $variables = array_merge($variables, $dotenv->getEnvironmentVariableNames());
        }

        return $variables;
    }

    /**
     * Register custom exception handler.
     *
     * Registers an alternative exception handler to be used for handling exceptions in the frontend.
     *
     * @throws ClassNotFoundException if the exception handler class is not available
     */
    public static function registerExceptionHandler()
    {
        LogService::log('Registering custom exception handler', LogService::DEBUG);

        $handler = new PrettyPageHandler();
        foreach (self::getEnvironmentVariableNames() as $key) {
            $handler->blacklist('_ENV', $key);
            $handler->blacklist('_SERVER', $key);
        }
        $whoops = self::makeInstance(Run::class);
        $whoops->pushHandler($handler);
        $whoops->register();
    }

    /**
     * Check whether debugging is enabled.
     *
     * @return bool `true` if debugging is enabled, `false` otherwise
     */
    public static function isDebugEnabled(): bool
    {
        self::loadEnvironment();

        return (bool) static::getEnvironmentVariable('DEBUG_EXCEPTIONS', false);
    }

    /**
     * Check whether the current request is secured with SSL.
     *
     * @return bool `true` if the request is secured, `false` otherwise
     */
    public static function isRequestSecure(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    }
}
