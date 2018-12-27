<?php
/**
 * Copyright (c) 2018 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Page;

use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Utility\GeneralUtility;
use EliasHaeussler\Api\Utility\RoutingUtility;

/**
 * @todo doc
 *
 * @package EliasHaeussler\Api\Page
 * @author Elias Häußler <mail@elias-haeussler.de>
 */
class Frontend
{
    /** @var string Message type for successful messages */
    const MESSAGE_TYPE_SUCCESS = "success";

    /** @var string Message type for notices */
    const MESSAGE_TYPE_NOTICE = "notice";

    /** @var string Message type for warnings */
    const MESSAGE_TYPE_WARNING = "warning";

    /** @var string Message type for error messages */
    const MESSAGE_TYPE_ERROR = "error";

    /**
     * @var Template Page template
     */
    protected static $template;


    /**
     * @todo add doc
     *
     * @internal Internally used to initialize template when rendering messages
     */
    public static function initializeTemplate()
    {
        if (!self::$template) {
            try {
                self::$template = GeneralUtility::makeInstance(Template::class);
            } catch (ClassNotFoundException $e) {
            }
        }
    }

    /**
     * @todo add doc
     *
     * @param string $content
     * @return string
     */
    public static function bootstrap(string $content): string
    {
        self::initializeTemplate();

        $body = sprintf('<div class="page">%s</div>', $content);
        return self::$template ? self::$template->renderTemplate([
            "body" => $body
        ]) : $body;
    }

    /**
     * @todo add doc
     *
     * @param string $header
     * @param string $body
     * @param string $type
     * @return string
     */
    public static function message(string $header, string $body, string $type = self::MESSAGE_TYPE_NOTICE): string
    {
        if (RoutingUtility::getAccess() == RoutingUtility::ACCESS_TYPE_CLI) {
            return $header . "\r\n" . $body;

        } else {
            $messageHeader = sprintf('<div class="message__header">%s</div>', $header);
            $messageBody = sprintf('<div class="message__body">%s</div>', $body);
            $copyright = sprintf('<div class="copyright">&copy; %s Elias Häußler. All rights reserved.</div>', date("Y"));
            $message = sprintf('<div class="message message--%s">%s</div>%s', $type, $messageHeader . $messageBody, $copyright);

            return self::bootstrap($message);
        }
    }

    /**
     * @todo add doc
     *
     * @param string $header
     * @param string $body
     * @return string
     */
    public static function success(string $header, string $body): string
    {
        return self::message($header, $body, self::MESSAGE_TYPE_SUCCESS);
    }

    /**
     * @todo add doc
     *
     * @param string $header
     * @param string $body
     * @return string
     */
    public static function notice(string $header, string $body): string
    {
        return self::message($header, $body);
    }

    /**
     * @todo add doc
     *
     * @param string $header
     * @param string $body
     * @return string
     */
    public static function warning(string $header, string $body): string
    {
        $header = sprintf("Warning: %s", $header);

        return self::message($header, $body, self::MESSAGE_TYPE_WARNING);
    }

    /**
     * @todo add doc
     *
     * @param \Exception $object
     * @return string
     */
    public static function error(\Exception $object): string
    {
        $header = sprintf("Error: %s", get_class($object));
        $body = sprintf("%s [%s]", $object->getMessage(), $object->getCode());

        return self::message($header, $body, self::MESSAGE_TYPE_ERROR);
    }
}
