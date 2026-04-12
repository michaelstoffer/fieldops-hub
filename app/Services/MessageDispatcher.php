<?php

namespace App\Services;

use App\Models\Job;
use App\Models\JobMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MessageDispatcher
{
    public function __construct(private readonly SmsService $sms) {}

    public function sendEmail(Job $job, string $event, string $to, string $subject, string $view, array $data = []): void
    {
        try {
            Mail::send($view, array_merge($data, ['job' => $job]), function ($m) use ($to, $subject) {
                $m->to($to)->subject($subject);
            });

            $this->log($job, $event, 'email', $to, $subject, 'sent');
        } catch (\Throwable $e) {
            Log::warning("MessageDispatcher email failed [{$event}]", ['error' => $e->getMessage()]);
            $this->log($job, $event, 'email', $to, $subject, 'failed', $e->getMessage());
        }
    }

    public function sendSms(Job $job, string $event, string $to, string $body): void
    {
        try {
            $this->sms->send($to, $body);
            $this->log($job, $event, 'sms', $to, $body, 'sent');
        } catch (\Throwable $e) {
            Log::warning("MessageDispatcher SMS failed [{$event}]", ['error' => $e->getMessage()]);
            $this->log($job, $event, 'sms', $to, $body, 'failed', $e->getMessage());
        }
    }

    private function log(Job $job, string $event, string $channel, string $recipient, string $body, string $status, ?string $error = null): void
    {
        JobMessage::create([
            'job_id'      => $job->id,
            'customer_id' => $job->customer_id,
            'channel'     => $channel,
            'event'       => $event,
            'recipient'   => $recipient,
            'body'        => $body,
            'status'      => $status,
            'error'       => $error,
        ]);
    }
}
