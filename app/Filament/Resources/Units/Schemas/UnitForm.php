<?php

namespace App\Filament\Resources\Units\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema; // <-- KEMBALIKAN ALAMATNYA KE 'Forms'

class UnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('nama_unit') // 👈 GANTI DARI 'nama_ruang'
                    ->label('Nama Unit')     // 👈 GANTI LABELNYA
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

            ]);
    }
}
