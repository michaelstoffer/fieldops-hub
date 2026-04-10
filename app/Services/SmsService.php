<?php

namespace App\Services;

interface SmsService
{
    public function send(string $to, string $message): void;
}
