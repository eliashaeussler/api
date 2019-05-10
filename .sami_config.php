<?php
/**
 * Copyright (c) 2019 Elias Häußler <elias@haeussler.dev>. All rights reserved.
 */
define('ROOT_PATH', __DIR__);
define('SOURCE_PATH', ROOT_PATH . '/src');
define('DOCS_PATH', ROOT_PATH . '/docs');

$iterator = \Symfony\Component\Finder\Finder::create()
    ->files()
    ->name('*.php')
    ->in(SOURCE_PATH);

$versions = (new \Sami\Version\GitVersionCollection(ROOT_PATH))
    ->addFromTags(function ($version) {
        return ((int) ltrim($version, 'v')[0]) != 0;
    })
    ->add('master');

return new \Sami\Sami($iterator, [
    'versions' => $versions,
    'title' => 'elias-haeussler.de API',
    'build_dir' => DOCS_PATH . '/php/%version%',
    'cache_dir' => DOCS_PATH . '/cache/%version%',
    'remote_repository' => new \Sami\RemoteRepository\GitHubRemoteRepository('eliashaeussler/api', ROOT_PATH),
    'default_opened_level' => 2,
]);
