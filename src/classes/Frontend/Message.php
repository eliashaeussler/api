<?php
/**
 * Copyright (c) 2019 Elias Häußler <elias@haeussler.dev>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Frontend;

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

use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Service\LogService;
use EliasHaeussler\Api\Service\RoutingService;
use EliasHaeussler\Api\Utility\GeneralUtility;

/**
 * Frontend rendering class.
 *
 * This class enables the rendering of messages in the Frontend using a Frontend template renderer. This template
 * renderer is provided through the {@see Template} class.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0+
 */
class Message
{
    /** @var string Message type for successful messages */
    const MESSAGE_TYPE_SUCCESS = 'success';

    /** @var string Message type for notices */
    const MESSAGE_TYPE_NOTICE = 'notice';

    /** @var string Message type for warnings */
    const MESSAGE_TYPE_WARNING = 'warning';

    /** @var string Message type for error messages */
    const MESSAGE_TYPE_ERROR = 'error';

    /** @var Template Page template */
    protected $template;

    /**
     * Initialize Frontend template for Frontend message rendering.
     *
     * @throws ClassNotFoundException if the {@see Template} class is not available
     */
    public function __construct()
    {
        $this->template = GeneralUtility::makeInstance(Template::class);
    }

    /**
     * Render message.
     *
     * Renders a message by given header, body and type. If the current request was accessed by a bot, header and
     * body will be printed without formatting. Otherwise, the default template will be rendered.
     *
     * @param string $header Message header
     * @param string $body   Message body
     * @param string $type   Message type
     *
     * @return string Rendered message
     */
    public function message(string $header, string $body, string $type = self::MESSAGE_TYPE_NOTICE): string
    {
        if (RoutingService::getAccess() == RoutingService::ACCESS_TYPE_BOT) {
            return $header . "\r\n" . $body;
        }

        return $this->template->renderTemplate([
            'message' => [
                'type' => $type,
                'header' => nl2br($header),
                'body' => nl2br($body),
            ],
        ]);
    }

    /**
     * Render success message.
     *
     * @param string $header Message header
     * @param string $body   Message body
     *
     * @return string Rendered message
     */
    public function success(string $header, string $body): string
    {
        LogService::log(sprintf('%s: %s', $header, $body), LogService::SUCCESS);

        return $this->message($header, $body, self::MESSAGE_TYPE_SUCCESS);
    }

    /**
     * Render notice message.
     *
     * @param string $header Message header
     * @param string $body   Message body
     *
     * @return string Rendered message
     */
    public function notice(string $header, string $body): string
    {
        LogService::log(sprintf('%s: %s', $header, $body), LogService::NOTICE);

        return $this->message($header, $body);
    }

    /**
     * Render warning message.
     *
     * @param string $header Message header
     * @param string $body   Message body
     *
     * @return string Rendered message
     */
    public function warning(string $header, string $body): string
    {
        LogService::log(sprintf('%s: %s', $header, $body), LogService::WARNING);

        $header = sprintf('Warning: %s', $header);

        return $this->message($header, $body, self::MESSAGE_TYPE_WARNING);
    }

    /**
     * Render error message.
     *
     * @param \Exception $object Exception object
     *
     * @return string Rendered message
     */
    public function error(\Exception $object): string
    {
        LogService::log(sprintf('%s: %s', get_class($object), $object->getMessage()), LogService::ERROR);

        $header = sprintf('Error: %s', get_class($object));
        $body = $object->getMessage();
        if (($code = $object->getCode()) > 0) {
            $body .= ' [' . $code . ']';
        }
        if (GeneralUtility::isDebugEnabled()) {
            $body .= PHP_EOL . PHP_EOL . $object->getTraceAsString();
        }

        return $this->message($header, $body, self::MESSAGE_TYPE_ERROR);
    }
}
