<?php
/**
 * Copyright (c) 2019 Elias Häußler <elias@haeussler.dev>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Helpers;

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
 * Formatting of Slack messages.
 *
 * This class allows easy formatting of Slack messages.
 *
 * @see https://api.slack.com/docs/message-formatting
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0+
 */
class SlackMessage
{
    /** @var string Regex pattern to identify format placeholders */
    const PLACEHOLDER_PATTERN = '/%%(%s){([^}]*)}/i';

    /** @var array Available message formats and their corresponding characters */
    const MESSAGE_FORMATS = [
        'emoji' => ':',
        'bold' => '*',
        'code' => '`',
        'italic' => '_',
        'strike' => '~',
        'link' => '<>',
    ];

    /** @var string Default prefix for mentions */
    const MENTION_PREFIX = '<@';

    /** @var string Character separating URL and link text */
    const LINK_TEXT_SEPARATOR = '|';

    /** @var array List of special mentions within Slack messages */
    const SPECIAL_MENTIONS = [
        'here',
        'channel',
        'everyone',
    ];

    /**
     * Magic method to style text with a specific message format.
     *
     * This methods allows styling of a text using the available message formats which are described in
     * {@see SlackMessage::MESSAGE_FORMATS}. Each array key can be used as method name while the input text should
     * be passed as first argument.
     *
     * @param string $name      Method name, should be one of the array keys in {@see SlackMessage::MESSAGE_FORMATS}
     * @param array  $arguments Method arguments, first argument should be the text to be formatted
     *
     * @throws \InvalidArgumentException if no input text has been provided
     *
     * @return string The formatted text
     */
    public static function __callStatic(string $name, array $arguments): string
    {
        if (count($arguments) == 0) {
            throw new \InvalidArgumentException('Please provide a valid text to be formatted.', 1550189187);
        }

        $text = trim($arguments[0]);

        // Return input text if message format is not available
        if (!isset(self::MESSAGE_FORMATS[$name])) {
            return $text;
        }

        return self::wrapTextWithCharacters($text, ...GeneralUtility::splitIntoCharacters(self::MESSAGE_FORMATS[$name], 2));
    }

    /**
     * Generate link.
     *
     * @param string $url   The URL
     * @param string $label An optional link text
     *
     * @return string The formatted link
     */
    public static function link(string $url, string $label = ''): string
    {
        $label = trim($label);
        list($prefix, $suffix) = GeneralUtility::splitIntoCharacters(self::MESSAGE_FORMATS['link'], 2);

        return self::wrapTextWithCharacters(
            !empty($label) ? sprintf('%s|%s', $url, $label) : $url,
            $prefix,
            $suffix
        );
    }

    /**
     * Generation mention for users or teams.
     *
     * @param string $name   The mention name, can be a special mention, user ID or team ID
     * @param bool   $isTeam Defines whether to mention a team
     *
     * @return string The formatted mention
     */
    public static function mention(string $name, bool $isTeam = false): string
    {
        $name = trim($name);
        $prefix = self::MENTION_PREFIX;
        $suffix = '>';

        if (in_array(strtolower($name), self::SPECIAL_MENTIONS)) {
            // Check for special mentions
            $prefix = '<!';
            $name = strtolower($name);
        } else {
            // Check for teams
            if ($isTeam) {
                $prefix = '<!subteam^';
            }
        }

        return self::wrapTextWithCharacters($name, $prefix, $suffix);
    }

    /**
     * Generate date.
     *
     * @param \DateTime $date     The {@see DateTime} object which holds the date
     * @param string    $format   A pre-formatted string to be used for formatting the date
     * @param string    $link     An optional link to be wrapped around the date string
     * @param string    $fallback An optional fallback text
     *
     * @return string The formatted date
     *
     * @see https://api.slack.com/docs/message-formatting#formatting_dates
     */
    public static function date(\DateTime $date, string $format = '{date_pretty}', string $link = '', string $fallback = ''): string
    {
        $timestamp = $date->getTimestamp();
        $contents = [
            $timestamp,
            $format,
            $link,
        ];
        if (!$fallback) {
            $fallback = $date->format('d.m.Y');
        }

        return sprintf('<!date^%s|%s>', implode('^', array_filter($contents)), $fallback);
    }

    /**
     * Convert message format placeholders into valid text.
     *
     * @param string $text Text containing message format placeholders
     *
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
            $format = trim($matches[1]);
            $value = trim($matches[2]);
            $value = $format == 'link' ? explode(self::LINK_TEXT_SEPARATOR, $value, 2) : (array) $value;

            return self::$format(...$value);
        }, $text);
    }

    /**
     * Wrap text with characters.
     *
     * @param string $text   Text to be wrapped
     * @param string $prefix Characters to be used as prefix
     * @param string $suffix Characters to be used as suffix
     *
     * @return string The wrapped text
     */
    protected static function wrapTextWithCharacters(string $text, string $prefix, string $suffix = ''): string
    {
        $text = trim($text);
        $prefix = trim($prefix);
        if (!$suffix) {
            $suffix = $prefix;
        }

        return !empty($text) ? ($prefix . $text . $suffix) : '';
    }

    /**
     * Build regular expression for available message formats.
     *
     * @return string Regular expression matching all available message formats
     */
    protected static function buildPlaceholderPatternFromMessageFormats(): string
    {
        return sprintf(self::PLACEHOLDER_PATTERN, implode('|', array_keys(self::MESSAGE_FORMATS)));
    }
}
