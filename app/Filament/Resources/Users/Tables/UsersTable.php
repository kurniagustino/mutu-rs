<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ✅ Existing columns (tidak diubah)
                TextColumn::make('name')
                    ->label('Nama Pengguna')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('username')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('NIP')
                    ->searchable(),

                // ✅ UPDATED: Display multiple departemens (tidak diubah)
                TextColumn::make('departemens.nama_ruang')
                    ->label('Departemen')
                    ->badge()
                    ->separator(',')
                    ->color('info')
                    ->wrap(),

                // ✅ NEW: Display level dari pivot table (tidak diubah)
                TextColumn::make('departemens')
                    ->label('Level')
                    ->formatStateUsing(function ($record) {
                        return $record->departemens
                            ->pluck('pivot.level')
                            ->filter()
                            ->map(fn ($level) => ucfirst($level))
                            ->join(', ');
                    })
                    ->badge()
                    ->color('success'),

                // ✅ 100% DYNAMIC - auto-format semua roles (tidak diubah)
                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->separator(',')
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->color(fn (string $state): string => match (true) {
                        str_contains(strtolower($state), 'admin') => 'danger',
                        str_contains(strtolower($state), 'super') => 'warning',
                        str_contains(strtolower($state), 'karu') => 'info',
                        str_contains(strtolower($state), 'operator') => 'success',
                        default => 'gray',
                    }),

                // ===================================
                // ✅ NEW COLUMNS - Tambahan
                // ===================================
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('identitas')
                    ->label('No. Identitas')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tempatlahir')
                    ->label('Tempat Lahir')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tgllahir')
                    ->label('Tanggal Lahir')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('pendidikan_terakhir')
                    ->label('Pendidikan')
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('aktivasi')
                    ->label('Aktivasi')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('status')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-no-symbol')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // ✅ Existing filters (tidak diubah)
                SelectFilter::make('departemen')
                    ->relationship('departemens', 'nama_ruang')
                    ->searchable()
                    ->preload()
                    ->label('Filter by Departemen'),

                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->label('Filter by Role')
                    ->getOptionLabelFromRecordUsing(fn ($record) => ucwords(str_replace('_', ' ', $record->name))),

                // ===================================
                // ✅ NEW FILTERS - Tambahan
                // ===================================
                TernaryFilter::make('aktivasi')
                    ->label('Status Aktivasi')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),

                TernaryFilter::make('status')
                    ->label('Status Kepegawaian')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),

                SelectFilter::make('pendidikan_terakhir')
                    ->options([
                        'SMA' => 'SMA',
                        'D3' => 'D3',
                        'D4' => 'D4',
                        'S1' => 'S1',
                        'S2' => 'S2',
                        'S3' => 'S3',
                    ])
                    ->label('Filter by Pendidikan'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
