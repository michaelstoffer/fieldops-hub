<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobMessage extends Model
{
    protected $fillable = [
        'job_id',
        'customer_id',
        'channel',
        'event',
        'recipient',
        'body',
        'status',
        'error',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
