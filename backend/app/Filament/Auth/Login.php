<?php

namespace App\Filament\Auth;

use Filament\Schemas\Components\Component;
use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\TextInput;

class Login extends BaseLogin
{
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('username')
            ->label('用户名')
            ->required()
            ->autofocus()
            ->autocomplete('username');
    }

    protected function getCredentialsFromFormData(#[\SensitiveParameter] array $data): array
    {
        return ['username' => $data['username'], 'password' => $data['password']];
    }
}
