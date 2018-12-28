<?php
/**
 * Copyright (c) 2018 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Frontend;

use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Utility\GeneralUtility;
use EliasHaeussler\Api\Utility\RoutingUtility;

/**
 * Frontend rendering class.
 *
 * This class enables the rendering of messages in the Frontend using a Frontend template renderer. This template
 * renderer is provided through the `Template` class.
 *
 * @package EliasHaeussler\Api\Frontend
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
 */
class Message
{
    /** @var string Message type for successful messages */
    const MESSAGE_TYPE_SUCCESS = "success";

    /** @var string Message type for notices */
    const MESSAGE_TYPE_NOTICE = "notice";

    /** @var string Message type for warnings */
    const MESSAGE_TYPE_WARNING = "warning";

    /** @var string Message type for error messages */
    const MESSAGE_TYPE_ERROR = "error";

    /** @var Template Page template */
    protected $template;


    /**
     * Initialize Frontend template for Frontend message rendering.
     *
     * @throws ClassNotFoundException if the `Template` class is not available
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
     * @param string $body Message body
     * @param string $type Message type
     * @return string Rendered message
     */
    public function message(string $header, string $body, string $type = self::MESSAGE_TYPE_NOTICE): string
    {
        if (RoutingUtility::getAccess() == RoutingUtility::ACCESS_TYPE_BOT) {
            return $header . "\r\n" . $body;

        } else {
            return $this->template->renderTemplate([
                "message" => [
                    "type" => $type,
                    "header" => $header,
                    "body" => $body,
                ],
            ]);
        }
    }

    /**
     * Render success message
     *
     * @param string $header Message header
     * @param string $body Message body
     * @return string Rendered message
     */
    public function success(string $header, string $body): string
    {
        return $this->message($header, $body, self::MESSAGE_TYPE_SUCCESS);
    }

    /**
     * Render notice message.
     *
     * @param string $header Message header
     * @param string $body Message body
     * @return string Rendered message
     */
    public function notice(string $header, string $body): string
    {
        return $this->message($header, $body);
    }

    /**
     * Render warning message.
     *
     * @param string $header Message header
     * @param string $body Message body
     * @return string Rendered message
     */
    public function warning(string $header, string $body): string
    {
        $header = sprintf("Warning: %s", $header);

        return $this->message($header, $body, self::MESSAGE_TYPE_WARNING);
    }

    /**
     * Render error message.
     *
     * @param \Exception $object Exception object
     * @return string Rendered message
     */
    public function error(\Exception $object): string
    {
        $header = sprintf("Error: %s", get_class($object));
        $body = sprintf("%s [%s]", $object->getMessage(), $object->getCode());

        return $this->message($header, $body, self::MESSAGE_TYPE_ERROR);
    }
}
