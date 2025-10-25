<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Pengguna')
                    ->required()
                    ->maxLength(255),
                
                     TextInput::make('username')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),

                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
                
                    TextInput::make('NIP')
                    ->maxLength(255),

                    TextInput::make('level'),
                
                    Select::make('id_ruang')
                    ->relationship('departemen', 'nama_ruang')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Departemen / Unit'),

                     Select::make('level')
                    ->options([
                        '1' => 'Admin',
                        '2' => 'User',
                    ])
                    ->required(),
            ]);
    }
}
