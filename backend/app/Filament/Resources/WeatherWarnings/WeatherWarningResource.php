<?php

namespace App\Filament\Resources\WeatherWarnings;

use App\Filament\Resources\WeatherWarnings\Pages\CreateWeatherWarning;
use App\Filament\Resources\WeatherWarnings\Pages\EditWeatherWarning;
use App\Filament\Resources\WeatherWarnings\Pages\ListWeatherWarnings;
use App\Filament\Resources\WeatherWarnings\Schemas\WeatherWarningForm;
use App\Filament\Resources\WeatherWarnings\Tables\WeatherWarningsTable;
use App\Models\WeatherWarning;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WeatherWarningResource extends Resource
{
    protected static ?string $model = WeatherWarning::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = '预警';

    protected static ?string $pluralModelLabel = '预警管理';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return WeatherWarningForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WeatherWarningsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWeatherWarnings::route('/'),
            'create' => CreateWeatherWarning::route('/create'),
            'edit' => EditWeatherWarning::route('/{record}/edit'),
        ];
    }
}
