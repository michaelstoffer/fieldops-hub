<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['owner', 'admin', 'dispatcher', 'bookkeeper']);
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $user->organization_id === $invoice->organization_id
            && $user->hasRole(['owner', 'admin', 'dispatcher', 'bookkeeper']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['owner', 'admin', 'bookkeeper']);
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->organization_id === $invoice->organization_id
            && $user->hasRole(['owner', 'admin', 'bookkeeper']);
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->organization_id === $invoice->organization_id
            && $invoice->status === Invoice::STATUS_DRAFT
            && $user->hasRole(['owner', 'admin']);
    }

    public function send(User $user, Invoice $invoice): bool
    {
        return $user->organization_id === $invoice->organization_id
            && $user->hasRole(['owner', 'admin', 'bookkeeper']);
    }

    public function void(User $user, Invoice $invoice): bool
    {
        return $user->organization_id === $invoice->organization_id
            && $user->hasRole(['owner', 'admin']);
    }

    public function recordPayment(User $user, Invoice $invoice): bool
    {
        return $user->organization_id === $invoice->organization_id
            && $user->hasRole(['owner', 'admin', 'bookkeeper']);
    }
}
