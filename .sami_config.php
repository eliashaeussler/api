<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
define("CLASSES_PATH", 'src/classes');
define("SOURCE_PATH", __DIR__ . '/' . CLASSES_PATH);
define("DOCS_DIR", __DIR__ . '/docs');

$files = [];

$iterator = \Symfony\Component\Finder\Finder::create()
    ->files()
    ->in(SOURCE_PATH);

// Use files from stdin in case they are defined
$stdin = fopen('php://stdin', 'r');
stream_set_blocking($stdin, false);
if ($stdin) {
    $files = explode(" ", trim(fgets($stdin)));
    $files = array_filter(array_map(function ($file) {
        return preg_replace(
            sprintf(
                "/^\\/?(%s|%s)\\/?/",
                str_replace("/", "\\/", SOURCE_PATH),
                str_replace("/", "\\/", CLASSES_PATH)
            ),
            "",
            $file
        );
    }, $files));
}
fclose($stdin);

// Use all PHP files if no files are defined by stdin
if (empty($files)) {
    $files[] = "*.php";
}

// Add files to iterator
foreach ($files as $file) {
    $iterator->path($file);
}

return new \Sami\Sami($iterator, [
    'title' => 'elias-haeussler.de API',
    'build_dir' => DOCS_DIR . '/php',
    'cache_dir' => DOCS_DIR . '/cache',
]);
