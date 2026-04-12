<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioSmsService implements SmsService
{
    public function send(string $to, string $message): void
    {
        $sid   = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from  = config('services.twilio.from');

        if (! $sid || ! $token || ! $from) {
            logger()->warning('Twilio SMS skipped: credentials not configured.', compact('to'));
            return;
        }

        (new Client($sid, $token))->messages->create($to, [
            'from' => $from,
            'body' => $message,
        ]);
    }
}
