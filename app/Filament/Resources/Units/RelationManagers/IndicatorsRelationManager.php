<?php

namespace App\Filament\Resources\Units\RelationManagers;

use App\Filament\Resources\Indicators\IndicatorResource;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
                TextColumn::make('indicator_name')
                    ->label('Nama Indikator'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->multiple() // Aktifkan multi select
                    ->preloadRecordSelect()
                    ->recordTitleAttribute('indicator_name'),
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
