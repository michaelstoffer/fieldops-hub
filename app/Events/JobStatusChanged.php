<?php

namespace App\Events;

use App\Models\Job;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Job $job,
        public readonly string $oldStatus,
        public readonly string $newStatus,
    ) {}
}
