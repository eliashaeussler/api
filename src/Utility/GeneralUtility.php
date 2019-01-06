<?php
/**
 * Copyright (c) 2018 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Utility;

use Dotenv\Dotenv;
use EliasHaeussler\Api\Exception\ClassNotFoundException;

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
            throw new ClassNotFoundException(sprintf(
                "The class \"%s\" could not be found.",
                $className
            ), 1543534319);
        }
    }

    /**
     * Explode string by given delimiter and trim all resulting array components.
     *
     * @param string $delimiter Boundary string
     * @param string $string Input string
     * @param string $trimCharacters Trim characters to be passed to `trim`
     * @param int $limit Limit to be passed to `explode`
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
     * Load API environment.
     *
     * Reads the environment variables of the current environment in order to use them in the API request.
     *
     * @param string $file File name of the .env file
     * @param bool $silent Define whether to not throw errors if .env file could not be found
     */
    public static function loadEnvironment(string $file = ".env", bool $silent = false)
    {
        $loader = new Dotenv(ROOT_PATH, $file);
        $loader->{$silent ? "safeLoad" : "load"}();
    }

    /**
     * Get value of an environment variable.
     *
     * @param string $name Name of the environment variable
     * @param mixed $default Default variable if environment variable is not available
     * @return string|array The value of the given environment variable
     */
    public static function getEnvironmentVariable(string $name, $default = "")
    {
        return getenv($name) ?: $default;
    }

    /**
     * Get latest Git commit on which the API is currently running.
     *
     * @return string Latest Git commit
     */
    public static function getGitCommit()
    {
        return exec('git log --pretty="%h" -n1 HEAD');
    }
}
