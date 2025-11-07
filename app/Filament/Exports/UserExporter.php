<?php

namespace App\Filament\Exports;

use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('name'),
            ExportColumn::make('email'),
            ExportColumn::make('username'),
            ExportColumn::make('NIP'),
            ExportColumn::make('units.nama_unit')->label('Departemen'),
            ExportColumn::make('roles.name')->label('Roles'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your user export has completed and '.$export->successful_rows.' '.str('row')->plural($export->successful_rows).' have been exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.$failedRowsCount.' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
