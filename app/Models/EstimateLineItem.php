<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstimateLineItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'estimate_package_id',
        'item_id',
        'name',
        'description',
        'unit_price',
        'quantity',
        'is_taxable',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'quantity'   => 'decimal:3',
            'is_taxable' => 'boolean',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(EstimatePackage::class, 'estimate_package_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
