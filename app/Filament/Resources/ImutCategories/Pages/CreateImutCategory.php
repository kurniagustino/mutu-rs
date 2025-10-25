<?php

namespace App\Filament\Resources\ImutCategories\Pages;

use App\Filament\Resources\ImutCategories\ImutCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateImutCategory extends CreateRecord
{
    protected static string $resource = ImutCategoryResource::class;

    /**
     * TAMBAHKAN METHOD DI BAWAH INI
     * Mengubah redirect setelah create, agar kembali ke halaman index (list).
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
