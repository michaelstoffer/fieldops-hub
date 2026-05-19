<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Driver location updates — only authenticated users in the same organization can listen
Broadcast::channel('organizations.{organizationId}.driver-locations', function (User $user, int $organizationId) {
    return (int) $user->organization_id === $organizationId;
});
