<?php

namespace App\Listeners;

use App\Events\JobStatusChanged;
use App\Models\Job;
use App\Services\MessageDispatcher;
use App\Services\TemplateRenderer;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendJobStatusMessages implements ShouldQueue
{
    public function __construct(
        private readonly MessageDispatcher $dispatcher,
        private readonly TemplateRenderer $renderer,
    ) {}

    public function handle(JobStatusChanged $event): void
    {
        $job = $event->job;
        $job->loadMissing('customer');

        match ($event->newStatus) {
            Job::STATUS_EN_ROUTE  => $this->sendEnRoute($job),
            Job::STATUS_COMPLETED => $this->sendCompleted($job),
            default               => null,
        };
    }

    private function sendEnRoute(Job $job): void
    {
        $phone = $job->customer?->mobile ?? $job->customer?->phone;
        if (blank($phone)) {
            return;
        }

        $rendered = $this->renderer->render($job->organization_id, 'en_route', 'sms', $job);
        if (! $rendered) {
            return;
        }

        $this->dispatcher->sendSms($job, 'en_route', $phone, $rendered['body']);
    }

    private function sendCompleted(Job $job): void
    {
        // Email
        if (filled($job->customer?->email)) {
            $rendered = $this->renderer->render($job->organization_id, 'job_completed', 'email', $job);
            if ($rendered) {
                $this->dispatcher->sendEmail(
                    $job,
                    'job_completed',
                    $job->customer->email,
                    $rendered['subject'],
                    'mail.job-completed',
                    ['rendered_body' => $rendered['body']],
                );
            }
        }

        // SMS
        $phone = $job->customer?->mobile ?? $job->customer?->phone;
        if (filled($phone)) {
            $rendered = $this->renderer->render($job->organization_id, 'job_completed', 'sms', $job);
            if ($rendered) {
                $this->dispatcher->sendSms($job, 'job_completed', $phone, $rendered['body']);
            }
        }
    }
}
