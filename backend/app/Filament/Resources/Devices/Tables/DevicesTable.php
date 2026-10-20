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
        return $table
            ->columns([
                TextColumn::make('name')->label('设备名称')->searchable()->sortable()->toggleable(),
                TextColumn::make('code')->label('设备编号')->searchable()->sortable()->copyable()->toggleable(),
                TextColumn::make('type')->label('设备类型')->searchable()->sortable()->toggleable(),
                TextColumn::make('district')->label('所属区域')->searchable()->sortable()->toggleable(),
                TextColumn::make('longitude')->label('经度')->placeholder('未填写')->copyable()->sortable()->toggleable(),
                TextColumn::make('latitude')->label('纬度')->placeholder('未填写')->copyable()->sortable()->toggleable(),
                TextColumn::make('status')->label('运行状态')->badge()
                    ->formatStateUsing(fn (string $state): string => ['online' => '在线', 'offline' => '离线', 'maintenance' => '维护中'][$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'online' => 'success',
                        'maintenance' => 'warning',
                        default => 'gray',
                    })->sortable()->toggleable(),
                TextColumn::make('last_seen_at')->label('最后上报时间')->dateTime('Y-m-d H:i:s')->placeholder('暂无上报')->sortable()->toggleable(),
                TextColumn::make('created_at')->label('创建时间')->dateTime('Y-m-d H:i:s')->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('更新时间')->dateTime('Y-m-d H:i:s')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->label('运行状态')->options(['online' => '在线', 'offline' => '离线', 'maintenance' => '维护中']),
            ])
            ->recordActions([EditAction::make()])
            ->defaultSort('created_at', 'desc');
    }
}
