<?php

namespace App\Filament\Resources\NotificationPolicies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class NotificationPoliciesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('策略名称')->searchable(),
                TextColumn::make('level')->label('预警等级')->badge()->formatStateUsing(fn (string $state): string => ['red' => '红色', 'orange' => '橙色', 'yellow' => '黄色', 'blue' => '蓝色'][$state] ?? $state)->color(fn (string $state): string => ['red' => 'danger', 'orange' => 'warning', 'yellow' => 'warning', 'blue' => 'info'][$state] ?? 'gray'),
                TextColumn::make('channels')->label('通知渠道')->badge()->formatStateUsing(fn (string $state): string => ['sms' => '短信', 'voice' => '语音', 'broadcast' => '广播'][$state] ?? $state),
                TextColumn::make('retry_interval_seconds')->label('重试间隔（秒）'),
                TextColumn::make('max_retries')->label('最大重试次数'),
                IconColumn::make('is_active')->label('启用')->boolean(),
            ])
            ->filters([
                SelectFilter::make('level')->label('预警等级')->options(['red' => '红色', 'orange' => '橙色', 'yellow' => '黄色', 'blue' => '蓝色']),
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
