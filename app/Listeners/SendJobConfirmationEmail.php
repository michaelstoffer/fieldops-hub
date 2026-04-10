<?php

namespace App\Listeners;

use App\Events\JobCreated;
use App\Mail\JobConfirmationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendJobConfirmationEmail implements ShouldQueue
{
    public function handle(JobCreated $event): void
    {
        $job = $event->job;
        $job->loadMissing('customer');

        if (blank($job->customer->email)) {
            return;
        }

        Mail::to($job->customer->email)
            ->queue(new JobConfirmationMail($job));
    }
}
