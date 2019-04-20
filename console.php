#!/usr/bin/env php
<?php
/**
 * Copyright (c) 2019 Elias Häußler <elias@haeussler.dev>. All rights reserved.
 */
declare(strict_types=1);

define('ROOT_PATH', __DIR__);
define('SOURCE_PATH', ROOT_PATH . '/src');
define('TEMP_PATH', ROOT_PATH . '/temp');
require ROOT_PATH . '/vendor/autoload.php';

use EliasHaeussler\Api\Command\DatabaseExportCommand;
use EliasHaeussler\Api\Command\DatabaseMigrateCommand;
use EliasHaeussler\Api\Command\DatabaseSchemaCommand;
use EliasHaeussler\Api\Command\LogfileClearCommand;
use EliasHaeussler\Api\Command\SchedulerRunCommand;
use EliasHaeussler\Api\Utility\ConsoleUtility;
use EliasHaeussler\Api\Utility\GeneralUtility;
use Symfony\Component\Console\Application;

// Load environment variables
GeneralUtility::loadEnvironment();

// Create application
$app = new Application('Elias Häußler API console', ConsoleUtility::describeHistory(ConsoleUtility::HISTORY_TYPE_VERSION));

// Register commands
$app->add(new DatabaseExportCommand());
$app->add(new DatabaseMigrateCommand());
$app->add(new DatabaseSchemaCommand());
$app->add(new LogfileClearCommand());
$app->add(new SchedulerRunCommand());

// Run application
/** @noinspection PhpUnhandledExceptionInspection */
$app->run();
