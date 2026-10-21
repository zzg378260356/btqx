<?php

namespace App\Filament\Resources\NotificationContacts;

use App\Filament\Resources\NotificationContacts\Pages\CreateNotificationContact;
use App\Filament\Resources\NotificationContacts\Pages\EditNotificationContact;
use App\Filament\Resources\NotificationContacts\Pages\ListNotificationContacts;
use App\Filament\Resources\NotificationContacts\Schemas\NotificationContactForm;
use App\Filament\Resources\NotificationContacts\Tables\NotificationContactsTable;
use App\Models\NotificationContact;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NotificationContactResource extends Resource
{
    protected static ?string $model = NotificationContact::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = '通知联系人';

    protected static ?string $pluralModelLabel = '通知联系人';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermission('contacts.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasPermission('contacts.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasPermission('contacts.update') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasPermission('contacts.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->hasPermission('contacts.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return NotificationContactForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NotificationContactsTable::configure($table);
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
            'index' => ListNotificationContacts::route('/'),
            'create' => CreateNotificationContact::route('/create'),
            'edit' => EditNotificationContact::route('/{record}/edit'),
        ];
    }
}
