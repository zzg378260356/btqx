<?php

namespace App\Filament\Resources\RiskZones\Pages;

use App\Filament\Resources\RiskZones\RiskZoneResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRiskZone extends EditRecord
{
    protected static string $resource = RiskZoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
