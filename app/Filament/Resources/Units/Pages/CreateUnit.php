<?php

namespace App\Filament\Resources\Units\Pages;

use App\Filament\Resources\Units\UnitResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUnit extends CreateRecord
{
    protected static string $resource = UnitResource::class;

    /**
     * TAMBAHKAN METHOD DI BAWAH INI
     * Mengubah redirect setelah create, agar kembali ke halaman index (list).
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Letakkan logika otomatis di sini.
     * Method ini akan berjalan sebelum data baru disimpan.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Jika id_unit auto increment, tidak perlu set manual
        return $data;
    }
}
