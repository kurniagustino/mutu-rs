<?php

namespace App\Filament\Resources\Indicators\Pages;

use App\Filament\Resources\Indicators\IndicatorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIndicator extends CreateRecord
{
    protected static string $resource = IndicatorResource::class;
    
    /**
     * TAMBAHKAN METHOD DI BAWAH INI
     * Mengubah redirect setelah create, agar kembali ke halaman index (list).
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
