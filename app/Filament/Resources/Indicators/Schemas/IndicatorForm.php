<?php

namespace App\Filament\Resources\Indicators\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
// <-- ✅ TAMBAHKAN BARIS INI
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema; // ✅ INI SUDAH BENAR (v4)

class IndicatorForm
{
    public static function configure(Schema $schema): Schema // ✅ INI SUDAH BENAR (v4)
    {
        return $schema
            ->components([
                Section::make('Informasi Utama Indikator') // <-- Ini yang error
                    ->columns(2)
                    ->components([
                        // ✅ PERBAIKAN: Dari 'indicator_element' ke 'indicator_name'
                        Textarea::make('indicator_name')
                            ->label('Nama Indikator')
                            ->required()
                            ->columnSpanFull(),

                        // ✅ BARU: Tambahan dari PDF
                        Textarea::make('tujuan')
                            ->label('Tujuan')
                            ->rows(3)
                            ->columnSpanFull(),

                        // ✅ BARU: Tambahan dari PDF
                        TextInput::make('dimensi_mutu')
                            ->label('Dimensi Mutu')
                            ->placeholder('cth: Keselamatan, Tepat Waktu, Efektif'),

                        // ✅ BARU: Pengganti 'type_persen'
                        TextInput::make('satuan_pengukuran')
                            ->label('Satuan Pengukuran')
                            ->placeholder('cth: Persentase, Menit, Indeks, Per mil'),
                    ]),

                Section::make('Profil & Pengaturan')
                    ->columns(2)
                    ->components([
                        Select::make('indicator_category_id')
                            ->label('Kategori (Area)')
                            ->relationship('imutCategory', 'imut_name_category')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('indicator_type')
                            ->label('Tipe Indikator')
                            ->placeholder('cth: Struktur, Proses, Outcome')
                            ->required(),

                        // ✅ PERUBAHAN: Dari TextInput menjadi Select multiple
                        Select::make('units')
                            ->label('Area Monitor (Unit)')
                            ->relationship('units', 'nama_unit')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->placeholder('Pilih satu atau lebih unit')
                            ->columnSpanFull(), // <-- Pastikan ini ada

                        // ✅ BARU: Input untuk Status Kategori
                        Select::make('statuses')
                            ->label('Status Indikator')
                            ->relationship('statuses', 'nama_status') // ✅ PERBAIKAN: Kolom yang benar adalah 'nama_status'
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->placeholder('Pilih satu atau lebih status')
                            ->columnSpanFull(), // <-- Pastikan ini ada

                        TextInput::make('indicator_target')
                            ->label('Target')
                            ->maxLength(10)
                            ->placeholder('cth: 100, 80, <5, >76.61')
                            ->required()
                            ->default(0),

                        TextInput::make('indicator_source_of_data')
                            ->label('Sumber Data')
                            ->placeholder('cth: Rekam Medis, Survey, Observasi')
                            ->maxLength(255), // Sesuai migrasi baru

                        // ✅ BARU: Tambahan opsional (Relasi ke User)
                        Select::make('penanggung_jawab_id')
                            ->label('Penanggung Jawab (PIC)')
                            ->relationship('user', 'name') // Asumsi relasi 'user' di model
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih PIC (Opsional)'),
                    ]),

                Section::make('Detail Kamus Indikator')
                    ->columns(1)
                    ->collapsible() // Boleh diciutkan
                    ->components([
                        Textarea::make('indicator_definition')
                            ->label('Definisi Operasional')
                            ->rows(5)
                            ->columnSpanFull(),

                        Textarea::make('indicator_criteria_inclusive')
                            ->label('Kriteria Inklusif')
                            ->placeholder('Kriteria yang HARUS dipenuhi...')
                            ->rows(4)
                            ->columnSpanFull(),

                        Textarea::make('indicator_criteria_exclusive')
                            ->label('Kriteria Eksklusif')
                            ->placeholder('Kriteria yang TIDAK boleh ada...')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Lampiran')
                    ->columns(1)
                    ->components([
                        FileUpload::make('files')
                            ->label('📎 Upload Manual Form / Lampiran')
                            ->disk('public')
                            ->directory('indicator-manuals')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            ])
                            ->maxSize(10240) // 10MB
                            ->downloadable()
                            ->openable()
                            ->previewable(false)
                            ->helperText('Format: PDF, Excel, Word - Maksimal 10MB')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
