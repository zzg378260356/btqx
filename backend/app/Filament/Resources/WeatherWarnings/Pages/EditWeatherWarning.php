<?php

namespace App\Filament\Resources\WeatherWarnings\Pages;

use App\Filament\Resources\WeatherWarnings\WeatherWarningResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWeatherWarning extends EditRecord
{
    protected static string $resource = WeatherWarningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
