<?php

namespace App\Filament\Resources\WeatherWarnings\Pages;

use App\Filament\Resources\WeatherWarnings\WeatherWarningResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWeatherWarnings extends ListRecords
{
    protected static string $resource = WeatherWarningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
