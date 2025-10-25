<?php

namespace App\Filament\Resources\Indicators\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VariablesRelationManager extends RelationManager
{
    protected static string $relationship = 'variables';
    protected static ?string $title = 'Variabel Indikator';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('variable_name')
                    ->label('Nama Variabel')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('cth: Jumlah tindakan kebersihan tangan yang dilakukan')
                    ->columnSpanFull(),

                Select::make('variable_type')
                    ->label('Tipe Variabel')
                    ->options([
                        'N' => 'Numerator (Pembilang)',
                        'D' => 'Denominator (Penyebut)',
                    ])
                    ->required()
                    ->helperText('N untuk pembilang, D untuk penyebut'),

                Textarea::make('variable_description')
                    ->label('Deskripsi/Keterangan')
                    ->rows(3)
                    ->placeholder('Keterangan tambahan untuk variabel ini...')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('variable_name')
            ->columns([
                TextColumn::make('variable_name')
                    ->label('Nama Variabel')
                    ->searchable()
                    ->wrap()
                    ->weight('medium'),

                TextColumn::make('variable_type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'N' => 'primary',
                        'D' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'N' => 'Numerator',
                        'D' => 'Denominator',
                        default => $state,
                    }),

                TextColumn::make('variable_description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->wrap()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Variabel')
                    ->icon('heroicon-o-plus'),
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
            ])
            ->emptyStateHeading('Belum ada variabel')
            ->emptyStateDescription('Tambahkan variabel numerator dan denominator untuk indikator ini.')
            ->emptyStateIcon('heroicon-o-variable');
    }
}
