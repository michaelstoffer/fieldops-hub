<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\User;
use App\Notifications\TrialEndingNotification;
use Illuminate\Console\Command;

class SendTrialEndingReminders extends Command
{
    protected $signature = 'subscriptions:trial-reminders {--days=3 : Days before trial ends to send reminder}';

    protected $description = 'Send trial ending reminder emails to owners whose trial expires in N days';

    public function handle(): int
    {
        $days = (int) $this->option('days');

        // Find subscriptions whose trial ends in exactly N days (within the next 24h window)
        $subscriptions = Subscription::query()
            ->where('status', Subscription::STATUS_TRIALING)
            ->whereBetween('trial_ends_at', [
                now()->addDays($days)->startOfDay(),
                now()->addDays($days)->endOfDay(),
            ])
            ->with('organization.users')
            ->get();

        $this->info("Found {$subscriptions->count()} trial(s) ending in {$days} day(s).");

        foreach ($subscriptions as $subscription) {
            $org = $subscription->organization;

            if (! $org) {
                continue;
            }

            // Notify all owners for this organization
            $owners = $org->users()
                ->whereHas('roles', fn ($q) => $q->where('name', 'owner'))
                ->get();

            foreach ($owners as $owner) {
                $owner->notify(new TrialEndingNotification($org, $days));
                $this->line("  → Notified {$owner->email} ({$org->name})");
            }
        }

        $this->info('Done.');

        return self::SUCCESS;
    }
}
