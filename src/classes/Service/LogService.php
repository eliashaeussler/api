<?php
/**
 * Copyright (c) 2019 Elias Häußler <elias@haeussler.dev>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Service;

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

use EliasHaeussler\Api\Utility\GeneralUtility;

/**
 * File-based log service.
 *
 * This class provides a log service which can be used to log several actions during API requests in log files. It can
 * be helpful to reproduce errors or debug custom API functions. Log files will be rotated if they reach a file size
 * of 5 MB (respectively 5242880 Bytes). Use the console to clear old log files.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0+
 */
class LogService
{
    /** @var int Severity level for success messages */
    const SUCCESS = -1;

    /** @var int Severity level for debug messages */
    const DEBUG = 0;

    /** @var int Severity level for notices */
    const NOTICE = 1;

    /** @var int Severity level for warnings */
    const WARNING = 2;

    /** @var int Severity level for error messages */
    const ERROR = 3;

    /** @var array Names for the different severity levels */
    const SEVERITY_NAME = [
        self::SUCCESS => 'Success',
        self::DEBUG => 'Debug',
        self::NOTICE => 'Notice',
        self::WARNING => 'Warning',
        self::ERROR => 'Error',
    ];

    /** @var string Path to store log files */
    const LOG_DIRECTORY = TEMP_PATH . '/logs';

    /** @var string File name of the log files */
    const LOG_FILE_NAME = 'api.log';

    /** @var string File name pattern of rotated log files */
    const LOG_FILE_NAME_ROTATION_PATTERN = 'api.*.log';

    /** @var int Maximum file size of log files in bytes */
    const LOG_FILE_MAX_SIZE = 5242880;

    /**
     * Write a message to the log file using a given severity.
     *
     * Writes a given message to the log file with the given severity. The method respects the globally set minimum
     * log level so that e.g. messages with severity {@see LogService::DEBUG} won't be logged if `MINIMUM_LOG_LEVEL`
     * is set to a number higher than `0`.
     *
     * @param string $message  The message to be logged
     * @param int    $severity The messages' severity level
     */
    public static function log(string $message, int $severity = self::NOTICE): void
    {
        if ($severity < GeneralUtility::getEnvironmentVariable('MINIMUM_LOG_LEVEL', self::NOTICE)) {
            return;
        }

        // Rotate log files
        self::rotateLogFiles();

        // Build log message
        $message = self::buildLogMessage($message, $severity);

        // Write log message to log file
        file_put_contents(self::getLogFileName(), $message, FILE_APPEND);
    }

    /**
     * Get full path and file name of a requested log file.
     *
     * @param string|null $fileName The base file name of the log file, can be null to use the default file name
     *
     * @return string The file name of the requested log file
     */
    public static function getLogFileName(?string $fileName = null): string
    {
        if (!$fileName) {
            $fileName = self::LOG_FILE_NAME;
        }

        return sprintf('%s/%s', self::LOG_DIRECTORY, $fileName);
    }

    /**
     * Clear old log files.
     *
     * Removes old log files from the file system. Set `$keepDefaultFile` to `true` to preserve the default file
     * {@see LogService::LOG_FILE_NAME}. The method returns an array with the result of each file removal.
     *
     * @param bool $keepDefaultFile Define whether to keep the default log file
     *
     * @return array Result set of each log file removal
     */
    public static function clearLogFiles(bool $keepDefaultFile = true): array
    {
        $result = [];

        // Clear default log file
        $default_file = self::getLogFileName();
        if (!$keepDefaultFile && file_exists($default_file)) {
            $result[$default_file] = unlink($default_file);
        }

        // Clear rotated log files
        foreach (glob(self::getLogFileName(self::LOG_FILE_NAME_ROTATION_PATTERN)) as $logFile) {
            $result[$logFile] = unlink($logFile);
        }

        return $result;
    }

    /**
     * Build log message.
     *
     * Builds the log message by adding custom information like time, severity level and backtrace (only for error
     * severity level).
     *
     * @param string $message  The message to be used for building the complete log message
     * @param int    $severity The messages' severity level
     *
     * @return string The generated log message
     */
    protected static function buildLogMessage(string $message, int $severity = self::NOTICE): string
    {
        // Set time prefix
        $time = date('d.m.Y H:i:s');

        // Set severity prefix
        $severity = self::SEVERITY_NAME[$severity];

        // Customize message with backtrace (if needed) and line breaks
        if ($severity == self::ERROR) {
            ob_start();
            debug_print_backtrace();
            $trace = ob_get_contents();
            ob_end_clean();
            $message .= PHP_EOL . $trace;
        } else {
            $message .= PHP_EOL;
        }

        // Build message
        return sprintf('[%s] %s: %s', $time, $severity, $message);
    }

    /**
     * Rotate log files.
     *
     * Rotates the log files if their file sizes reaches the maximum file size defined within
     * {@see LogService::LOG_FILE_MAX_SIZE}. This means that the default log file will be suffixed with a number
     * higher than the last rotated log file. After that, a new default log file will be created. This method also
     * checks whether the log directory exists. After calling it, the default log file always exists and is writable.
     */
    protected static function rotateLogFiles(): void
    {
        $log_file = self::getLogFileName();

        // Build log directory and file if they do not exist
        if (!file_exists(self::LOG_DIRECTORY)) {
            mkdir(self::LOG_DIRECTORY, 0777, true);
            self::writeLogFile();

            return;
        }

        if (!file_exists($log_file)) {
            self::writeLogFile();
        }

        // Rotate log files
        if (filesize($log_file) >= self::LOG_FILE_MAX_SIZE) {
            $next_rotation = 1;

            // Get last rotated file
            if ($rotated_files = glob(self::getLogFileName(self::LOG_FILE_NAME_ROTATION_PATTERN))) {
                natsort($rotated_files);
                $last_file = basename(end($rotated_files));

                $file_pattern = sprintf('/^%s$/', str_replace('*', '(\\d+)', self::LOG_FILE_NAME_ROTATION_PATTERN));
                preg_match($file_pattern, $last_file, $matches);
                if ($matches) {
                    $next_rotation = $matches[1] + 1;
                }
            }

            // Rotate file
            $new_file = str_replace('*', $next_rotation, self::LOG_FILE_NAME_ROTATION_PATTERN);
            rename($log_file, self::getLogFileName($new_file));
            self::writeLogFile();
        }
    }

    /**
     * Write log file without contents.
     *
     * Writes a log file to the file system without writing any contents into it.
     *
     * @param string|null $fileName The base file name of the log file, can be null to use the default file name
     */
    protected static function writeLogFile(?string $fileName = null): void
    {
        touch(self::getLogFileName($fileName));
    }
}
