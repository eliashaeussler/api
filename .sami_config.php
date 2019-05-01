<?php
/**
 * Copyright (c) 2019 Elias Häußler <elias@haeussler.dev>. All rights reserved.
 */
define('CLASSES_PATH', 'src');
define('SOURCE_PATH', __DIR__ . '/' . CLASSES_PATH);
define('DOCS_DIR', __DIR__ . '/docs');

$iterator = \Symfony\Component\Finder\Finder::create()
    ->files()
    ->name('*.php')
    ->in(SOURCE_PATH);

$versions = (new \Sami\Version\GitVersionCollection(__DIR__))
    ->addFromTags(function ($version) {
        return strpos($version, '0') != 0;
    })
    ->add('master');

return new \Sami\Sami($iterator, [
    'versions' => $versions,
    'title' => 'elias-haeussler.de API',
    'build_dir' => DOCS_DIR . '/php/%version%',
    'cache_dir' => DOCS_DIR . '/cache/%version%',
    'remote_repository' => new \Sami\RemoteRepository\GitHubRemoteRepository('eliashaeussler/api', __DIR__),
    'default_opened_level' => 2,
]);
