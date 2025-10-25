<?php

namespace App\Livewire;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Illuminate\Validation\ValidationException;

class LoginController extends BaseLogin
{
    /**
     * Specify custom view
     */
    public function getView(): string
    {
        return 'livewire.login-controller';
    }

    /**
     * Mengganti komponen input email bawaan.
     */
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('login')
            ->label('Email atau Username')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    /**
     * Override password component dengan proper tabindex
     */
    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Password')
            ->password()
            ->revealable()
            ->required()
            ->extraInputAttributes(['tabindex' => 2]);
    }

    /**
     * ✅ Logika untuk otentikasi - support email atau username
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        $login = $data['login'];
        $login_type = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return [
            $login_type => $login,
            'password' => $data['password'],
        ];
    }

    /**
     * Custom error message
     */
    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => 'Username atau email dan password tidak cocok.',
        ]);
    }
}
