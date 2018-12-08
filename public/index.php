<?php
/**
 * Copyright (c) 2018 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */

define("ROOT_PATH", dirname(__DIR__));
include_once ROOT_PATH . '/vendor/autoload.php';

use EliasHaeussler\Api\Routing\PageRouter;
use EliasHaeussler\Api\Utility\GeneralUtility;

try {
    $loader = new \Dotenv\Dotenv(ROOT_PATH);
    $loader->load();

    /** @var PageRouter $router */
    $router = GeneralUtility::makeInstance(PageRouter::class);
    $router->route();

} catch (\Exception $e) {
    echo \EliasHaeussler\Api\Page\Frontend::error($e);
}
