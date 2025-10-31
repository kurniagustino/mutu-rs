<?php

namespace App\Filament\Resources\Units\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table; // 👈 Penting

class RuanganRelationManager extends RelationManager
{
    protected static string $relationship = 'ruangan';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Form untuk 'Create' atau 'Edit' ruangan dari sini
                TextInput::make('nama_ruang')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('sink')
                    ->maxLength(50),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_ruang')
            ->columns([
                TextColumn::make('id_ruang')->label('ID Ruang')->sortable(),
                TextColumn::make('nama_ruang')->searchable()->sortable(),
                TextColumn::make('sink')->label('Alias/Sink'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
