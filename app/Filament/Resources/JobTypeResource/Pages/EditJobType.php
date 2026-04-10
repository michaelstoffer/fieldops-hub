<?php

namespace App\Filament\Resources\JobTypeResource\Pages;

use App\Filament\Resources\JobTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJobType extends EditRecord
{
    protected static string $resource = JobTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
