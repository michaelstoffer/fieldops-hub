<?php

namespace App\Console\Commands;

use App\Models\Estimate;
use Illuminate\Console\Command;

class MarkEstimatesExpired extends Command
{
    protected $signature = 'estimates:mark-expired';

    protected $description = 'Transition sent estimates past their expiry date to expired status';

    public function handle(): int
    {
        $count = Estimate::where('status', Estimate::STATUS_SENT)
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '<', today())
            ->update(['status' => Estimate::STATUS_EXPIRED]);

        $this->info("Marked {$count} estimate(s) as expired.");

        return self::SUCCESS;
    }
}
