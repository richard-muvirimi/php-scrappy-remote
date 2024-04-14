<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class Redocly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:redocly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate project doumentation html';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // phpcs:ignore Generic.Files.LineLength.TooLong
        Process::run('npx @redocly/cli build-docs -o resources/views/documentation.blade.php resources/docs/api-documentation.yaml');
    }
}
