<?php

namespace App\Filament\Resources\ImutCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ImutCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                TextColumn::make('imut')
                    ->label('Nama Kategori (Area)')
                    ->searchable()
                    ->sortable()
                    ->limit(50) // ✅ Limit text untuk performa rendering
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) > 50) {
                            return $state;
                        }

                        return null;
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth('md'), // ✅ Optimize modal size
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('imut', 'asc') // ✅ Default sorting
            ->persistSortInSession() // ✅ Cache sort preference
            ->persistSearchInSession() // ✅ Cache search preference
            ->defaultPaginationPageOption(25) // ✅ Optimize pagination
            ->paginationPageOptions([10, 25, 50, 100]); // ✅ Custom pagination
    }
}
