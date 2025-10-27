<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Pengguna')
                    ->searchable(),
                TextColumn::make('username')
                    ->searchable(),
                TextColumn::make('NIP')
                    ->searchable(),
                TextColumn::make('departemen.nama_ruang')
                    ->label('Departemen')
                    ->sortable(),
                // ✅ 100% DYNAMIC - auto-format semua roles
                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->separator(',')
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state))
                    )
                    ->color(fn (string $state): string =>
                        // Dynamic color based on role name pattern
                        match (true) {
                            str_contains(strtolower($state), 'admin') => 'danger',
                            str_contains(strtolower($state), 'super') => 'warning',
                            str_contains(strtolower($state), 'karu') => 'info',
                            str_contains(strtolower($state), 'operator') => 'success',
                            default => 'gray',
                        }
                    ),
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
