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
                                                    tag="a" href="#">
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
    </div>
</x-filament-panels::page>
