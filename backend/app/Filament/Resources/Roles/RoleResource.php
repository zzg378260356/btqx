<?php

namespace App\Filament\Resources\Roles;

use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Filament\Resources\Roles\Schemas\RoleForm;
use App\Filament\Resources\Roles\Tables\RolesTable;
use App\Models\Role;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?string $modelLabel = '角色';

    protected static ?string $pluralModelLabel = '角色权限';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermission('roles.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function canEdit($record): bool
    {
        return (auth()->user()?->isSuperAdmin() ?? false) && $record->slug !== 'super_admin';
    }

    public static function canDelete($record): bool
    {
        return self::canEdit($record) && ! $record->users()->exists();
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RolesTable::configure($table);
    }

    public static function getPages(): array
    {
        return ['index' => ListRoles::route('/'), 'create' => CreateRole::route('/create'), 'edit' => EditRole::route('/{record}/edit')];
    }
}
