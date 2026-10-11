<?php

namespace App\Filament\Resources\NotificationPolicies\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class NotificationPolicyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('策略名称')->required(), Select::make('level')->label('预警等级')->options(['red' => '红色', 'orange' => '橙色', 'yellow' => '黄色', 'blue' => '蓝色'])->required(), CheckboxList::make('channels')->label('通知渠道')->options(['sms' => '短信', 'voice' => '语音', 'broadcast' => '广播'])->columns(3)->required(), TextInput::make('retry_interval_seconds')->label('重试间隔（秒）')->numeric()->default(300), TextInput::make('max_retries')->label('最大重试次数')->numeric()->default(3), Toggle::make('is_active')->label('启用')->default(true),
            ]);
    }
}
