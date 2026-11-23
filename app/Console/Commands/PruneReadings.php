<?php

namespace App\Console\Commands;

use App\Models\Reading;
use Illuminate\Console\Command;

class PruneReadings extends Command
{
    protected $signature = 'lightsense:prune {--days=30 : Keep readings from the last N days}';

    protected $description = 'Delete old readings so the database does not grow forever';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $deleted = Reading::where('recorded_at', '<', now()->subDays($days))->delete();

        $this->info("Deleted {$deleted} readings older than {$days} days.");

        return self::SUCCESS;
    }
}
