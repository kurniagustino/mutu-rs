<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filter Form - HORIZONTAL LAYOUT --}}
        <x-filament::card>
            <div class="space-y-4">
                {{-- Title & Info --}}
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Filter Laporan</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            <span class="inline-block w-2 h-2 bg-gray-300 dark:bg-gray-600 rounded-full mr-1"></span>
                            Opsi abu-abu = Tidak ada data
                        </p>
                    </div>
                </div>

                <form wire:submit="lihatLaporan">
                    {{-- ✅ GRID LAYOUT - 4 KOLOM RESPONSIVE --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        {{-- IMUT Category --}}
                        <div class="md:col-span-2">
                            {{ $this->form->getComponent('imut_category_id') }}
                        </div>

                        {{-- Tahun --}}
                        <div>
                            {{ $this->form->getComponent('tahun') }}
                        </div>

                        {{-- Bulan --}}
                        <div>
                            {{ $this->form->getComponent('bulan') }}
                        </div>
                    </div>

                    {{-- Tombol Lihat Laporan --}}
                    <div class="mt-4 flex justify-end">
                        <x-filament::button type="submit" color="primary" icon="heroicon-o-magnifying-glass"
                            size="lg">
                            Lihat Laporan
                        </x-filament::button>
                    </div>
                </form>
            </div>
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
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-950 dark:text-white">
                                📊 Daftar Pendataan IMUT
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Periode: <span class="font-semibold">{{ $bulanNama[$bulan] ?? '' }}
                                    {{ $tahun }}</span>
                                · Total: <span class="font-semibold text-primary-600">{{ count($indicators) }}
                                    Indikator</span>
                            </p>
                        </div>

                        <x-filament::button color="success" icon="heroicon-o-document-arrow-down" wire:click="cetakPdf">
                            Cetak PDF
                        </x-filament::button>
                    </div>

                    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-white/10">
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto divide-y divide-gray-200 dark:divide-white/10">
                                <thead class="bg-gray-50 dark:bg-white/5">
                                    <tr>
                                        <th
                                            class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
                                            No
                                        </th>
                                        <th
                                            class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
                                            Indikator Mutu
                                        </th>
                                        <th
                                            class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
                                            Total Pendataan
                                        </th>
                                        <th
                                            class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                                    @forelse($indicators as $index => $indicator)
                                        <tr
                                            class="transition duration-75 hover:bg-primary-50/30 dark:hover:bg-primary-900/10 {{ $index % 2 === 0 ? 'bg-white dark:bg-gray-900' : 'bg-gray-50/50 dark:bg-gray-800/30' }}">
                                            <td class="px-4 py-4 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ $index + 1 }}
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="space-y-2">
                                                    <div class="flex items-center gap-2">
                                                        <x-filament::badge color="info" size="sm">
                                                            {{ $indicator['area'] }}
                                                        </x-filament::badge>
                                                        <x-filament::badge color="gray" size="xs">
                                                            {{ $indicator['type'] }}
                                                        </x-filament::badge>
                                                    </div>
                                                    <div class="text-sm font-medium text-gray-950 dark:text-white">
                                                        {{ $indicator['title'] }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <div class="flex items-center justify-center">
                                                    <x-filament::badge :color="$indicator['total_pendataan'] > 0 ? 'success' : 'danger'" size="lg">
                                                        <span
                                                            class="text-lg font-bold">{{ $indicator['total_pendataan'] }}</span>
                                                    </x-filament::badge>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <x-filament::button color="primary" size="sm" icon="heroicon-m-eye"
                                                    wire:click="showDetail({{ $indicator['id'] }})" outlined>
                                                    Detail
                                                </x-filament::button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center justify-center py-4">
                                                    <div class="mb-4 rounded-full bg-gray-100 dark:bg-gray-500/20 p-4">
                                                        <svg class="h-10 w-10 text-gray-400 dark:text-gray-500"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                                        </svg>
                                                    </div>
                                                    <h4 class="text-base font-semibold text-gray-950 dark:text-white">
                                                        Tidak ada data
                                                    </h4>
                                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                        Tidak ada data pendataan untuk periode yang dipilih
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

</x-filament-panels::page>
