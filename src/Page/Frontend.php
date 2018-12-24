<?php
/**
 * Copyright (c) 2018 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Page;

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
     * @todo add doc
     *
     * @param string $content
     * @return string
     */
    public static function bootstrap(string $content): string
    {
        return sprintf('<div class="page">%s</div>', $content);
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
        $messageHeader = sprintf('<div class="message__header">%s</div>', $header);
        $messageBody = sprintf('<div class="message__body">%s</div>', $body);
        $message = sprintf('<div class="message message--%s">%s%s</div>', $type, $messageHeader, $messageBody);

        return self::bootstrap($message);
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
