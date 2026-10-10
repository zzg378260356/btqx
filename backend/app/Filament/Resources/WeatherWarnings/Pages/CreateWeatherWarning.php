<?php

namespace App\Filament\Resources\WeatherWarnings\Pages;

use App\Filament\Resources\WeatherWarnings\WeatherWarningResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWeatherWarning extends CreateRecord
{
    protected static string $resource = WeatherWarningResource::class;
}
