<?php

namespace App\Filament\Resources\Devices\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DeviceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->label('设备名称')->required()->maxLength(255), TextInput::make('code')->label('设备编号')->required()->maxLength(255)->unique(ignoreRecord: true), Select::make('type')->label('设备类型')->options(['自动气象站' => '自动气象站', '雨量计' => '雨量计', '风速仪' => '风速仪'])->required(), TextInput::make('district')->label('所属区域')->required()->maxLength(255), TextInput::make('latitude')->label('纬度')->numeric()->minValue(-90)->maxValue(90)->required(), TextInput::make('longitude')->label('经度')->numeric()->minValue(-180)->maxValue(180)->required(), Select::make('status')->label('运行状态')->options(['online' => '在线', 'offline' => '离线', 'maintenance' => '维护中'])->required()->default('offline'), DateTimePicker::make('last_seen_at')->label('最后上报时间')]);
    }
}
