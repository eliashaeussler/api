<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Helpers;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Extended output style for Symfony console.
 * 
 * @package EliasHaeussler\Api\Helpers
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
 */
class ExtendedStyle extends SymfonyStyle
{
    /**
     * {@inheritdoc}
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        parent::__construct($input, $output);

        // Add output styles
        $this->registerCustomOutputStyles($output);
    }

    /**
     * Register custom output styles.
     *
     * @param OutputInterface $output The output interface
     */
    protected function registerCustomOutputStyles(OutputInterface $output)
    {
        $outputStyle = new OutputFormatterStyle('blue', null, ['bold']);
        $output->getFormatter()->setStyle("param", $outputStyle);
    }

    /**
     * Formats a notice.
     *
     * @param array|string $message The notice message
     * @param string $prefix Optional first line message
     */
    public function notice($message, string $prefix = "")
    {
        $message = is_array($message) ? array_merge([$prefix], $message) : [$prefix, $message];
        $this->block($message, null, "fg=black;bg=yellow", "  ", true, false);
    }
}
