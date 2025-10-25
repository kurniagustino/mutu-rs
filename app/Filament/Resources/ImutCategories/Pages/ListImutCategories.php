<?php

namespace App\Filament\Resources\ImutCategories\Pages;

use App\Filament\Resources\ImutCategories\ImutCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListImutCategories extends ListRecords
{
    protected static string $resource = ImutCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
