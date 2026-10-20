<?php

namespace App\Filament\Resources\RiskZones\Pages;

use App\Filament\Resources\RiskZones\RiskZoneResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRiskZones extends ListRecords
{
    protected static string $resource = RiskZoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
