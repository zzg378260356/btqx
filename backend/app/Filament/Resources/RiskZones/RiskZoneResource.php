<?php

namespace App\Filament\Resources\RiskZones;

use App\Filament\Resources\RiskZones\Pages\CreateRiskZone;
use App\Filament\Resources\RiskZones\Pages\EditRiskZone;
use App\Filament\Resources\RiskZones\Pages\ListRiskZones;
use App\Filament\Resources\RiskZones\Schemas\RiskZoneForm;
use App\Filament\Resources\RiskZones\Tables\RiskZonesTable;
use App\Models\RiskZone;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RiskZoneResource extends Resource
{
    protected static ?string $model = RiskZone::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = '风险区';

    protected static ?string $pluralModelLabel = '风险区管理';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermission('warnings.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasPermission('warnings.manage') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return RiskZoneForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RiskZonesTable::configure($table);
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
            'index' => ListRiskZones::route('/'),
            'create' => CreateRiskZone::route('/create'),
            'edit' => EditRiskZone::route('/{record}/edit'),
        ];
    }
}
