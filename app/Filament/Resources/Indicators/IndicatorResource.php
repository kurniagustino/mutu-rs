<?php

namespace App\Filament\Resources\Indicators;

use App\Filament\Resources\Indicators\Pages\CreateIndicator;
use App\Filament\Resources\Indicators\Pages\EditIndicator;
use App\Filament\Resources\Indicators\Pages\ListIndicators;
use App\Filament\Resources\Indicators\Schemas\IndicatorForm;
use App\Filament\Resources\Indicators\Tables\IndicatorsTable;
use App\Models\HospitalSurveyIndicator;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class IndicatorResource extends Resource
{
    protected static ?string $model = HospitalSurveyIndicator::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'HospitalSurveyIndicator';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    public static function form(Schema $schema): Schema
    {
        return IndicatorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IndicatorsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\VariablesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIndicators::route('/'),
            'create' => CreateIndicator::route('/create'),
            'edit' => EditIndicator::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return static::getModel()::query()
            ->select([
                'hospital_survey_indicator.*',
                'imut_category.imut_name_category',
            ])
            ->leftJoin('imut_category', 'hospital_survey_indicator.indicator_category_id', '=', 'imut_category.imut_category_id');
    }
}
