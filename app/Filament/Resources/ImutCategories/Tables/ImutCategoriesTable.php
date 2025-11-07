<?php

namespace App\Filament\Resources\ImutCategories\Tables;

use App\Filament\Exports\ImutCategoryExporter;
use App\Models\ImutCategory;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ImutCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->striped()
            ->headerActions([
                ExportAction::make()
                    ->exporter(ImutCategoryExporter::class),
                Action::make('export_pdf')
                    ->label('Export PDF')
                    ->action(function () {
                        $categories = ImutCategory::all();
                        $pdf = new \FPDF;
                        $pdf->AddPage('P', 'A4');
                        $pdf->SetFont('Arial', 'B', 12);

                        $pdf->Cell(0, 10, 'Imut Categories Data', 0, 1, 'C');
                        $pdf->Ln(10);

                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->Cell(20, 10, 'ID', 1);
                        $pdf->Cell(150, 10, 'Nama Kategori (Area)', 1);
                        $pdf->Ln();

                        $pdf->SetFont('Arial', '', 8);
                        foreach ($categories as $category) {
                            $pdf->Cell(20, 10, $category->id, 1);
                            $pdf->Cell(150, 10, $category->imut_name_category, 1);
                            $pdf->Ln();
                        }

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->Output('S');
                        }, 'imut-categories.pdf');
                    }),
            ])
            ->columns([
                TextColumn::make('imut_name_category')
                    ->label('Nama Kategori (Area)')
                    ->searchable()
                    ->sortable()
                    ->limit(50) // ✅ Limit text untuk performa rendering
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) > 50) {
                            return $state;
                        }

                        return null;
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth('md'), // ✅ Optimize modal size
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('imut_name_category', 'asc') // ✅ Default sorting
            ->persistSortInSession() // ✅ Cache sort preference
            ->persistSearchInSession() // ✅ Cache search preference
            ->defaultPaginationPageOption(25) // ✅ Optimize pagination
            ->paginationPageOptions([10, 25, 50, 100]); // ✅ Custom pagination
    }
}
