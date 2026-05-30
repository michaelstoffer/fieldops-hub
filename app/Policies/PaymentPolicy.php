<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['owner', 'admin', 'bookkeeper']);
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->organization_id === $payment->organization_id
            && $user->hasRole(['owner', 'admin', 'bookkeeper']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['owner', 'admin', 'bookkeeper']);
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->organization_id === $payment->organization_id
            && $user->hasRole(['owner', 'admin']);
    }
}
