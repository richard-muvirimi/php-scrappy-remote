<?php

namespace App\Console\Commands;

use App\Models\Content;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class RemoveOld extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Content::query()
            ->where('type', '=', Content::TYPE_SITE)
            ->where('created_at', '<', Carbon::now()->subWeeks(5))
            ->delete();
    }
}
