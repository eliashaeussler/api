<?php
/**
 * Copyright (c) 2019 Elias Häußler <mail@elias-haeussler.de>. All rights reserved.
 */
declare(strict_types=1);
namespace EliasHaeussler\Api\Command;

use EliasHaeussler\Api\Helpers\ExtendedStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base Symfony console command.
 *
 * @author Elias Häußler <mail@elias-haeussler.de>
 * @license MIT
 */
abstract class BaseCommand extends Command
{
    /** @var ExtendedStyle Custom output style */
    protected $io;

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->io = new ExtendedStyle($input, $output);
    }
}
