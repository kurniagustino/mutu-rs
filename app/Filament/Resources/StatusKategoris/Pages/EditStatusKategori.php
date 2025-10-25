<?php

namespace App\Filament\Resources\StatusKategoris\Pages;

use App\Filament\Resources\StatusKategoris\StatusKategoriResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStatusKategori extends EditRecord
{
    protected static string $resource = StatusKategoriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
