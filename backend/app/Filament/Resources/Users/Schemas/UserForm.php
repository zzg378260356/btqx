<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('姓名')->required(),
                TextInput::make('username')->label('用户名')->required()->unique(ignoreRecord: true),
                TextInput::make('email')->label('邮箱')->email()->required()->unique(ignoreRecord: true),
                TextInput::make('password')->label('密码')->password()->revealable()->dehydrated(fn (?string $state): bool => filled($state))->required(fn (string $operation): bool => $operation === 'create')->minLength(12),
                Select::make('role')->label('角色')->options(['super_admin' => '超级管理员', 'operator' => '业务操作员', 'viewer' => '只读查看员', 'disabled' => '已禁用'])->required()->default('viewer'),
            ]);
    }
}
