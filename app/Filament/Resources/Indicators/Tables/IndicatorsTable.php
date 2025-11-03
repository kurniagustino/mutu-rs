<?php

namespace App\Filament\Resources\Indicators\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter; // ✅ Ganti Filter
use Filament\Tables\Table;

class IndicatorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('imutCategory.imut_name_category')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Wajib' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('indicator_name')
                    ->label('Nama Indikator')
                    ->searchable()
                    ->limit(70)
                    ->wrap()
                    ->weight('medium'),

                // ✅ BARU: Menampilkan dimensi mutu
                TextColumn::make('dimensi_mutu')
                    ->label('Dimensi Mutu')
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(true), // Sembunyikan by default

                // ✅ BARU: Menampilkan satuan
                TextColumn::make('satuan_pengukuran')
                    ->label('Satuan')
                    ->badge()
                    ->color('info'),

                TextColumn::make('imutCategory.imut_name_category')
                    ->badge()
                    ->label('Kategori (Area)')
                    ->searchable(),

                TextColumn::make('indicator_type')
                    ->label('Tipe')
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(true), // Sembunyikan by default

                TextColumn::make('indicator_target')
                    ->label('Target')
                    ->badge(),
            ])
            ->filters([
                // ✅ PERBAIKAN: Filter berdasarkan Kategori Area
                SelectFilter::make('indicator_category_id')
                    ->label('Kategori (Area)')
                    ->relationship('imutCategory', 'imut_name_category')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
