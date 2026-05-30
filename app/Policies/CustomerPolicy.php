<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['owner', 'admin', 'dispatcher', 'bookkeeper']);
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->organization_id === $customer->organization_id
            && $user->hasRole(['owner', 'admin', 'dispatcher', 'bookkeeper']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['owner', 'admin', 'dispatcher']);
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->organization_id === $customer->organization_id
            && $user->hasRole(['owner', 'admin', 'dispatcher']);
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->organization_id === $customer->organization_id
            && $user->hasRole(['owner', 'admin']);
    }

    public function import(User $user): bool
    {
        return $user->hasRole(['owner', 'admin']);
    }
}
