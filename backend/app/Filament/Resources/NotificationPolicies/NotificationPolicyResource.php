<?php

namespace App\Filament\Resources\NotificationPolicies;

use App\Filament\Resources\NotificationPolicies\Pages\CreateNotificationPolicy;
use App\Filament\Resources\NotificationPolicies\Pages\EditNotificationPolicy;
use App\Filament\Resources\NotificationPolicies\Pages\ListNotificationPolicies;
use App\Filament\Resources\NotificationPolicies\Schemas\NotificationPolicyForm;
use App\Filament\Resources\NotificationPolicies\Tables\NotificationPoliciesTable;
use App\Models\NotificationPolicy;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NotificationPolicyResource extends Resource
{
    protected static ?string $model = NotificationPolicy::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = '通知策略';

    protected static ?string $pluralModelLabel = '通知策略';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermission('policies.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasPermission('policies.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasPermission('policies.update') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasPermission('policies.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->hasPermission('policies.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return NotificationPolicyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NotificationPoliciesTable::configure($table);
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
            'index' => ListNotificationPolicies::route('/'),
            'create' => CreateNotificationPolicy::route('/create'),
            'edit' => EditNotificationPolicy::route('/{record}/edit'),
        ];
    }
}
