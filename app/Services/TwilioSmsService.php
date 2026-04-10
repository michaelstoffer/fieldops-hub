<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioSmsService implements SmsService
{
    private Client $client;
    private string $from;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token'),
        );

        $this->from = config('services.twilio.from');
    }

    public function send(string $to, string $message): void
    {
        $this->client->messages->create($to, [
            'from' => $this->from,
            'body' => $message,
        ]);
    }
}
