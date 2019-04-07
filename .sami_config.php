<?php
/**
 * Copyright (c) 2019 Elias Häußler <elias@haeussler.dev>. All rights reserved.
 */
define("CLASSES_PATH", 'src/classes');
define("SOURCE_PATH", __DIR__ . '/' . CLASSES_PATH);
define("DOCS_DIR", __DIR__ . '/docs');

$files = [];

$iterator = \Symfony\Component\Finder\Finder::create()
    ->files()
    ->name("*.php")
    ->in(SOURCE_PATH);

return new \Sami\Sami($iterator, [
    'title' => 'elias-haeussler.de API',
    'build_dir' => DOCS_DIR . '/php',
    'cache_dir' => DOCS_DIR . '/cache',
]);
