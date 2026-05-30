<?php

namespace App\Policies;

use App\Models\Estimate;
use App\Models\User;

class EstimatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['owner', 'admin', 'dispatcher', 'bookkeeper']);
    }

    public function view(User $user, Estimate $estimate): bool
    {
        return $user->organization_id === $estimate->organization_id
            && $user->hasRole(['owner', 'admin', 'dispatcher', 'bookkeeper']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['owner', 'admin', 'dispatcher']);
    }

    public function update(User $user, Estimate $estimate): bool
    {
        return $user->organization_id === $estimate->organization_id
            && $user->hasRole(['owner', 'admin', 'dispatcher']);
    }

    public function delete(User $user, Estimate $estimate): bool
    {
        return $user->organization_id === $estimate->organization_id
            && $user->hasRole(['owner', 'admin']);
    }

    public function send(User $user, Estimate $estimate): bool
    {
        return $user->organization_id === $estimate->organization_id
            && $user->hasRole(['owner', 'admin', 'dispatcher']);
    }

    public function convertToJob(User $user, Estimate $estimate): bool
    {
        return $user->organization_id === $estimate->organization_id
            && $user->hasRole(['owner', 'admin', 'dispatcher']);
    }
}
