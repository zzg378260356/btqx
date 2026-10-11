<?php

namespace App\Filament\Resources\HazardPoints\Pages;

use App\Filament\Resources\HazardPoints\HazardPointResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHazardPoint extends EditRecord
{
    protected static string $resource = HazardPointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
