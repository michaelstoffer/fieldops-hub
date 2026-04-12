<?php

namespace App\Services;

use App\Models\Job;
use App\Models\MessageTemplate;

class TemplateRenderer
{
    /**
     * Build the variable map for a given job.
     */
    public function variables(Job $job): array
    {
        $job->loadMissing(['customer', 'assignedTechnician']);

        $customerName   = $job->customer
            ? "{$job->customer->first_name} {$job->customer->last_name}"
            : 'Valued Customer';

        $technicianName = $job->assignedTechnician?->name ?? 'Your technician';

        $jobDate = $job->scheduled_at
            ? $job->scheduled_at->format('l, F j \a\t g:i A')
            : 'TBD';

        return [
            '{{customer_name}}'   => $customerName,
            '{{job_title}}'       => $job->title,
            '{{job_date}}'        => $jobDate,
            '{{technician_name}}' => $technicianName,
            '{{company_name}}'    => config('app.name'),
        ];
    }

    /**
     * Fetch the active template for an org/event/channel and render it,
     * falling back to a hardcoded default when no template is configured.
     *
     * @return array{subject: string, body: string}|null  null = channel disabled
     */
    public function render(int $orgId, string $event, string $channel, Job $job): ?array
    {
        $template = MessageTemplate::where('organization_id', $orgId)
            ->where('event', $event)
            ->where('channel', $channel)
            ->where('is_active', true)
            ->first();

        $vars = $this->variables($job);

        if ($template) {
            return [
                'subject' => $template->renderSubject($vars),
                'body'    => $template->render($vars),
            ];
        }

        // Hardcoded defaults — used when no template row exists yet
        return $this->defaults($event, $channel, $vars);
    }

    private function defaults(string $event, string $channel, array $vars): ?array
    {
        $cn  = $vars['{{customer_name}}'];
        $jt  = $vars['{{job_title}}'];
        $jd  = $vars['{{job_date}}'];
        $tn  = $vars['{{technician_name}}'];
        $co  = $vars['{{company_name}}'];

        return match ("{$event}:{$channel}") {
            'job_scheduled:email' => [
                'subject' => "Appointment Confirmed: {$jt}",
                'body'    => "Hi {$cn},\n\nYour appointment \"{$jt}\" is confirmed for {$jd}.\n\nThanks,\n{$co}",
            ],
            'job_scheduled:sms' => [
                'subject' => '',
                'body'    => "Hi {$cn}, your appointment \"{$jt}\" is confirmed for {$jd}. Reply STOP to opt out.",
            ],
            'job_reminder:email' => [
                'subject' => "Reminder: {$jt} Tomorrow",
                'body'    => "Hi {$cn},\n\nThis is a reminder that \"{$jt}\" is scheduled for {$jd}.\n\nThanks,\n{$co}",
            ],
            'job_reminder:sms' => [
                'subject' => '',
                'body'    => "Reminder: \"{$jt}\" is tomorrow at {$jd}. Reply STOP to opt out.",
            ],
            'en_route:sms' => [
                'subject' => '',
                'body'    => "Hi {$cn}, {$tn} is on the way to your appointment. See you soon!",
            ],
            'job_completed:email' => [
                'subject' => "Service Complete: {$jt}",
                'body'    => "Hi {$cn},\n\nYour service \"{$jt}\" has been completed. Thank you for choosing {$co}!\n\nThanks,\n{$co}",
            ],
            'job_completed:sms' => [
                'subject' => '',
                'body'    => "Hi {$cn}, your service \"{$jt}\" is complete. Thank you for choosing {$co}!",
            ],
            default => null,
        };
    }
}
