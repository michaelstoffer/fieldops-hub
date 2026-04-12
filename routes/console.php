<?php

use App\Console\Commands\PruneDriverLocations;
use App\Console\Commands\SendInvoiceReminders;
use App\Console\Commands\SendJobReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(SendInvoiceReminders::class)->dailyAt('08:00');
Schedule::command(SendJobReminders::class)->hourly();
Schedule::command(PruneDriverLocations::class)->dailyAt('03:00');
