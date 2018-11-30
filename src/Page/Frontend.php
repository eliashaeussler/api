<?php
/**
 * Copyright (c) 2018 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */

namespace EliasHaeussler\Api\Page;

/**
 * @todo doc
 *
 * @package EliasHaeussler\Api\Page
 * @author Elias Häußler <mail@elias-haeussler.de>
 */
class Frontend
{
    public static function bootstrap(string $content): string
    {
        return sprintf('<div class="page">%s</div>', $content);
    }

    /**
     * @param \Exception $object
     * @return string
     */
    public static function error(\Exception $object): string
    {
        $err_header = sprintf('<div class="error__header">Error: %s</div>', get_class($object));
        $err_body = sprintf('<div class="error__body">%s [%s]</div>', $object->getMessage(), $object->getCode());
        $content = sprintf('<div class="error">%s%s</div>', $err_header, $err_body);

        return self::bootstrap($content);
    }
}
