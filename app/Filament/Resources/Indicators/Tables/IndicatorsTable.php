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
                TextColumn::make('urutan')
                    ->label('No.')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(true),

                TextColumn::make('imutCategory.imut_name_category')
                    ->label('Kategori (Area)')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state ?? '') {
                        'Wajib' => 'success',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('indicator_name')
                    ->label('Nama Indikator')
                    ->searchable()
                    ->limit(70)
                    ->wrap()
                    ->weight('medium'),

                TextColumn::make('statuses.nama_status')
                    ->label('Status')
                    ->badge()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('units.nama_unit')
                    ->label('Area Monitor')
                    ->badge()
                    ->color('warning')
                    ->searchable()
                    ->limitList(2)
                    ->expandableLimitedList()
                    ->toggleable()
                    ->toggledHiddenByDefault(true),

                TextColumn::make('user.name')
                    ->label('PIC')
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(true),

                TextColumn::make('indicator_type')
                    ->label('Tipe')
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(true),

                TextColumn::make('dimensi_mutu')
                    ->label('Dimensi Mutu')
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(true),

                TextColumn::make('tujuan')
                    ->label('Tujuan')
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(true)
                    ->limit(50)
                    ->wrap(),

                TextColumn::make('satuan_pengukuran')
                    ->label('Satuan')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('indicator_target')
                    ->label('Target')
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(true),
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
