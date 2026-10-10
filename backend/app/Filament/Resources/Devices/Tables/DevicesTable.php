<?php

namespace App\Filament\Resources\Devices\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DevicesTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->label('设备名称')->searchable(), TextColumn::make('code')->label('设备编号')->searchable(), TextColumn::make('type')->label('类型'), TextColumn::make('district')->label('区域')->searchable(), TextColumn::make('status')->label('状态')->badge()->formatStateUsing(fn (string $state): string => ['online' => '在线', 'offline' => '离线', 'maintenance' => '维护中'][$state] ?? $state)->color(fn (string $state): string => $state === 'online' ? 'success' : 'warning'), TextColumn::make('last_seen_at')->label('最后上报')->dateTime()->sortable()])->filters([SelectFilter::make('status')->label('运行状态')->options(['online' => '在线', 'offline' => '离线', 'maintenance' => '维护中'])])->recordActions([EditAction::make()])->defaultSort('created_at', 'desc');
    }
}
