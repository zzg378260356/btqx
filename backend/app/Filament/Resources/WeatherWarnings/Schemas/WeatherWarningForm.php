<?php

namespace App\Filament\Resources\WeatherWarnings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WeatherWarningForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('title')->label('预警名称')->required()->maxLength(255), Select::make('level')->label('预警等级')->options(['red' => '红色', 'orange' => '橙色', 'yellow' => '黄色', 'blue' => '蓝色'])->required(), Select::make('type')->label('灾害类型')->options(['暴雨' => '暴雨', '雷电' => '雷电', '大风' => '大风', '冰雹' => '冰雹', '其他' => '其他'])->required(), TextInput::make('district')->label('影响区域')->required()->maxLength(255), Select::make('status')->label('处置状态')->options(['处置中' => '处置中', '已完成' => '已完成'])->default('处置中')->required(), DateTimePicker::make('published_at')->label('发布时间')->default(now())->required(), TextInput::make('responsible_unit')->label('责任单位')->required()->maxLength(255)]);
    }
}
