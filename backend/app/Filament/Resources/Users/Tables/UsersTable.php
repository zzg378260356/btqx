<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('姓名')->searchable(),
                TextColumn::make('username')->label('用户名')->searchable(),
                TextColumn::make('email')->label('邮箱')->searchable(),
                TextColumn::make('role')->label('角色')->badge()->formatStateUsing(fn (string $state): string => ['super_admin' => '超级管理员', 'operator' => '业务操作员', 'viewer' => '只读查看员', 'disabled' => '已禁用'][$state] ?? $state),
                TextColumn::make('created_at')->label('创建时间')->dateTime(),
            ])
            ->filters([
                //
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
