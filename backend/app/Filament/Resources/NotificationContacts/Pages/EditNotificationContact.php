<?php

namespace App\Filament\Resources\NotificationContacts\Pages;

use App\Filament\Resources\NotificationContacts\NotificationContactResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNotificationContact extends EditRecord
{
    protected static string $resource = NotificationContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
