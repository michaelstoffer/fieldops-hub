<?php

namespace App\Listeners;

use App\Events\JobCreated;
use App\Services\MessageDispatcher;
use App\Services\TemplateRenderer;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendJobConfirmationEmail implements ShouldQueue
{
    public function __construct(
        private readonly MessageDispatcher $dispatcher,
        private readonly TemplateRenderer $renderer,
    ) {}

    public function handle(JobCreated $event): void
    {
        $job = $event->job;
        $job->loadMissing('customer');

        if (blank($job->customer?->email)) {
            return;
        }

        $rendered = $this->renderer->render($job->organization_id, 'job_scheduled', 'email', $job);
        if (! $rendered) {
            return;
        }

        $this->dispatcher->sendEmail(
            $job,
            'job_scheduled',
            $job->customer->email,
            $rendered['subject'],
            'mail.job-confirmation',
            ['rendered_body' => $rendered['body']],
        );
    }
}
