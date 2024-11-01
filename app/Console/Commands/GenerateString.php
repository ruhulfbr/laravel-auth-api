<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class GenerateString extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'random-string';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'To generate random string';

    protected function configure(): void
    {
        $this->addArgument('length', InputArgument::REQUIRED, 'Length of string');
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $length = $this->argument('length') ?? 10;
        $string = Str::random($length);

        $this->output->writeln('Generated string: ' . $string);
    }

}
