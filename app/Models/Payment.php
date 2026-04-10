<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    const METHOD_CASH = 'cash';
    const METHOD_CHECK = 'check';
    const METHOD_CARD = 'card';
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_STRIPE = 'stripe';

    protected $fillable = [
        'organization_id',
        'invoice_id',
        'recorded_by',
        'amount',
        'method',
        'reference',
        'stripe_payment_intent_id',
        'status',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
