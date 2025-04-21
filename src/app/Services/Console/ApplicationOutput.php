<?php

declare(strict_types=1);

namespace App\Services\Console;

use App\Contracts\Console\ApplicationOutputInterface;
use Illuminate\Console\OutputStyle;

class ApplicationOutput implements ApplicationOutputInterface
{
    private ?OutputStyle $output = null;

    public function replace(OutputStyle $output): void
    {
        $this->output = $output;
    }

    public function info(string $message): void
    {
        $this->output->writeln("<info>$message</info>");
    }

    public function error(string $message): void
    {
        $this->output->error("$message");
    }

    public function warning(string $message): void
    {
        $this->output->warning("$message");
    }
}
