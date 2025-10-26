<?php

namespace App\Filament\Resources\StatusKategoris;

use App\Filament\Resources\StatusKategoris\Pages\CreateStatusKategori;
use App\Filament\Resources\StatusKategoris\Pages\EditStatusKategori;
use App\Filament\Resources\StatusKategoris\Pages\ListStatusKategoris;
use App\Filament\Resources\StatusKategoris\Schemas\StatusKategoriForm;
use App\Filament\Resources\StatusKategoris\Tables\StatusKategorisTable;
use App\Models\StatusKategori;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum; // ✅ TAMBAHKAN INI DI ATAS

class StatusKategoriResource extends Resource
{
    protected static ?string $model = StatusKategori::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $recordTitleAttribute = 'nama_status';

    // ✅ PENTING: Eager load relationship dengan withCount
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('indicators') // Eager load count indicators
            ->orderBy('id', 'desc');   // Default sorting
    }

    public static function form(Schema $schema): Schema
    {
        return StatusKategoriForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StatusKategorisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStatusKategoris::route('/'),
            'create' => CreateStatusKategori::route('/create'),
            'edit' => EditStatusKategori::route('/{record}/edit'),
        ];
    }
}
