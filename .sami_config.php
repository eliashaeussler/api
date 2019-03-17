<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
define("SOURCE_PATH", __DIR__ . '/src/classes');
define("DOCS_DIR", __DIR__ . '/docs');

$iterator = \Symfony\Component\Finder\Finder::create()
    ->files()
    ->name('*.php')
    ->in(SOURCE_PATH);

return new \Sami\Sami($iterator, [
    'title' => 'elias-haeussler.de API',
    'build_dir' => DOCS_DIR . '/php',
    'cache_dir' => DOCS_DIR . '/cache',
]);
