<?php
/**
 * Copyright (c) 2019 Elias Häußler <elias@haeussler.dev>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Frontend;

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

use EliasHaeussler\Api\Service\LogService;
use EliasHaeussler\Api\Utility\ConsoleUtility;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;

/**
 * Browser template rendering.
 *
 * This class contains the rendering of Twig templates. It will be used to render page content within a given layout.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0+
 */
class Template
{
    /** @var string Path containing Twig templates */
    const TEMPLATE_PATH = SOURCE_PATH . "/templates";

    /** @var string File name of default Twig template */
    const DEFAULT_TEMPLATE = "default.twig";

    /** @var string File name of Twig template */
    protected $file;

    /** @var Environment Twig environment */
    protected $environment;

    /** @var TemplateWrapper Twig template wrapper */
    protected $template;

    /**
     * Initialize template rendering with Twig.
     *
     * Initializes Twig as rendering engine, registers Twig Globals for use inside the template and prepares the
     * template for rendering by loading and storing it inside the class instance.
     *
     * @param string $file File name of Twig template
     *
     * @throws \Twig_Error if Twig template cannot be loaded
     */
    public function __construct(string $file = self::DEFAULT_TEMPLATE)
    {
        $this->file = $file;

        $this->initializeTwig();
        $this->loadTemplate();
    }

    /**
     * Render Twig template.
     *
     * @param array $parameters Additional parameters to pass to the template
     *
     * @return string The rendered template
     */
    public function renderTemplate(array $parameters = [])
    {
        LogService::log("Rendering Twig template", LogService::DEBUG);

        return $this->template->render($parameters);
    }

    /**
     * Initialize Twig environment and register Globals.
     */
    protected function initializeTwig()
    {
        LogService::log("Initializing Twig template", LogService::DEBUG);

        // Initialize environment
        $loader = new FilesystemLoader(self::TEMPLATE_PATH);
        $this->environment = new Environment($loader);

        // Register Globals
        $this->environment->addGlobal("year", date("Y"));
        $this->environment->addGlobal("commit", ConsoleUtility::describeHistory());
        $this->environment->addGlobal("version", ConsoleUtility::describeHistory(ConsoleUtility::HISTORY_TYPE_VERSION));
    }

    /**
     * Load Twig template.
     *
     * @throws \Twig_Error if Twig template cannot be loaded
     */
    protected function loadTemplate()
    {
        LogService::log(sprintf("Loading Twig template \"%s\"", $this->file), LogService::DEBUG);

        $this->template = $this->environment->load($this->file);
    }
}
