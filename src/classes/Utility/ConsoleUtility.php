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

/**
 * Console utility functions.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0+
 */
class ConsoleUtility
{
    /** @var int Git revision as history type */
    const HISTORY_TYPE_REVISION = 1;

    /** @var int Git version as history type */
    const HISTORY_TYPE_VERSION = 2;

    /**
     * Build a console command by a given script name and optional parameters.
     *
     * Builds a console command by using the given script name and optional parameters. The parameters need to
     * be defined in parameterName => value combination for named parameters; unnamed parameters do not need
     * a parameterName and therefore no specified key. Note that the ordering of parameters will be respected
     * when building the console command.
     *
     * @param string $scriptName The script name
     * @param array  $parameters Optional parameters (named or unnamed)
     *
     * @return string The console command
     */
    public static function buildCommand(string $scriptName, array $parameters = []): string
    {
        if (!$scriptName) {
            throw new \InvalidArgumentException("Please specify a script name to be used.", 1547904016);
        }

        $command = $scriptName;
        foreach ($parameters as $parameter => $value) {
            if (is_int($parameter)) {
                $command .= " " . $value;
            } else {
                $command .= sprintf(
                    (strlen($parameter) == 1 ? " -%s '%s'" : " --%s='%s'"),
                    $parameter,
                    str_replace("'", "\\'", $value)
                );
            }
        }

        return $command;
    }

    /**
     * Describe history of Git commit on which the API is currently running.
     *
     * @param int $mode Type of description, should be one of `HISTORY_TYPE_` constants
     *
     * @return string Description of history
     */
    public static function describeHistory(int $mode = self::HISTORY_TYPE_REVISION): string
    {
        // Get command and file name (as fallback) for history description mode
        switch ($mode) {
            case self::HISTORY_TYPE_VERSION:
                $command = sprintf("git --git-dir=%s/.git describe --tags 2> /dev/null", ROOT_PATH);
                $fileName = ROOT_PATH . "/VERSION";
                break;

            case self::HISTORY_TYPE_REVISION:
            default:
                $command = sprintf('git --git-dir=%s/.git log --pretty="%%h" -n1 HEAD 2> /dev/null', ROOT_PATH);
                $fileName = ROOT_PATH . "/REVISION";
                break;
        }

        // Execute command
        $commandResult = exec($command);

        // Return result of use file name as fallback if result is empty
        if ($commandResult) {
            return $commandResult;
        }
        if (@file_exists($fileName)) {
            return trim(fgets(fopen($fileName, 'r')));
        }

        return "";
    }
}
