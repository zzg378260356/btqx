<?php

namespace App\Filament\Resources\WeatherWarnings\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WeatherWarningsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([TextColumn::make('title')->label('预警名称')->searchable(), TextColumn::make('level')->label('等级')->badge()->formatStateUsing(fn (string $state): string => ['red' => '红色', 'orange' => '橙色', 'yellow' => '黄色', 'blue' => '蓝色'][$state] ?? $state)->color(fn (string $state): string => ['red' => 'danger', 'orange' => 'warning', 'yellow' => 'warning', 'blue' => 'info'][$state] ?? 'gray'), TextColumn::make('district')->label('区域')->searchable(), TextColumn::make('status')->label('处置状态')->badge(), TextColumn::make('published_at')->label('发布时间')->dateTime()->sortable(), TextColumn::make('responsible_unit')->label('责任单位')])->filters([SelectFilter::make('level')->label('等级')->options(['red' => '红色', 'orange' => '橙色', 'yellow' => '黄色', 'blue' => '蓝色']), SelectFilter::make('status')->label('处置状态')->options(['处置中' => '处置中', '已完成' => '已完成'])])->recordActions([EditAction::make()])->defaultSort('created_at', 'desc');
    }
}
