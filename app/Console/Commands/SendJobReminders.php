<?php

namespace App\Console\Commands;

use App\Models\Job;
use App\Services\MessageDispatcher;
use App\Services\TemplateRenderer;
use Illuminate\Console\Command;

class SendJobReminders extends Command
{
    protected $signature = 'jobs:send-reminders
                            {--hours=24 : Send reminder this many hours before the scheduled time}';

    protected $description = 'Send reminder messages to customers for upcoming jobs';

    public function __construct(
        private readonly MessageDispatcher $dispatcher,
        private readonly TemplateRenderer $renderer,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $hours = (int) $this->option('hours');

        $jobs = Job::with(['customer'])
            ->whereIn('status', [Job::STATUS_SCHEDULED, Job::STATUS_EN_ROUTE])
            ->whereBetween('scheduled_at', [
                now()->addHours($hours - 1),
                now()->addHours($hours),
            ])
            ->get();

        $count = 0;

        foreach ($jobs as $job) {
            if (blank($job->customer)) {
                continue;
            }

            // Email reminder
            if (filled($job->customer->email)) {
                $rendered = $this->renderer->render($job->organization_id, 'job_reminder', 'email', $job);
                if ($rendered) {
                    $this->dispatcher->sendEmail(
                        $job,
                        'job_reminder',
                        $job->customer->email,
                        $rendered['subject'],
                        'mail.job-confirmation',
                        ['rendered_body' => $rendered['body']],
                    );
                    $count++;
                }
            }

            // SMS reminder
            $phone = $job->customer->mobile ?? $job->customer->phone;
            if (filled($phone)) {
                $rendered = $this->renderer->render($job->organization_id, 'job_reminder', 'sms', $job);
                if ($rendered) {
                    $this->dispatcher->sendSms($job, 'job_reminder', $phone, $rendered['body']);
                    $count++;
                }
            }
        }

        $this->info("Sent {$count} job reminder message(s) for {$jobs->count()} job(s).");

        return self::SUCCESS;
    }
}
