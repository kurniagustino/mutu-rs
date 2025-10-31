<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ✅ Existing fields (tidak diubah)
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
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),

                TextInput::make('NIP')
                    ->maxLength(255),

                // ✅ NEW FIELDS - Tambahan tanpa merusak struktur
                TextInput::make('identitas')
                    ->label('Nomor Identitas')
                    ->maxLength(50),

                TextInput::make('glr_depan')
                    ->label('Gelar Depan')
                    ->maxLength(50)
                    ->placeholder('Dr., Ir., dll'),

                TextInput::make('glr_blkg')
                    ->label('Gelar Belakang')
                    ->maxLength(50)
                    ->placeholder('S.Ked, M.Sc, dll'),

                TextInput::make('tempatlahir')
                    ->label('Tempat Lahir')
                    ->maxLength(50),

                DatePicker::make('tgllahir')
                    ->label('Tanggal Lahir')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->maxDate(now()),

                TextInput::make('pendidikan_terakhir')
                    ->label('Pendidikan Terakhir')
                    ->maxLength(50)
                    ->placeholder('S1, S2, D3, dll'),

                TextInput::make('alamat')
                    ->label('Alamat')
                    ->maxLength(255)
                    ->columnSpanFull(),

                // TextInput::make('fto')
                //     ->label('FTO')
                //     ->maxLength(50),

                Toggle::make('aktivasi')
                    ->label('Aktivasi Akun')
                    ->default(true)
                    ->inline(false),

                Toggle::make('status')
                    ->label('Status Aktif')
                    ->default(true)
                    ->inline(false),

                // ✅ Multi-select departemen (tidak diubah)
                Select::make('ruangans') // 👈 PERBAIKAN #1
                    ->relationship('ruangans', 'nama_ruang') // 👈 PERBAIKAN #2
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Departemen / Unit')
                    ->helperText('Pilih satu atau lebih departemen')
                    ->columnSpanFull(),

                // ✅ Roles (tidak diubah)
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->label('Roles (Hak Akses)')
                    ->getOptionLabelFromRecordUsing(fn ($record) => ucwords(str_replace('_', ' ', $record->name)))
                    ->columnSpanFull(),
            ]);
    }
}
