<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */

define("ROOT_PATH", dirname(__DIR__));
define("SOURCE_PATH", ROOT_PATH . "/src");
require ROOT_PATH . '/vendor/autoload.php';

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

    if ($e instanceof DBALException && !GeneralUtility::isDebugEnabled()) {
        $e = new DatabaseException(
            sprintf(
                "Sorry, but there was a problem during interaction with the database in %s:%s.",
                basename($e->getFile()),
                $e->getLine()
            ), 1546801779
        );
    }

    try {
        // Use custom exception handler if debugging is enabled
        if (GeneralUtility::isDebugEnabled() && class_exists("\\Whoops\\Run")) {
            GeneralUtility::registerExceptionHandler();
            GeneralUtility::makeInstance(\Whoops\Run::class)->handleException($e);
        }

        if (isset($router) && ($controller = $router->getController())) {
            echo $controller->buildMessage(Message::MESSAGE_TYPE_ERROR, $e);
        } else {
            /** @var Message $message */
            $message = GeneralUtility::makeInstance(Message::class);
            echo $message->error($e);
        }

    } catch (ClassNotFoundException $e) {
        echo $e->getMessage();
        if (GeneralUtility::isDebugEnabled()) {
            echo PHP_EOL . $e->getTraceAsString();
        }
    }

}
