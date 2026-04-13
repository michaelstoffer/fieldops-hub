<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoundingMemberCoupon extends Model
{
    protected $fillable = [
        'code',
        'description',
        'discount_percent',
        'max_uses',
        'uses',
        'active',
        'expires_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function isAvailable(): bool
    {
        if (! $this->active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return $this->uses < $this->max_uses;
    }

    public function remainingUses(): int
    {
        return max(0, $this->max_uses - $this->uses);
    }

    public function incrementUses(): void
    {
        $this->increment('uses');

        if ($this->uses >= $this->max_uses) {
            $this->update(['active' => false]);
        }
    }
}
