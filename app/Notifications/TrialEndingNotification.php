<?php

namespace App\Notifications;

use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialEndingNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Organization $organization,
        private readonly int $daysRemaining,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subscribeUrl = route('owner.subscription.index');
        $planLabel    = ucfirst($this->organization->plan);

        return (new MailMessage)
            ->subject("Your FieldOps Hub trial ends in {$this->daysRemaining} day".($this->daysRemaining === 1 ? '' : 's'))
            ->greeting("Hi {$notifiable->name},")
            ->line("Your 14-day free trial for **{$this->organization->name}** ends in **{$this->daysRemaining} day".($this->daysRemaining === 1 ? '' : 's')."**.")
            ->line("You're currently on the **{$planLabel}** plan. To keep your data and continue using FieldOps Hub without interruption, add a payment method before your trial expires.")
            ->action('Subscribe now', $subscribeUrl)
            ->line('No credit card was required to start your trial, but you\'ll need one to continue after the trial period.')
            ->line('Questions? Just reply to this email — we\'re happy to help.')
            ->salutation('— The FieldOps Hub Team');
    }
}
