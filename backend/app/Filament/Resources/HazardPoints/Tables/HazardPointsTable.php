<?php

namespace App\Filament\Resources\HazardPoints\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class HazardPointsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('隐患点编号')->searchable()->sortable()->copyable()->toggleable(),
                TextColumn::make('name')->label('隐患点名称')->searchable()->sortable()->toggleable(),
                TextColumn::make('hazard_type')->label('灾害类型')->searchable()->sortable()->toggleable(),
                TextColumn::make('level')->label('风险等级')->badge()
                    ->formatStateUsing(fn (string $state): string => ['high' => '高', 'medium' => '中', 'low' => '低'][$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'high' => 'danger',
                        'medium' => 'warning',
                        'low' => 'success',
                        default => 'gray',
                    })->sortable()->toggleable(),
                TextColumn::make('district')->label('行政区')->searchable()->sortable()->toggleable(),
                TextColumn::make('longitude')->label('经度')->placeholder('未填写')->copyable()->sortable()->toggleable(),
                TextColumn::make('latitude')->label('纬度')->placeholder('未填写')->copyable()->sortable()->toggleable(),
                TextColumn::make('threatened_people')->label('威胁人数')->numeric(decimalPlaces: 0)->sortable()->toggleable(),
                TextColumn::make('threatened_property')->label('威胁财产')->numeric(decimalPlaces: 2)->sortable()->toggleable(),
                TextColumn::make('status')->label('状态')->badge()
                    ->formatStateUsing(fn (string $state): string => ['active' => '在册', '治理中' => '治理中', 'closed' => '已核销'][$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'info',
                        '治理中' => 'warning',
                        'closed' => 'success',
                        default => 'gray',
                    })->sortable()->toggleable(),
                TextColumn::make('description')->label('描述')->limit(50)->placeholder('未填写')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('discovered_at')->label('发现时间')->dateTime('Y-m-d H:i:s')->placeholder('未填写')->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('closed_at')->label('核销时间')->dateTime('Y-m-d H:i:s')->placeholder('未核销')->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label('创建时间')->dateTime('Y-m-d H:i:s')->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('更新时间')->dateTime('Y-m-d H:i:s')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('hazard_type')->label('灾害类型')->options([
                    '滑坡' => '滑坡', '泥石流' => '泥石流', '崩塌' => '崩塌', '山洪' => '山洪', '地面塌陷' => '地面塌陷',
                ]),
                SelectFilter::make('level')->label('风险等级')->options(['high' => '高', 'medium' => '中', 'low' => '低']),
                SelectFilter::make('status')->label('状态')->options(['active' => '在册', '治理中' => '治理中', 'closed' => '已核销']),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
