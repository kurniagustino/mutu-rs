<?php

namespace App\Filament\Resources\Units\RelationManagers;

use App\Filament\Resources\Indicators\IndicatorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;   
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachBulkAction;


class IndicatorsRelationManager extends RelationManager
{
    protected static string $relationship = 'indicators';

    protected static ?string $relatedResource = IndicatorResource::class;

    protected static ?string $title = 'Indikator Terkait';

    public function table(Table $table): Table
    {
        return $table
        ->recordTitleAttribute('indicator_element')
            ->columns([
                TextColumn::make('indicator_element')
                    ->label('Nama Indikator'),
            ])
            ->headerActions([
                // GANTI CreateAction MENJADI AttachAction
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordTitleAttribute('indicator_element'),
            ])
            ->actions([
                DetachAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
