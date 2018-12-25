<?php
/**
 * Copyright (c) 2018 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Page;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;

/**
 * @todo add doc
 *
 * @package EliasHaeussler\Api\Page
 * @author Elias Häußler <mail@elias-haeussler.de>
 */
class Template
{
    /** @var string Path containing Twig templates */
    const TEMPLATE_PATH = ROOT_PATH . "/templates";

    /** @var string File name of default Twig template */
    const TEMPLATE_FILE = "default.twig";

    /**
     * @var Environment Twig environment
     */
    protected $environment;

    /**
     * @var TemplateWrapper Twig template wrapper
     */
    protected $template;


    /**
     * @todo add doc
     */
    public function __construct()
    {
        $this->initializeTwig();
        $this->registerGlobals();
        $this->loadTemplate();
    }

    /**
     * @todo add doc
     *
     * @param array $parameters
     * @return string
     */
    public function renderTemplate(array $parameters = [])
    {
        return $this->template->render($parameters);
    }

    /**
     * @todo add doc
     */
    protected function initializeTwig()
    {
        $loader = new FilesystemLoader(self::TEMPLATE_PATH);
        $this->environment = new Environment($loader);
    }

    /**
     * @todo add doc
     */
    protected function registerGlobals()
    {
        if (!$this->environment) {
            return;
        }

        // Add Twig Globals
        $this->environment->addGlobal("title", "API – Elias Häußler");
    }

    /**
     * @todo add doc
     */
    protected function loadTemplate()
    {
        try {
            $this->template = $this->environment->load(self::TEMPLATE_FILE);
        } catch (\Twig_Error $e) {
            // Use plain HTML as fallback if Twig template cannot be loaded
        }
    }

}
