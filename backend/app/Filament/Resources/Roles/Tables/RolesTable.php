<?php

namespace App\Filament\Resources\Roles\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('角色名称')->searchable(),
            TextColumn::make('slug')->label('角色标识'),
            TextColumn::make('users_count')->label('用户数')->counts('users'),
            TextColumn::make('updated_at')->label('更新时间')->dateTime(),
        ])->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
