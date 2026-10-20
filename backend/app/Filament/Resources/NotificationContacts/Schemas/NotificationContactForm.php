<?php

namespace App\Filament\Resources\NotificationContacts\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class NotificationContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('联系人')->required(), TextInput::make('organization')->label('单位'), TextInput::make('district')->label('区域'), TextInput::make('phone')->label('手机号')->tel()->required(), TextInput::make('email')->label('邮箱')->email(), CheckboxList::make('notification_channels')->label('通知渠道')->options(['sms' => '短信', 'voice' => '语音', 'broadcast' => '广播'])->columns(3), Toggle::make('is_active')->label('启用')->default(true),
            ]);
    }
}
