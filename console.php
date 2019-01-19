#!/usr/bin/env php
<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);

define("ROOT_PATH", __DIR__);
define("ASSETS_PATH", ROOT_PATH . "/assets");
include_once ROOT_PATH . '/vendor/autoload.php';

use EliasHaeussler\Api\Command\DatabaseExportCommand;
use EliasHaeussler\Api\Command\DatabaseMigrateCommand;
use EliasHaeussler\Api\Command\DatabaseSchemaCommand;
use EliasHaeussler\Api\Utility\ConsoleUtility;
use EliasHaeussler\Api\Utility\GeneralUtility;
use Symfony\Component\Console\Application;

// Load environment variables
GeneralUtility::loadEnvironment();

// Create application
$app = new Application("Elias Häußler API console", ConsoleUtility::getGitCommit());

// Register commands
$app->add(new DatabaseExportCommand());
$app->add(new DatabaseMigrateCommand());
$app->add(new DatabaseSchemaCommand());

// Run application
$app->run();
