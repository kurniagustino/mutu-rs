<?php

namespace App\Filament\Resources\Units\Pages;

use App\Filament\Resources\Units\UnitResource;
use App\Models\Departemen;
use Filament\Actions;
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
        $nextIdUnit = Departemen::max('id_unit') + 1;
        
        $data['id_unit'] = $nextIdUnit;

        return $data;
    }
}
