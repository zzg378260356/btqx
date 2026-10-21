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
                TextColumn::make('assignedRole.name')->label('角色')->badge()->default('已禁用'),
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
