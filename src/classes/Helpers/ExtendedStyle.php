<?php
/**
 * Copyright (c) 2019 Elias Häußler <elias@haeussler.dev>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Helpers;

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

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Extended output style for Symfony console.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0+
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
     * Formats a notice.
     *
     * @param array|string $message The notice message
     * @param string       $prefix  Optional first line message
     */
    public function notice($message, string $prefix = "")
    {
        if ($prefix) {
            $message = is_array($message) ? array_merge([$prefix], $message) : [$prefix, $message];
        }
        $this->block($message, null, "fg=black;bg=yellow", "  ", true, false);
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
}
