<?php

namespace App\Filament\Resources\StatusKategoris\Schemas;

use Filament\Forms\Components\ColorPicker; 
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StatusKategoriForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_status')
                    ->label('Nama Kategori')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('cth: Indikator Mutu Nasional 2021')
                    ->columnSpanFull(),

                ColorPicker::make('warna_badge')
                    ->label('🎨 Warna Badge')
                    ->required()
                    ->default('#3b82f6') // Default blue
                    ->helperText('Pilih warna untuk badge kategori')
                    ->columnSpanFull(),
            ]);
    }
}
