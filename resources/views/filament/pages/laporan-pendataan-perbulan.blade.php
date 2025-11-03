<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filter Form - HORIZONTAL LAYOUT --}}
        <x-filament::card>
            <form wire:submit="lihatLaporan" class="space-y-4">
                {{-- ✅ CUSTOM GRID LAYOUT - 3 KOLOM + TOMBOL --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    {{-- IMUT Category --}}
                    <div>
                        {{ $this->form->getComponent('imut_category_id') }}
                    </div>

                    {{-- Bulan --}}
                    <div>
                        {{ $this->form->getComponent('bulan') }}
                    </div>

                    {{-- Tahun --}}
                    <div>
                        {{ $this->form->getComponent('tahun') }}
                    </div>

                    {{-- Tombol Lihat Laporan --}}
                    <div>
                        <x-filament::button type="submit" color="primary" class="w-full">
                            Lihat Laporan
                        </x-filament::button>
                    </div>
                </div>
            </form>
        </x-filament::card>

        {{-- Results Table --}}
        @if ($showResults)
            @php
                $bulanNama = [
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember',
                ];
            @endphp

            <x-filament::card>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-950 dark:text-white">
                            Daftar Pendataan IMUT Bulan {{ $bulanNama[$bulan] ?? '' }} {{ $tahun }}
                            ({{ count($indicators) }})
                        </h2>

                        <x-filament::button color="info" icon="heroicon-o-document-arrow-down" wire:click="cetakPdf">
                            Cetak PDF
                        </x-filament::button>
                    </div>

                    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-white/10">
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto divide-y divide-gray-200 dark:divide-white/10">
                                <thead class="bg-gray-50 dark:bg-white/5">
                                    <tr>
                                        <th
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-950 dark:text-white">
                                            ID #
                                        </th>
                                        <th
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-950 dark:text-white">
                                            Indikator Mutu
                                        </th>
                                        <th
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-950 dark:text-white">
                                            Total Pendataan
                                        </th>
                                        <th
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-950 dark:text-white">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                                    @forelse($indicators as $index => $indicator)
                                        <tr
                                            class="transition duration-75 hover:bg-gray-50 dark:hover:bg-white/5 bg-white dark:bg-gray-900">
                                            <td class="px-3 py-4 text-sm text-gray-950 dark:text-white">
                                                {{ $index + 1 }}
                                            </td>
                                            <td class="px-3 py-4">
                                                <div class="space-y-1">
                                                    <div class="flex items-center gap-2">
                                                        <x-filament::badge color="info">
                                                            {{ $indicator['area'] }}
                                                        </x-filament::badge>
                                                    </div>
                                                    <div class="text-sm font-medium text-gray-950 dark:text-white">
                                                        🏥 {{ $indicator['title'] }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        Tipe: {{ $indicator['type'] }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-4">
                                                <x-filament::badge :color="$indicator['total_pendataan'] > 0 ? 'success' : 'danger'" size="lg">
                                                    {{ $indicator['total_pendataan'] }}
                                                </x-filament::badge>
                                            </td>
                                            <td class="px-3 py-4">
                                                <x-filament::button color="info" size="sm" icon="heroicon-m-eye"
                                                    wire:click="showDetail({{ $indicator['id'] }})">
                                                    Detil
                                                </x-filament::button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div class="mb-4 rounded-full bg-gray-100 dark:bg-gray-500/20 p-3">
                                                        <svg class="h-6 w-6 text-gray-500 dark:text-gray-400"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </div>
                                                    <h4 class="text-base font-semibold text-gray-950 dark:text-white">
                                                        Tidak ada data
                                                    </h4>
                                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                        Tidak ada data pendataan untuk bulan yang dipilih
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </x-filament::card>
        @endif

        {{-- Modal Detail Indikator --}}
        <x-filament::modal id="detail-modal" width="6xl" :close-by-clicking-away="false">
            <x-slot name="heading">
                <div class="flex items-center space-x-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                    <span>📋 Detail Indikator</span>
                </div>
            </x-slot>

            @if (!empty($detailData) && isset($detailData['indicator']))
                <div class="space-y-6">
                    {{-- Info Indikator --}}
                    <div
                        class="bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 p-5">
                        <h3 class="font-bold text-lg text-blue-900 dark:text-blue-100 mb-3">
                            {{ $detailData['indicator']['name'] }}
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Tipe:</span>
                                <span
                                    class="text-gray-900 dark:text-gray-100">{{ $detailData['indicator']['type'] }}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Kategori:</span>
                                <span
                                    class="text-gray-900 dark:text-gray-100">{{ $detailData['indicator']['category'] }}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Unit:</span>
                                <span
                                    class="text-gray-900 dark:text-gray-100">{{ $detailData['indicator']['unit'] }}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Target:</span>
                                <span class="text-gray-900 dark:text-gray-100">
                                    {{ $detailData['indicator']['target'] }}
                                    @if ($detailData['indicator']['satuan'] == 'Persentase')
                                        %
                                    @else
                                        {{ $detailData['indicator']['satuan'] }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Statistik --}}
                    <div
                        class="bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">📊 Statistik</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Total Input:</span>
                                <span
                                    class="font-bold text-gray-900 dark:text-gray-100">{{ $detailData['statistics']['total_records'] ?? 0 }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Total Numerator:</span>
                                <span
                                    class="font-bold text-blue-600 dark:text-blue-400">{{ number_format($detailData['statistics']['total_numerator'] ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Total Denominator:</span>
                                <span
                                    class="font-bold text-green-600 dark:text-green-400">{{ number_format($detailData['statistics']['total_denominator'] ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Rata-rata %:</span>
                                <span
                                    class="font-bold text-purple-600 dark:text-purple-400">{{ number_format($detailData['statistics']['rata_rata_persentase'] ?? 0, 2, ',', '.') }}%</span>
                            </div>
                        </div>
                    </div>

                    {{-- Tabel Data --}}
                    <div class="overflow-x-auto border border-gray-300 dark:border-gray-600 rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100 dark:bg-gray-800">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                        Tanggal
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                        Numerator
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                        Denominator
                                    </th>
                                    <th
                                        class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                        Persentase
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                        Input Oleh
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($detailData['results'] ?? [] as $result)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td
                                            class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-gray-900 dark:text-gray-100">
                                            {{ $result['tanggal_formatted'] }}
                                        </td>
                                        <td
                                            class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-center text-gray-900 dark:text-gray-100 font-semibold">
                                            {{ number_format($result['numerator'], 0, ',', '.') }}
                                        </td>
                                        <td
                                            class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-center text-gray-900 dark:text-gray-100 font-semibold">
                                            {{ number_format($result['denominator'], 0, ',', '.') }}
                                        </td>
                                        <td
                                            class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $result['persentase'] >= ($detailData['indicator']['target'] ?? 0) ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                {{ number_format($result['persentase'], 2, ',', '.') }}%
                                            </span>
                                        </td>
                                        <td
                                            class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $result['editor'] }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"
                                            class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            Tidak ada data untuk periode ini
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <x-slot name="footerActions">
                <x-filament::button color="gray" wire:click="closeDetailModal">
                    Tutup
                </x-filament::button>
            </x-slot>
        </x-filament::modal>

    </div>

    @if ($showResults && !empty($indicators))
        <div id="pdf-data" data-indicators="{{ json_encode($indicators) }}" data-bulan="{{ $bulan }}"
            data-tahun="{{ $tahun }}"
            data-category="{{ \App\Models\ImutCategory::find($imut_category_id)->imut_name_category ?? 'N/A' }}"
            data-unit="{{ Auth::user()->ruangans->first()->nama_ruang ?? 'N/A' }}" style="display: none;">
        </div>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', function() {
                Livewire.on('open-pdf-window', function() {
                    var pdfDataEl = document.getElementById('pdf-data');
                    if (!pdfDataEl) {
                        return;
                    }

                    var pdfData = {
                        indicators: JSON.parse(pdfDataEl.getAttribute('data-indicators') || '[]'),
                        bulan: parseInt(pdfDataEl.getAttribute('data-bulan') || '0'),
                        tahun: parseInt(pdfDataEl.getAttribute('data-tahun') || '0'),
                        category: pdfDataEl.getAttribute('data-category') || 'N/A',
                        unit: pdfDataEl.getAttribute('data-unit') || 'N/A'
                    };

                    if (!pdfData.indicators || pdfData.indicators.length === 0) {
                        return;
                    }

                    var bulanNama = {
                        1: 'Januari',
                        2: 'Februari',
                        3: 'Maret',
                        4: 'April',
                        5: 'Mei',
                        6: 'Juni',
                        7: 'Juli',
                        8: 'Agustus',
                        9: 'September',
                        10: 'Oktober',
                        11: 'November',
                        12: 'Desember'
                    };

                    var printWindow = window.open('', '_blank', 'width=800,height=600');

                    var htmlContent = '<!DOCTYPE html><html><head><title>Laporan Pendataan IMUT</title>';
                    htmlContent += '<style>';
                    htmlContent += 'body { font-family: Arial, sans-serif; padding: 20px; }';
                    htmlContent += 'table { width: 100%; border-collapse: collapse; margin-top: 20px; }';
                    htmlContent += 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
                    htmlContent += 'th { background-color: #f2f2f2; font-weight: bold; }';
                    htmlContent +=
                        '.header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }';
                    htmlContent += '.header h1 { margin: 0; font-size: 24px; font-weight: bold; }';
                    htmlContent += '.header p { margin: 5px 0; }';
                    htmlContent += '@media print { body { padding: 10px; } }';
                    htmlContent += '</style></head><body>';

                    htmlContent += '<div class="header">';
                    htmlContent += '<h1>Laporan Pendataan IMUT Perbulan</h1>';
                    htmlContent += '<p><strong>Unit:</strong> ' + pdfData.unit + '</p>';
                    htmlContent += '<p><strong>Kategori:</strong> ' + pdfData.category + '</p>';
                    htmlContent += '<p><strong>Periode:</strong> ' + (bulanNama[pdfData.bulan] || '') + ' ' +
                        pdfData.tahun + '</p>';
                    htmlContent += '<p><strong>Tanggal Cetak:</strong> ' + new Date().toLocaleString('id-ID') +
                        '</p>';
                    htmlContent += '</div>';

                    htmlContent += '<table>';
                    htmlContent += '<thead><tr>';
                    htmlContent +=
                        '<th style="width: 50px; border: 1px solid #ddd; padding: 10px; text-align: center; font-weight: bold;">No</th>';
                    htmlContent +=
                        '<th style="border: 1px solid #ddd; padding: 10px; text-align: left; font-weight: bold;">Indikator Mutu</th>';
                    htmlContent +=
                        '<th style="width: 150px; border: 1px solid #ddd; padding: 10px; text-align: left; font-weight: bold;">Unit</th>';
                    htmlContent +=
                        '<th style="width: 120px; border: 1px solid #ddd; padding: 10px; text-align: left; font-weight: bold;">Tipe</th>';
                    htmlContent +=
                        '<th style="width: 150px; border: 1px solid #ddd; padding: 10px; text-align: center; font-weight: bold;">Total Pendataan</th>';
                    htmlContent += '</tr></thead><tbody>';

                    for (var i = 0; i < pdfData.indicators.length; i++) {
                        var indicator = pdfData.indicators[i];
                        htmlContent += '<tr>';
                        htmlContent +=
                            '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' + (i + 1) +
                            '</td>';
                        htmlContent += '<td style="border: 1px solid #ddd; padding: 8px;">' + (indicator
                            .title || '') + '</td>';
                        htmlContent += '<td style="border: 1px solid #ddd; padding: 8px;">' + (indicator.area ||
                            'N/A') + '</td>';
                        htmlContent += '<td style="border: 1px solid #ddd; padding: 8px;">' + (indicator.type ||
                            'N/A') + '</td>';
                        htmlContent +=
                            '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;"><strong>' + (
                                indicator.total_pendataan || 0) + '</strong></td>';
                        htmlContent += '</tr>';
                    }

                    htmlContent += '</tbody><tfoot>';
                    htmlContent += '<tr style="background-color: #f9f9f9; font-weight: bold;">';
                    htmlContent +=
                        '<td colspan="4" style="border: 1px solid #ddd; padding: 10px; text-align: right;">Total Indikator:</td>';
                    htmlContent += '<td style="border: 1px solid #ddd; padding: 10px; text-align: center;">' +
                        pdfData.indicators.length + '</td>';
                    htmlContent += '</tr></tfoot></table>';
                    htmlContent += '</body></html>';

                    printWindow.document.write(htmlContent);
                    printWindow.document.close();

                    setTimeout(function() {
                        printWindow.print();
                    }, 250);
                });
            });
        </script>
    @endpush
</x-filament-panels::page>
