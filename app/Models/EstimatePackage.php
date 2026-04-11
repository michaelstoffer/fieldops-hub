<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class EstimatePackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'estimate_id',
        'tier',
        'label',
        'description',
        'subtotal',
        'tax_amount',
        'total',
        'is_recommended',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'       => 'decimal:2',
            'tax_amount'     => 'decimal:2',
            'total'          => 'decimal:2',
            'is_recommended' => 'boolean',
        ];
    }

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(EstimateLineItem::class)->orderBy('sort_order');
    }

    public function recalculate(): void
    {
        $taxRate  = (float) ($this->estimate->tax_rate ?? 0);
        $subtotal = $this->lineItems()->sum(DB::raw('unit_price * quantity'));
        $taxable  = $this->lineItems()->where('is_taxable', true)->sum(DB::raw('unit_price * quantity'));

        $this->update([
            'subtotal'   => $subtotal,
            'tax_amount' => round($taxable * $taxRate, 2),
            'total'      => round($subtotal + ($taxable * $taxRate), 2),
        ]);
    }
}
