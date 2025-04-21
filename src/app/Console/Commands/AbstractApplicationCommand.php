<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Console\ApplicationOutput;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractApplicationCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->laravel->get(ApplicationOutput::class)->replace($this->output);

        return parent::execute($input, $output);
    }
}
