<?php

namespace App\Filament\Resources\NotificationPolicies\Pages;

use App\Filament\Resources\NotificationPolicies\NotificationPolicyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNotificationPolicies extends ListRecords
{
    protected static string $resource = NotificationPolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
