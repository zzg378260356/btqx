<?php

namespace App\Filament\Resources\HazardPoints\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class HazardPointForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')->label('隐患点编号')->required()->unique(ignoreRecord: true), TextInput::make('name')->label('名称')->required(), Select::make('hazard_type')->label('灾害类型')->options(['滑坡' => '滑坡', '泥石流' => '泥石流', '崩塌' => '崩塌', '山洪' => '山洪'])->required(), Select::make('level')->label('风险等级')->options(['high' => '高', 'medium' => '中', 'low' => '低'])->required(), TextInput::make('district')->label('行政区')->required(), TextInput::make('latitude')->label('纬度')->numeric(), TextInput::make('longitude')->label('经度')->numeric(), TextInput::make('threatened_people')->label('威胁人数')->numeric()->default(0), TextInput::make('threatened_property')->label('威胁财产')->numeric()->default(0), Select::make('status')->label('状态')->options(['active' => '在册', '治理中' => '治理中', 'closed' => '已核销'])->default('active'), Textarea::make('description')->label('描述')->columnSpanFull(),
            ]);
    }
}
