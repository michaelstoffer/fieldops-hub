<?php

namespace App\Console\Commands;

use App\Models\DriverLocation;
use Illuminate\Console\Command;

class PruneDriverLocations extends Command
{
    protected $signature = 'driver-locations:prune
                            {--days=7 : Delete location records older than this many days}';

    protected $description = 'Remove driver location records older than the retention period';

    public function handle(): int
    {
        $days    = (int) $this->option('days');
        $deleted = DriverLocation::where('recorded_at', '<', now()->subDays($days))->delete();

        $this->info("Pruned {$deleted} driver location record(s) older than {$days} day(s).");

        return self::SUCCESS;
    }
}
