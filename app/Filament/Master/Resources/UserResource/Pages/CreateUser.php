<?php

namespace App\Filament\Master\Resources\UserResource\Pages;

use App\Filament\Master\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
