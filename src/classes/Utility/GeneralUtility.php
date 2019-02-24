<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Utility;

use Dotenv\Dotenv;
use Dotenv\Loader;
use EliasHaeussler\Api\Exception\ClassNotFoundException;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

/**
 * General utility functions.
 *
 * @package EliasHaeussler\Api\Utility
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
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
     * @param string $className Name of the class whose instance should be returned
     * @param mixed ...$constructorArguments Optional arguments for the appropriate class constructor
     * @return object A concrete instance of `$className`
     * @throws ClassNotFoundException if the requested class is not available
     */
    public static function makeInstance(string $className, ...$constructorArguments)
    {
        if (class_exists($className)) {
            if (!isset(self::$instances[$className])) {
                self::$instances[$className] = new $className(...$constructorArguments);
            }
            return self::$instances[$className];

        } else {
            throw new ClassNotFoundException(
                LocalizationUtility::localize("exception.1543534319", null, null, $className),
                1543534319
            );
        }
    }

    /**
     * Explode string by given delimiter and trim all resulting array components.
     *
     * @param string $delimiter Boundary string
     * @param string $string Input string
     * @param string $trimCharacters Trim characters to be passed to {@see trim()}
     * @param int $limit Limit to be passed to {@see explode()}
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
                if (!empty($trimmedValue)) $result[] = $trimmedValue;
            });

            return $result;
        }

        return $values;
    }

    /**
     * Replace first occurrence of search string with replacement string.
     *
     * @param string $haystack The input string
     * @param string $needle The search pattern
     * @param string $replacement The replacement string
     * @return string The replaced input string
     */
    public static function replaceFirst(string $haystack, string $needle, string $replacement): string
    {
        if (($pos = strpos($haystack, $needle)) !== false) {
            return substr_replace($haystack, $replacement, $pos, strlen($needle));
        } else {
            return $haystack;
        }
    }

    /**
     * Split string into single characters.
     *
     * Splits a given string into its single characters and returns them as an array. Optionally, it's possible to set
     * a maximum number of characters to be returned.
     *
     * @param string $string String to be split into characters
     * @param int|null $max Maximum number of characters to be returned
     * @return array Single characters of given string
     */
    public static function splitIntoCharacters(string $string, int $max = null): array
    {
        return array_slice(str_split($string), 0, $max);
    }

    /**
     * Get normalized name of current API controller without namespace and class suffix.
     *
     * @param string $class Class name of the API controller
     * @return string Normalized name of current API controller
     */
    public static function getControllerName(string $class)
    {
        $controller = ($pos = strrpos($class, "\\")) ? substr($class, $pos + 1) : $pos;
        if ($controller === false) {
            return $class;
        } else {
            return implode("", preg_split("/Controller$/", $controller, -1, PREG_SPLIT_NO_EMPTY));
        }
    }

    /**
     * Load API environment.
     *
     * Reads the environment variables of the current environment in order to use them in the API request.
     *
     * @param string $file File name of the .env file
     * @param bool $silent Define whether to not throw errors if .env file could not be found
     */
    public static function loadEnvironment(string $file = ".env", bool $silent = false)
    {
        $path = ROOT_PATH . "/" . $file;

        if (isset(self::$dotenv[$path])) {
            $loader = self::$dotenv[$path];
        } else {
            $loader = new Dotenv(ROOT_PATH, $file);
            self::$dotenv[$path] = $loader;
        }

        $loader->{$silent ? "safeLoad" : "load"}();
    }

    /**
     * Get value of an environment variable.
     *
     * @param string $name Name of the environment variable
     * @param mixed $default Default variable if environment variable is not available
     * @return mixed The value of the given environment variable
     */
    public static function getEnvironmentVariable(string $name, $default = "")
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
        $handler = new PrettyPageHandler();
        foreach (self::getEnvironmentVariableNames() as $key) {
            $handler->blacklist("_ENV", $key);
            $handler->blacklist("_SERVER", $key);
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
        return !!static::getEnvironmentVariable("DEBUG_EXCEPTIONS", false);
    }
}
