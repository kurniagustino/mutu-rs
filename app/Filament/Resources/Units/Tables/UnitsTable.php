<?php

namespace App\Filament\Resources\Units\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
// PERBAIKI SEMUA ALAMAT ACTION DI BAWAH INI
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UnitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id') // 👈 GANTI DARI 'id_unit'
                    ->label('ID')      // 👈 GANTI LABELNYA
                    ->sortable()
                    ->searchable(),

                TextColumn::make('nama_unit') // 👈 GANTI DARI 'nama_ruang'
                    ->label('Nama Unit')      // 👈 GANTI LABELNYA
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
