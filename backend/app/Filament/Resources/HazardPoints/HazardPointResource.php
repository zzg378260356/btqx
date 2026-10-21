<?php

namespace App\Filament\Resources\HazardPoints;

use App\Filament\Resources\HazardPoints\Pages\CreateHazardPoint;
use App\Filament\Resources\HazardPoints\Pages\EditHazardPoint;
use App\Filament\Resources\HazardPoints\Pages\ListHazardPoints;
use App\Filament\Resources\HazardPoints\Schemas\HazardPointForm;
use App\Filament\Resources\HazardPoints\Tables\HazardPointsTable;
use App\Models\HazardPoint;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HazardPointResource extends Resource
{
    protected static ?string $model = HazardPoint::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = '隐患点';

    protected static ?string $pluralModelLabel = '隐患点管理';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermission('hazards.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasPermission('hazards.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasPermission('hazards.update') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasPermission('hazards.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->hasPermission('hazards.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return HazardPointForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HazardPointsTable::configure($table);
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
            'index' => ListHazardPoints::route('/'),
            'create' => CreateHazardPoint::route('/create'),
            'edit' => EditHazardPoint::route('/{record}/edit'),
        ];
    }
}
