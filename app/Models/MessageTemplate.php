<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageTemplate extends Model
{
    protected $fillable = [
        'organization_id',
        'event',
        'channel',
        'subject',
        'body',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Supported events and their human-readable labels.
     */
    public static function events(): array
    {
        return [
            'job_scheduled' => 'Job Scheduled',
            'job_reminder'  => 'Job Reminder (24h before)',
            'en_route'      => 'Technician En Route',
            'job_completed' => 'Job Completed',
        ];
    }

    /**
     * Available template variables with descriptions.
     */
    public static function variables(): array
    {
        return [
            '{{customer_name}}'  => 'Customer full name',
            '{{job_title}}'      => 'Job title',
            '{{job_date}}'       => 'Scheduled date & time',
            '{{technician_name}}' => 'Assigned technician name',
            '{{company_name}}'   => 'Your company name',
        ];
    }

    /**
     * Render the body by substituting variables.
     */
    public function render(array $vars): string
    {
        return str_replace(array_keys($vars), array_values($vars), $this->body);
    }

    /**
     * Render the subject by substituting variables.
     */
    public function renderSubject(array $vars): string
    {
        return str_replace(array_keys($vars), array_values($vars), $this->subject ?? '');
    }
}
