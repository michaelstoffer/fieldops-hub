<?php

namespace App\Policies;

use App\Models\Job;
use App\Models\User;

class JobPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['owner', 'admin', 'dispatcher', 'bookkeeper']);
    }

    public function view(User $user, Job $job): bool
    {
        return $user->organization_id === $job->organization_id
            && $user->hasRole(['owner', 'admin', 'dispatcher', 'bookkeeper']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['owner', 'admin', 'dispatcher']);
    }

    public function update(User $user, Job $job): bool
    {
        return $user->organization_id === $job->organization_id
            && $user->hasRole(['owner', 'admin', 'dispatcher']);
    }

    public function delete(User $user, Job $job): bool
    {
        return $user->organization_id === $job->organization_id
            && $user->hasRole(['owner', 'admin']);
    }

    public function generateInvoice(User $user, Job $job): bool
    {
        return $user->organization_id === $job->organization_id
            && $user->hasRole(['owner', 'admin', 'bookkeeper']);
    }
}
