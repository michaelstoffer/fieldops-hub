<?php

namespace App\Filament\Resources\JobTypeResource\Pages;

use App\Filament\Resources\JobTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJobType extends CreateRecord
{
    protected static string $resource = JobTypeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['organization_id'] = auth()->user()->organization_id;

        return $data;
    }
}
