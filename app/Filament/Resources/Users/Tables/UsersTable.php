<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Exports\UserExporter;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use FPDF;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->exporter(UserExporter::class),
                Action::make('export_pdf')
                    ->label('Export PDF')
                    ->action(function () {
                        $users = User::with('ruangans.unit')->get();
                        $pdf = new FPDF;
                        $pdf->AddPage('L', 'A4');
                        $pdf->SetFont('Arial', 'B', 12);

                        $pdf->Cell(0, 10, 'Users Data', 0, 1, 'C');
                        $pdf->Ln(10);

                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->Cell(10, 10, 'ID', 1);
                        $pdf->Cell(40, 10, 'Name', 1);
                        $pdf->Cell(50, 10, 'Email', 1);
                        $pdf->Cell(50, 10, 'Departemen/Unit', 1);
                        $pdf->Cell(30, 10, 'Username', 1);
                        $pdf->Cell(30, 10, 'NIP', 1);
                        $pdf->Cell(40, 10, 'Roles', 1);
                        $pdf->Cell(30, 10, 'Created At', 1);
                        $pdf->Ln();

                        $pdf->SetFont('Arial', '', 8);
                        foreach ($users as $user) {
                            // Mengambil nama departemen/unit dari relasi
                            $departments = $user->ruangans->pluck('unit.nama_unit')->unique()->join(', ');

                            $pdf->Cell(10, 10, $user->id, 1);
                            $pdf->Cell(40, 10, $user->name, 1);
                            $pdf->Cell(50, 10, $user->email, 1);
                            $pdf->Cell(50, 10, $departments, 1);
                            $pdf->Cell(30, 10, $user->username, 1);
                            $pdf->Cell(30, 10, $user->NIP, 1);
                            $pdf->Cell(40, 10, $user->roles->pluck('name')->join(', '), 1);
                            $pdf->Cell(30, 10, $user->created_at->format('Y-m-d'), 1);
                            $pdf->Ln();
                        }

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->Output('S');
                        }, 'users.pdf');
                    }),
            ])
            ->columns([
                // ✅ Existing columns (tidak diubah)
                TextColumn::make('name')
                    ->label('Nama Pengguna')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('username')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('NIP')
                    ->searchable(),

                // ✅ UPDATED: Display multiple departemens (tidak diubah)
                TextColumn::make('departemen')
                    ->label('Departemen')
                    ->badge()
                    ->color('info')
                    ->state(function ($record) {
                        $departments = $record->ruangans->pluck('nama_ruang')->unique()->values();
                        if ($departments->count() > 1) {
                            return $departments->first().' (+'.($departments->count() - 1).')';
                        }

                        return $departments->first() ?? '-';
                    })
                    ->tooltip(function ($record) {
                        return $record->ruangans->pluck('nama_ruang')->unique()->join("\n");
                    }),

                // ✅ 100% DYNAMIC - auto-format semua roles (tidak diubah)
                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->separator(',')
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->color(fn (string $state): string => match (true) {
                        str_contains(strtolower($state), 'admin') => 'danger',
                        str_contains(strtolower($state), 'super') => 'warning',
                        str_contains(strtolower($state), 'karu') => 'info',
                        str_contains(strtolower($state), 'operator') => 'success',
                        default => 'gray',
                    }),

                // ===================================
                // ✅ NEW COLUMNS - Tambahan
                // ===================================
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('identitas')
                    ->label('No. Identitas')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tempatlahir')
                    ->label('Tempat Lahir')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tgllahir')
                    ->label('Tanggal Lahir')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('pendidikan_terakhir')
                    ->label('Pendidikan')
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('aktivasi')
                    ->label('Aktivasi')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('status')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-no-symbol')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // ✅ Existing filters (tidak diubah)
                SelectFilter::make('ruangan') // (Lebih baik ganti nama 'key' nya juga)
                    ->relationship('ruangans', 'nama_ruang') // 👈 PERBAIKAN #4
                    ->searchable()
                    ->preload()
                    ->label('Filter by Departemen'),

                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->label('Filter by Role')
                    ->getOptionLabelFromRecordUsing(fn ($record) => ucwords(str_replace('_', ' ', $record->name))),

                // ===================================
                // ✅ NEW FILTERS - Tambahan
                // ===================================
                TernaryFilter::make('aktivasi')
                    ->label('Status Aktivasi')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),

                TernaryFilter::make('status')
                    ->label('Status Kepegawaian')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),

                SelectFilter::make('pendidikan_terakhir')
                    ->options([
                        'SMA' => 'SMA',
                        'D3' => 'D3',
                        'D4' => 'D4',
                        'S1' => 'S1',
                        'S2' => 'S2',
                        'S3' => 'S3',
                    ])
                    ->label('Filter by Pendidikan'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
