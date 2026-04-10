<?php

namespace App\Mail;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Job $job) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Job Confirmation: {$this->job->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.job-confirmation',
        );
    }
}
