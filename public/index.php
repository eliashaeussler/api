<?php
/**
 * Copyright (c) 2018 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */

define("ROOT_PATH", dirname(__DIR__));
define("ASSETS_PATH", ROOT_PATH . "/assets");
include_once ROOT_PATH . '/vendor/autoload.php';

use EliasHaeussler\Api\Page\Frontend;
use EliasHaeussler\Api\Routing\PageRouter;
use EliasHaeussler\Api\Utility\GeneralUtility;

try {
    /** @var PageRouter $router */
    $router = GeneralUtility::makeInstance(PageRouter::class);
    $router->route();

} catch (\Exception $e) {
    $message = Frontend::error($e);

    if (isset($router) && ($controller = $router->getController())) {
        echo $controller->buildMessage($message, Frontend::MESSAGE_TYPE_ERROR);
    } else {
        echo $message;
    }
}
