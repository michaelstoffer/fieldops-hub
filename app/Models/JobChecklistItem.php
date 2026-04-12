<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobChecklistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'job_type_checklist_item_id',
        'label',
        'sort_order',
        'is_required',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'sort_order' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(JobTypeChecklistItem::class, 'job_type_checklist_item_id');
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }
}
