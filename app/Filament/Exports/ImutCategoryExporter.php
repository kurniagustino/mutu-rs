<?php

namespace App\Filament\Exports;

use App\Models\ImutCategory;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ImutCategoryExporter extends Exporter
{
    protected static ?string $model = ImutCategory::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('imut_name_category'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your imut category export has completed and ' . $export->successful_rows . ' ' . str('row')->plural($export->successful_rows) . ' have been exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . $failedRowsCount . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
