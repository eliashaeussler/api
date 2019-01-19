<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Utility;

/**
 * Console utility functions.
 *
 * @package EliasHaeussler\Api\Utility
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
 */
class ConsoleUtility
{
    /**
     * Build a console command by a given script name and optional parameters.
     *
     * Builds a console command by using the given script name and optional parameters. The parameters need to
     * be defined in parameterName => value combination for named parameters; unnamed parameters do not need
     * a parameterName and therefore no specified key. Note that the ordering of parameters will be respected
     * when building the console command.
     *
     * @param string $scriptName The script name
     * @param array $parameters Optional parameters (named or unnamed)
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
     * Get latest Git commit on which the API is currently running.
     *
     * @return string Latest Git commit
     */
    public static function getGitCommit()
    {
        $revision = exec('git log --pretty="%h" -n1 HEAD');
        if (!$revision && @file_exists(ROOT_PATH . "/REVISION")) {
            return file_get_contents(ROOT_PATH . "/REVISION");
        } else {
            return "";
        }
    }
}
