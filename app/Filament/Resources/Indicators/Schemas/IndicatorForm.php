<?php

namespace App\Filament\Resources\Indicators\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class IndicatorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('indicator_element')
                    ->label('Nama Indikator')
                    ->required()
                    ->columnSpanFull(),

                Select::make('indicator_category_id')
                    ->label('Kategori (Area)')
                    ->relationship('imutCategory', 'imut_name_category')
                    ->searchable()
                    ->preload()
                    ->required(),

                // ✅ FIELD JENIS IMUT PAKAI TEXT INPUT (BEBAS ISI APA SAJA)
                TextInput::make('indicator_imut_type')
                    ->label('Jenis IMUT')
                    ->placeholder('cth: INM, IKP, IAP, IMK')
                    ->maxLength(20),

                // ✅ FIELD KATEGORI INDIKATOR (CHECKBOX MULTIPLE)
                CheckboxList::make('statuses')
                    ->label('🏷️ Kategori Indikator')
                    ->relationship('statuses', 'nama_status')
                    ->columns(2)
                    ->gridDirection('row')
                    ->bulkToggleable()
                    ->helperText('Pilih satu atau lebih kategori untuk indikator ini')
                    ->columnSpanFull(),

                TextInput::make('indicator_type')
                    ->label('Tipe Indikator (cth: Struktur, Proses, Outcome)')
                    ->required(),

                // ✅ FIELD AREA MONITOR (BARU DITAMBAHKAN)
                TextInput::make('indicator_monitoring_area')
                    ->label('Area Monitor')
                    ->placeholder('cth: Rawat Inap, IGD, Poliklinik, Laboratorium')
                    ->maxLength(200),

                TextInput::make('indicator_target')
                    ->label('Target')
                    ->numeric()
                    ->suffix('%')
                    ->required()
                    ->default(0),

                // ✅ FIELD BARU 1: SUMBER DATA
                TextInput::make('indicator_source_of_data')
                    ->label('Sumber Data')
                    ->placeholder('cth: Rekam Medis, Survey, Observasi')
                    ->maxLength(100),

                Textarea::make('indicator_definition')
                    ->label('Definisi Operasional')
                    ->rows(5)
                    ->columnSpanFull(),

                // ✅ FIELD BARU 2: KRITERIA INKLUSIF
                Textarea::make('indicator_criteria_inclusive')
                    ->label('Kriteria Inklusif')
                    ->placeholder('Kriteria yang HARUS dipenuhi untuk masuk dalam pengukuran...')
                    ->rows(4)
                    ->columnSpanFull(),

                // ✅ FIELD BARU 3: KRITERIA EKSKLUSIF
                Textarea::make('indicator_criteria_exclusive')
                    ->label('Kriteria Eksklusif')
                    ->placeholder('Kriteria yang TIDAK boleh ada untuk masuk dalam pengukuran...')
                    ->rows(4)
                    ->columnSpanFull(),

                // ✅ FIELD UPLOAD FILE MANUAL FORM
                FileUpload::make('files')
                    ->label('📎 Upload Manual Form')
                    ->disk('public') // Atau disk yang Anda gunakan
                    ->directory('indicator-manuals') // Folder penyimpanan
                    ->acceptedFileTypes([
                        'application/pdf', // PDF
                        'application/vnd.ms-excel', // Excel (.xls)
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // Excel (.xlsx)
                        'application/msword', // Word (.doc)
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // Word (.docx)
                    ])
                    ->maxSize(10240) // Max 10MB
                    ->downloadable() // Bisa didownload
                    ->openable() // Bisa dibuka di tab baru
                    ->previewable(false) // Tidak preview (karena bukan gambar)
                    ->helperText('Format: PDF, Excel (.xls, .xlsx), Word (.doc, .docx) - Maksimal 10MB')
                    ->columnSpanFull(),

            ]);
    }
}
