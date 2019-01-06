<?php
/**
 * Copyright (c) 2018 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */

define("ROOT_PATH", dirname(__DIR__));
define("ASSETS_PATH", ROOT_PATH . "/assets");
include_once ROOT_PATH . '/vendor/autoload.php';

use Doctrine\DBAL\DBALException;
use EliasHaeussler\Api\Exception\ClassNotFoundException;
use EliasHaeussler\Api\Exception\DatabaseException;
use EliasHaeussler\Api\Frontend\Message;
use EliasHaeussler\Api\Service\RoutingService;
use EliasHaeussler\Api\Utility\GeneralUtility;

try {

    /** @var RoutingService $router */
    $router = GeneralUtility::makeInstance(RoutingService::class);
    $router->route();

} catch (\Exception $e) {

    if ($e instanceof DBALException) {
        $e = new DatabaseException(
            sprintf(
                "Sorry, but there was a problem during interaction with the database in %s:%s.",
                basename($e->getFile()),
                $e->getLine()
            ), 1546801779
        );
    }

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
