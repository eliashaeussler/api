<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Helpers;

/**
 * Formatting of Slack messages.
 *
 * This class allows easy formatting of Slack messages.
 *
 * @link https://api.slack.com/docs/message-formatting
 * @package EliasHaeussler\Api\Helpers
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
 */
class SlackMessage
{
    /** @var string Regex pattern to identify format placeholders */
    const PLACEHOLDER_PATTERN = "/%%(%s){([^}]*)}/i";

    /** @var array Available message formats and their corresponding characters */
    const MESSAGE_FORMATS = [
        "emoji" => ":",
        "bold" => "*",
        "code" => "`",
        "italic" => "_",
        "strike" => "~",
    ];


    /**
     * Magic method to style text with a specific message format.
     *
     * This methods allows styling of a text using the available message formats which are described in
     * {@see SlackMessage::MESSAGE_FORMATS}. Each array key can be used as method name while the input text should
     * be passed as first argument.
     *
     * @param string $name Method name, should be one of the array keys in {@see SlackMessage::MESSAGE_FORMATS}
     * @param array $arguments Method arguments, first argument should be the text to be formatted
     * @return string The formatted text
     * @throws \InvalidArgumentException if no input text has been provided
     */
    public static function __callStatic(string $name, array $arguments): string
    {
        if (count($arguments) == 0) {
            throw new \InvalidArgumentException("Please provide a valid text to be formatted.", 1550189187);
        }

        $text = trim($arguments[0]);

        // Return input text if message format is not available
        if (!isset(self::MESSAGE_FORMATS[$name])) {
            return $text;
        }

        return self::wrapTextWithCharacters($text, self::MESSAGE_FORMATS[$name]);
    }

    /**
     * Wrap text with characters.
     *
     * @param string $text Text to be wrapped
     * @param string $characters Character to be used as wrapping elements
     * @return string The wrapped text
     */
    protected static function wrapTextWithCharacters(string $text, string $characters): string
    {
        $text = trim($text);
        $characters = trim($characters);
        return !empty($text) ? ($characters . $text . $characters) : "";
    }

    /**
     * Convert message format placeholders into valid text.
     *
     * @param string $text Text containing message format placeholders
     * @return string Converted text
     */
    public static function convertPlaceholders(string $text): string
    {
        // Build message format pattern
        $pattern = self::buildPlaceholderPatternFromMessageFormats();

        // Convert input text
        return preg_replace_callback($pattern, function ($matches) {
            if (count($matches) < 3) {
                return $matches[0];
            }
            $format = $matches[1];
            return self::$format(trim($matches[2]));
        }, $text);
    }

    /**
     * Build regular expression for available message formats.
     *
     * @return string Regular expression matching all available message formats
     */
    protected static function buildPlaceholderPatternFromMessageFormats(): string
    {
        return sprintf(self::PLACEHOLDER_PATTERN, implode("|", array_keys(self::MESSAGE_FORMATS)));
    }
}
