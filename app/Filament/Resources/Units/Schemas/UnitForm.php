<?php

namespace App\Filament\Resources\Units\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput; // <-- KEMBALIKAN ALAMATNYA KE 'Forms'

class UnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('nama_ruang')
                    ->label('Nama Unit / Ruang')
                    ->required()
                    ->maxLength(255),

                TextInput::make('id_unit')
                    ->label('ID Unit')
                    ->disabledOn('edit') // <-- Akan nonaktif saat di halaman Edit
                    ->hiddenOn('create') // <-- Akan tersembunyi saat di halaman Create
                    ->required()
                    ->numeric()
                    ->unique(ignoreRecord: true),
            ]);
    }
}