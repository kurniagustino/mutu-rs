<?php

namespace App\Filament\Resources\ImutCategories;

use App\Filament\Resources\ImutCategories\Pages\CreateImutCategory;
use App\Filament\Resources\ImutCategories\Pages\EditImutCategory;
use App\Filament\Resources\ImutCategories\Pages\ListImutCategories;
use App\Filament\Resources\ImutCategories\Schemas\ImutCategoryForm;
use App\Filament\Resources\ImutCategories\Tables\ImutCategoriesTable;
use App\Models\ImutCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ImutCategoryResource extends Resource
{
    protected static ?string $model = ImutCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Imut Categori Area';

    protected static ?string $recordTitleAttribute = 'imut';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    // ✅ Disable polling untuk performa lebih baik
    protected static ?string $pollingInterval = null;

    public static function form(Schema $schema): Schema
    {
        return ImutCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ImutCategoriesTable::configure($table);
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
            'index' => ListImutCategories::route('/'),
            'create' => CreateImutCategory::route('/create'),
            'edit' => EditImutCategory::route('/{record}/edit'),
        ];
    }

    // ✅ Optimize Eloquent Query
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes(); // ✅ Remove global scopes if any
    }
}
