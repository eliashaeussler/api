<?php
/**
 * Copyright (c) 2018 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */

define("ROOT_PATH", dirname(__DIR__));
define("ASSETS_PATH", ROOT_PATH . "/assets");
include_once ROOT_PATH . '/vendor/autoload.php';

use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Frontend\Message;
use EliasHaeussler\Api\Service\RoutingService;
use EliasHaeussler\Api\Utility\GeneralUtility;

try {
    /** @var RoutingService $router */
    $router = GeneralUtility::makeInstance(RoutingService::class);
    $router->route();

} catch (\Exception $e) {
    try {
        if (isset($router) && ($controller = $router->getController())) {
            echo $controller->buildMessage(Message::MESSAGE_TYPE_ERROR, $e);
        } else {
            /** @var Message $message */
            $message = GeneralUtility::makeInstance(Message::class);
            echo $message->error($e);
        }

    } catch (ClassNotFoundException $e) {
        echo $e->getMessage();
    }
}
