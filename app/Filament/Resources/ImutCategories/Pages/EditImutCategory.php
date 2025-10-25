<?php

namespace App\Filament\Resources\ImutCategories\Pages;

use App\Filament\Resources\ImutCategories\ImutCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditImutCategory extends EditRecord
{
    protected static string $resource = ImutCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
