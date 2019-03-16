<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Utility;

use Adbar\Dot;
use EliasHaeussler\Api\Exception\FileNotFoundException;

/**
 * Data utility functions.
 *
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
 */
class DataUtility
{
    /** @var string Path in which data files are stored */
    const DATA_PATH = SOURCE_PATH . "/data";

    /** @var string File name pattern of data files */
    const DATA_FILE_PATTERN = "%s.json";

    /** @var array Storage of processed data files */
    private static $dataStorage = [];

    /**
     * Get data of a specified data file.
     *
     * Returns the contents of a specified JSON data file. Since all data files are located in the same directory
     * with the same file extension, it's necessary to provide only the file name without any file extension or
     * path to this method. Provide the optional `$key` parameter in dot notation to get a specific element.
     *
     * Example:
     * * `getData("slack")` returns all contents of {@see DataUtility::DATA_PATH}/slack.json file
     * * `getData("slack", "lunch.help-text")` returns the element `{ "lunch": { "help-text": "..." } }`
     *
     * @param string $fileName Data file name without path or file extension
     * @param string $key      Optional key to be used for traversing over JSON file and returning the resulting contents
     *
     * @throws FileNotFoundException if the data file is not available
     *
     * @return mixed The contents of the data file, or a specific set of elements if `$key` is defined
     */
    public static function getData(string $fileName, string $key = "")
    {
        // Build absolute file name
        $file = self::DATA_PATH . "/" . sprintf(self::DATA_FILE_PATTERN, strtolower($fileName));

        if (!@file_exists($file)) {
            throw new FileNotFoundException(
                LocalizationUtility::localize("exception.1547985632", null, null, $file),
                1547985632
            );
        }

        // Get file contents
        if (isset(self::$dataStorage[$file])) {
            $elements = self::$dataStorage[$file];
        } else {
            $elements = json_decode(file_get_contents($file), true);
            self::$dataStorage[$file] = $elements;
        }

        // Return all elements or a specific one
        if (!$key) {
            return $elements;
        }

        return (new Dot($elements))->get($key);
    }
}
