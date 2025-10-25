<?php

namespace App\Filament\Resources\Units\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
// PERBAIKI SEMUA ALAMAT ACTION DI BAWAH INI
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class UnitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_unit')
                    ->label('ID Unit')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('nama_ruang')
                    ->label('Nama Unit / Ruang')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([ // Aksi per baris
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([ // Aksi di atas tabel
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}