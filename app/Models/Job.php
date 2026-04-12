<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'field_jobs';

    const STATUS_SCHEDULED = 'scheduled';

    const STATUS_EN_ROUTE = 'en_route';

    const STATUS_IN_PROGRESS = 'in_progress';

    const STATUS_COMPLETED = 'completed';

    const STATUS_CANCELLED = 'cancelled';

    const STATUS_ON_HOLD = 'on_hold';

    protected $fillable = [
        'organization_id',
        'customer_id',
        'property_id',
        'job_type_id',
        'estimate_id',
        'assigned_to',
        'title',
        'description',
        'status',
        'scheduled_at',
        'started_at',
        'arrived_at',
        'completed_at',
        'cancelled_at',
        'technician_notes',
        'customer_notes',
        'office_notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'arrived_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function jobType(): BelongsTo
    {
        return $this->belongsTo(JobType::class);
    }

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }

    public function assignedTechnician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(JobLineItem::class)->orderBy('sort_order');
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(JobChecklistItem::class)->orderBy('sort_order');
    }

    protected static function booted(): void
    {
        static::created(function (Job $job) {
            if (! $job->job_type_id) {
                return;
            }

            $templateItems = JobTypeChecklistItem::where('job_type_id', $job->job_type_id)
                ->orderBy('sort_order')
                ->get();

            foreach ($templateItems as $template) {
                $job->checklistItems()->create([
                    'job_type_checklist_item_id' => $template->id,
                    'label' => $template->label,
                    'sort_order' => $template->sort_order,
                    'is_required' => $template->is_required,
                ]);
            }
        });
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(JobMessage::class)->orderByDesc('created_at');
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_EN_ROUTE => 'En Route',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_ON_HOLD => 'On Hold',
        ];
    }
}
