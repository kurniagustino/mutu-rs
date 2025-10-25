<?php

namespace App\Filament\Resources\ImutCategories\Pages;

use App\Filament\Resources\ImutCategories\ImutCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListImutCategories extends ListRecords
{
    protected static string $resource = ImutCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /**
     * ✅ Optimize query - select only needed columns
     */
    protected function getTableQuery(): Builder
    {
        return static::getResource()::getEloquentQuery()
            ->select(['imut_category_id', 'imut']); // ✅ Hanya select kolom yang dipakai
    }
}
