<?php
/**
 * Copyright (c) 2018 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */

include_once dirname(__DIR__) . '/vendor/autoload.php';

use EliasHaeussler\Api\Routing\PageRouter;
use EliasHaeussler\Api\Utility\GeneralUtility;

try {
    /** @var PageRouter $router */
    $router = GeneralUtility::makeInstance(PageRouter::class);
    $router->route();

} catch (\Exception $e) {
    echo \EliasHaeussler\Api\Page\Frontend::error($e);
}
