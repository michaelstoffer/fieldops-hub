<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;
    const STATUS_TRIALING = 'trialing';
    const STATUS_ACTIVE   = 'active';
    const STATUS_PAST_DUE = 'past_due';
    const STATUS_CANCELED = 'canceled';
    const STATUS_PAUSED   = 'paused';

    protected $fillable = [
        'organization_id',
        'plan',
        'status',
        'billing_interval',
        'stripe_subscription_id',
        'stripe_price_id',
        'trial_ends_at',
        'current_period_start',
        'current_period_end',
        'canceled_at',
    ];

    protected $casts = [
        'trial_ends_at'        => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end'   => 'datetime',
        'canceled_at'          => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function isTrialing(): bool
    {
        return $this->status === self::STATUS_TRIALING
            && $this->trial_ends_at
            && $this->trial_ends_at->isFuture();
    }

    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_TRIALING])
            && (! $this->trial_ends_at || $this->trial_ends_at->isFuture());
    }

    public function trialDaysRemaining(): int
    {
        if (! $this->trial_ends_at) {
            return 0;
        }

        return max(0, (int) now()->diffInDays($this->trial_ends_at, false));
    }
}
