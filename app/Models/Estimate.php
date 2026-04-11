<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Estimate extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_DRAFT    = 'draft';
    const STATUS_SENT     = 'sent';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_DECLINED = 'declined';
    const STATUS_EXPIRED  = 'expired';

    const TIERS = ['good', 'better', 'best'];

    protected $fillable = [
        'organization_id',
        'customer_id',
        'job_id',
        'estimate_number',
        'title',
        'intro',
        'footer',
        'status',
        'token',
        'expires_at',
        'sent_at',
        'accepted_at',
        'accepted_package',
        'declined_at',
        'tax_rate',
    ];

    protected function casts(): array
    {
        return [
            'tax_rate'    => 'decimal:4',
            'expires_at'  => 'date',
            'sent_at'     => 'datetime',
            'accepted_at' => 'datetime',
            'declined_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Estimate $estimate) {
            if (empty($estimate->token)) {
                $estimate->token = Str::random(48);
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(EstimatePackage::class)->orderByRaw("CASE tier WHEN 'good' THEN 1 WHEN 'better' THEN 2 WHEN 'best' THEN 3 END");
    }

    public function convertedJob(): HasOne
    {
        return $this->hasOne(Job::class, 'estimate_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast() && $this->status === self::STATUS_SENT;
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT    => 'Draft',
            self::STATUS_SENT     => 'Sent',
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_DECLINED => 'Declined',
            self::STATUS_EXPIRED  => 'Expired',
        ];
    }
}
