<?php

namespace App\Filament\Resources\NotificationContacts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class NotificationContactsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('联系人')->searchable(),
                TextColumn::make('organization')->label('单位')->searchable(),
                TextColumn::make('district')->label('区域')->searchable(),
                TextColumn::make('phone')->label('手机号')->searchable(),
                TextColumn::make('notification_channels')->label('通知渠道')->badge()->formatStateUsing(fn (string $state): string => ['sms' => '短信', 'voice' => '语音', 'broadcast' => '广播'][$state] ?? $state),
                IconColumn::make('is_active')->label('启用')->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('启用状态'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
