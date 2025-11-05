<?php

namespace App\Filament\Resources\Indicators\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IndicatorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('imutCategory.imut_name_category')
                    ->label('Kategori (Area)')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state ?? '') {
                        'Wajib' => 'success',
                        default => 'gray',
                    })
                    ->searchable(),

                TextColumn::make('indicator_name')
                    ->label('Nama Indikator')
                    ->searchable()
                    ->limit(70)
                    ->wrap()
                    ->weight('medium'),

                TextColumn::make('dimensi_mutu')
                    ->label('Dimensi Mutu')
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(true),

                TextColumn::make('satuan_pengukuran')
                    ->label('Satuan')
                    ->badge()
                    ->color('info'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultPaginationPageOption(50);
    }
}
