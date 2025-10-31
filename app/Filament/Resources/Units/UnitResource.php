<?php

namespace App\Filament\Resources\Units;

use App\Filament\Resources\Units\Pages\CreateUnit; // <-- Tambahkan ini di atas
use App\Filament\Resources\Units\Pages\EditUnit;
use App\Filament\Resources\Units\Pages\ListUnits;
use App\Filament\Resources\Units\RelationManagers\IndicatorsRelationManager;
use App\Filament\Resources\Units\RelationManagers\RuanganRelationManager;
use App\Filament\Resources\Units\Schemas\UnitForm;
use App\Filament\Resources\Units\Tables\UnitsTable;
use App\Models\Unit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder; // ✅ Import Builder
use UnitEnum;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class; // 👈 Ganti ke model Unit yang baru

    protected static ?string $recordTitleAttribute = 'nama_unit'; // 👈 Ganti ke 'nama_unit' (kolom baru di tabel unit)

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    public static function form(Schema $schema): Schema
    {
        return UnitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UnitsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            IndicatorsRelationManager::class, // <-- Daftarkan di sini
            RuanganRelationManager::class, // 2. 👈 DAFTARKAN DI SINI
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUnits::route('/'),
            'create' => CreateUnit::route('/create'),
            'edit' => EditUnit::route('/{record}/edit'),
        ];
    }
}
