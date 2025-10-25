<?php

namespace App\Filament\Resources\StatusKategoris\Pages;

use App\Filament\Resources\StatusKategoris\StatusKategoriResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStatusKategoris extends ListRecords
{
    protected static string $resource = StatusKategoriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
