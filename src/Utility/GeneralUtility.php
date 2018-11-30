<?php
/**
 * Copyright (c) 2018 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */

namespace EliasHaeussler\Api\Utility;

use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Exception\InvalidClassHierarchyException;
use EliasHaeussler\Api\Singleton;

/**
 * @todo documentation needed
 *
 * @package EliasHaeussler\Api\Utility
 * @author Elias Häußler <mail@elias-haeussler.de>
 */
class GeneralUtility
{
    /**
     * @var array Singleton instances, ordered by class name
     */
    private static $instances = [];


    /**
     * @todo documentation needed
     *
     * @param string $delimiter
     * @param string $string
     * @param string $trimCharacters
     * @param int $limit
     * @return array
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
     * @todo documentation needed
     *
     * @param string $className
     * @param mixed ...$constructorArguments
     * @return object
     * @throws ClassNotFoundException
     */
    public static function makeInstance(string $className, ...$constructorArguments)
    {
        if (class_exists($className)) {
            if (!isset(self::$instances[$className])) {
                self::$instances[$className] = new $className(...$constructorArguments);
            }
            return self::$instances[$className];

        } else {
            throw new ClassNotFoundException(sprintf("The class \"%s\" could not be found.", $className), 1543534319);
        }
    }
}
