<?php

namespace App\Filament\Resources\NotificationContacts\Pages;

use App\Filament\Resources\NotificationContacts\NotificationContactResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNotificationContacts extends ListRecords
{
    protected static string $resource = NotificationContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
