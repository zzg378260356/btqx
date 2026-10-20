<?php

namespace App\Filament\Resources\NotificationPolicies\Pages;

use App\Filament\Resources\NotificationPolicies\NotificationPolicyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNotificationPolicy extends EditRecord
{
    protected static string $resource = NotificationPolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
