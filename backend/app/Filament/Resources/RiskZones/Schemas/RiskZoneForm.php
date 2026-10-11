<?php

namespace App\Filament\Resources\RiskZones\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RiskZoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')->label('风险区编号')->required()->unique(ignoreRecord: true), TextInput::make('name')->label('名称')->required(), Select::make('level')->label('预警等级')->options(['red' => '红色', 'orange' => '橙色', 'yellow' => '黄色', 'blue' => '蓝色'])->required(), TextInput::make('district')->label('行政区')->required(), TextInput::make('area_sq_km')->label('面积（平方公里）')->numeric(), TextInput::make('threatened_people')->label('威胁人数')->numeric()->default(0), TextInput::make('threatened_property')->label('威胁财产')->numeric()->default(0), Select::make('status')->label('状态')->options(['active' => '有效', 'closed' => '已解除'])->default('active'), Textarea::make('description')->label('描述')->columnSpanFull(),
            ]);
    }
}
