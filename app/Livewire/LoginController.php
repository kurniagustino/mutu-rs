<?php

namespace App\Livewire;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
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
     * Override method form untuk Filament 4
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                // $this->getRememberFormComponent(), // Dikomentari untuk hapus "Remember me"
            ])
            ->statePath('data');
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

        return
        [
            $login_type => $login,
            'password' => $data['password'],
        ];
    }

    /**
     * Cek apakah username/email ada di database
     */
    protected function checkUserExists(string $login): bool
    {
        $login_type = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Query ke database untuk mengecek apakah user ada
        $user = \App\Models\User::where($login_type, $login)->first();

        // Jika user ditemukan, return true
        // Jika tidak, return false
        return (bool) $user;
    }

    /**
     * Custom error message
     */
    protected function throwFailureValidationException(): never
    {
        $login = $this->form->getState()['login'];
        $userExists = $this->checkUserExists($login);

        $message = $userExists ? 'Password salah.' : 'Username atau email tidak ditemukan.';

        throw ValidationException::withMessages([
            'data.login' => $message,

        ]);
    }
}
