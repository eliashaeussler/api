<?php
/**
 * Copyright (c) 2019 Elias Häußler <elias@haeussler.dev>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Command;

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

use EliasHaeussler\Api\Helpers\ExtendedStyle;
use EliasHaeussler\Api\Service\LogService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base Symfony console command.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0+
 */
abstract class BaseCommand extends Command
{
    /** @var ExtendedStyle Custom output style */
    protected $io;

    /**
     * Write log message from LogService to console.
     *
     * @param string $message  The log message
     * @param int    $severity Severity of the message
     */
    public function log(string $message, int $severity): void
    {
        switch ($severity) {
            case LogService::SUCCESS:
            case LogService::DEBUG:
            case LogService::NOTICE:
                $this->io->write($message);
                break;

            case LogService::WARNING:
                $this->io->warning($message);
                break;

            case LogService::ERROR:
                $this->io->error($message);
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        // Set IO style
        $this->io = new ExtendedStyle($input, $output);

        // Register CLI command within log service
        LogService::setConsoleCommandInstance($this);

        // Set verbosity level
        LogService::setCliLogLevel([
            OutputInterface::VERBOSITY_NORMAL => LogService::WARNING,
            OutputInterface::VERBOSITY_VERBOSE => LogService::NOTICE,
            OutputInterface::VERBOSITY_VERY_VERBOSE => LogService::DEBUG,
            OutputInterface::VERBOSITY_DEBUG => LogService::SUCCESS,
        ][$this->io->getVerbosity()]);
    }
}
