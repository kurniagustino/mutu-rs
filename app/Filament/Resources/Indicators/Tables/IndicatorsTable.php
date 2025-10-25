<?php

namespace App\Filament\Resources\Indicators\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;

class IndicatorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('indicator_element')
                    ->label('Nama Indikator')
                    ->searchable() 
                    ->limit(50)    
                    ->wrap(),      

                // ✅ KOLOM JENIS IMUT DENGAN TEXT INPUT (BEBAS)
                TextColumn::make('indicator_imut_type')
                    ->label('Jenis IMUT')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                // KOLOM BARU DARI RELASI
                TextColumn::make('imutCategory.imut')
                ->badge()
                    ->label('Kategori (Area)')
                    ->searchable(),

                TextColumn::make('indicator_type')
                    ->label('Tipe')
                    ->searchable(),

                TextColumn::make('indicator_target')
                    ->label('Target')
                    ->suffix('%'),
            ])
            ->filters([
                // ✅ PAKAI Filter LANGSUNG (BUKAN Tables\Filters\Filter)
                Filter::make('indicator_imut_type')
                    ->label('Jenis IMUT')
                    ->query(fn ($query) => $query->whereNotNull('indicator_imut_type')),
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
