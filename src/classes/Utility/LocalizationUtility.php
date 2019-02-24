<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Utility;

use EliasHaeussler\Api\Exception\FileNotFoundException;
use EliasHaeussler\Api\Exception\InvalidFileException;

/**
 * Localization utility functions.
 *
 * @package EliasHaeussler\Api\Utility
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
 */
class LocalizationUtility
{
    /** @var string Path where localization files are stored */
    const LOCALIZATION_PATH = SOURCE_PATH . "/l10n";

    /** @var string Pattern of localization file names */
    const LOCALIZATION_FILE_PATTERN = "%s.xml";

    /** @var string Default localization file name */
    const DEFAULT_FILE_NAME = "default";

    /** @var string Xpath to localization nodes in localization files */
    const LOCALIZATION_TEXT_XPATH = "//l10n/body/text[@id]";

    /** @var array Cache of localization files */
    protected static $fileCache = [];

    /**
     * Localize a text by its id and localization type.
     *
     * Returns a localized text which is identified by its id and the localization type. The localization type needs to
     * match the appropriate localization file without its file extension.
     *
     * @param string $id The localization text identifier
     * @param string|null $type The localization type, needs to match the basename of the appropriate localization file
     * @param string|null $default An optional default value which will be returned if the localization text is not available
     * @param mixed ...$arguments Additional arguments which will be passed to the internal {@see sprintf()} function
     * @return string The localized text
     */
    public static function localize(string $id, ?string $type = self::DEFAULT_FILE_NAME, ?string $default = "", ...$arguments): string
    {
        if ($type == null) {
            $type = self::DEFAULT_FILE_NAME;
        }
        if ($default == null) {
            $default = "";
        }

        try {
            // Parse localization file nodes
            self::parseNodes($type);

            // Get localization file nodes
            $type = strtolower($type);
            $nodes = self::$fileCache[$type]["nodes"] ?? [];

            if (isset($nodes[$id])) {
                return sprintf($nodes[$id], ...$arguments);
            } else {
                return $default;
            }

        } catch (FileNotFoundException | InvalidFileException $e) {
            return $default;
        }
    }

    /**
     * Parse nodes of a localization file.
     *
     * Reads the file contents of a localization file, identified by the given type, and parses its nodes. During the
     * execution of this method, the file contents and its nodes will be saved to the
     * {@see LocalizationUtility::$fileCache} property. Note that the method does not return the nodes of the
     * localization files, but only parses and stores them locally.
     *
     * @param string $type The localization type, needs to match the basename of the appropriate localization file
     * @throws FileNotFoundException if the localization file cannot be found or is not readable
     * @throws InvalidFileException if the localization file contains invalid content
     */
    protected static function parseNodes(string $type = self::DEFAULT_FILE_NAME): void
    {
        // Get file cache
        $type = strtolower($type);
        $fileCache = &self::$fileCache[$type];

        if ($fileCache != null) {
            return;
        }

        // Get file contents
        self::readFileContents($type);

        // Parse XML nodes
        $xml = new \SimpleXMLElement($fileCache["contents"]);
        $nodes = $xml->xpath(self::LOCALIZATION_TEXT_XPATH);

        if ($nodes === false) {
            throw new InvalidFileException(
                sprintf(
                    "The localization file for the type \"%s\" contains invalid nodes and cannot be parsed correctly.",
                    $type
                ), 1551035667
            );
        }

        // Add localizations to cache
        $fileCache["nodes"] = [];
        foreach ($nodes as $node) {
            $id = (string) $node->xpath('@id')[0];
            $text = trim((string) $node);
            $fileCache["nodes"][$id] = $text;
        }
    }

    /**
     * Read contents of a localization file.
     *
     * Reads the contents of a localization file, identified by the given type, and stores them locally in the
     * {@see LocalizationUtility::$fileCache} property. Note that this method does not return the file contents,
     * but only parses and stores them locally.
     *
     * @param string $type The localization type, needs to match the basename of the appropriate localization file
     * @throws FileNotFoundException if the localization file cannot be found or is not readable
     */
    protected static function readFileContents(string $type = self::DEFAULT_FILE_NAME): void
    {
        $type = strtolower($type);
        $fileName = sprintf(self::LOCALIZATION_FILE_PATTERN, $type);
        $filePath = sprintf("%s/%s", self::LOCALIZATION_PATH, $fileName);

        // Get file contents
        if (!($fileContents = @file_get_contents($filePath))) {
            throw new FileNotFoundException(
                sprintf("The localization file \"%s\" could not be found or is not readable.", $fileName),
                1551035147
            );
        }

        // Store file contents
        $fileContents = trim($fileContents);
        if (!isset(self::$fileCache[$type])) {
            self::$fileCache[$type] = [];
        }
        self::$fileCache[$type]["parse_time"] = time();
        self::$fileCache[$type]["contents"] = $fileContents;
    }
}
