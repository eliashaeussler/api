#!/usr/bin/env php
<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);

define("ROOT_PATH", __DIR__);
define("ASSETS_PATH", ROOT_PATH . "/assets");
include_once ROOT_PATH . '/vendor/autoload.php';

use EliasHaeussler\Api\Command\DatabaseCommand;
use EliasHaeussler\Api\Utility\GeneralUtility;
use Symfony\Component\Console\Application;

// Create application
$app = new Application("Elias Häußler API console", GeneralUtility::getGitCommit());

// Register commands
$app->add(new DatabaseCommand());

// Run application
$app->run();
