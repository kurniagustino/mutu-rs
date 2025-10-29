@push('styles')
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endpush

<x-filament-panels::page>
    {{-- Header Stats Card --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <x-filament::card class="bg-gradient-to-br from-blue-500 to-blue-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-100">Total Indikator</p>
                    <p class="text-3xl font-bold text-white">{{ count($validationData) }}</p>
                </div>
                <div class="p-3 bg-white/20 rounded-full">
                    <x-heroicon-o-chart-bar class="w-8 h-8 text-white" />
                </div>
            </div>
        </x-filament::card>

        <x-filament::card class="bg-gradient-to-br from-green-500 to-green-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-100">Sudah Validasi</p>
                    <p class="text-3xl font-bold text-white">
                        {{ collect($validationData)->where('hasil_validasi', 'Sudah Validasi')->count() }}
                    </p>
                </div>
                <div class="p-3 bg-white/20 rounded-full">
                    <x-heroicon-o-check-circle class="w-8 h-8 text-white" />
                </div>
            </div>
        </x-filament::card>

        <x-filament::card class="bg-gradient-to-br from-orange-500 to-orange-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-orange-100">Belum Validasi</p>
                    <p class="text-3xl font-bold text-white">
                        {{ collect($validationData)->where('hasil_validasi', 'Belum validasi')->count() }}
                    </p>
                </div>
                <div class="p-3 bg-white/20 rounded-full">
                    <x-heroicon-o-clock class="w-8 h-8 text-white" />
                </div>
            </div>
        </x-filament::card>

        <x-filament::card class="bg-gradient-to-br from-purple-500 to-purple-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-purple-100">Sudah Analisa</p>
                    <p class="text-3xl font-bold text-white">
                        {{ collect($validationData)->where('analisa', 'Sudah dianalisa')->count() }}
                    </p>
                </div>
                <div class="p-3 bg-white/20 rounded-full">
                    <x-heroicon-o-document-text class="w-8 h-8 text-white" />
                </div>
            </div>
        </x-filament::card>
    </div>

    {{-- Filter Card --}}
    <x-filament::card class="mb-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                <x-heroicon-o-funnel class="w-5 h-5 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Filter Data</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pilih kategori, tahun, dan bulan untuk melihat data
                </p>
            </div>
        </div>

        <form wire:submit.prevent="loadData">
            {{ $this->form }}
        </form>
    </x-filament::card>

    {{-- Data Table Card --}}
    <x-filament::card>
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg">
                    <x-heroicon-o-table-cells class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Data Indikator Mutu</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Periode
                        {{ $selectedYear }}-{{ str_pad($selectedMonth, 2, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>

            @if (count($validationData) > 0)
                <x-filament::badge color="success">
                    {{ count($validationData) }} Data
                </x-filament::badge>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th
                            class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-12">
                            ID #
                        </th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider"
                            style="max-width: 280px; width: 280px;">
                            Indikator Mutu
                        </th>
                        <th
                            class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-40">
                            Total Data<br />(N | D)
                        </th>
                        <th
                            class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-24">
                            Persentase
                        </th>
                        <th
                            class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-28">
                            Hasil<br />Validasi
                        </th>
                        <th
                            class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-24">
                            Analisa
                        </th>
                        <th
                            class="px-3 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-36">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($validationData as $index => $data)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150">
                            {{-- ID # --}}
                            <td
                                class="px-3 py-3 whitespace-nowrap text-center text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $data['indicator_id'] }}
                            </td>

                            {{-- Indikator Mutu - DIPERPENDEK --}}
                            <td class="px-3 py-3 text-sm text-gray-900 dark:text-gray-100" style="max-width: 280px;">
                                <div class="space-y-1.5">
                                    {{-- Kategori Badge --}}
                                    <div>
                                        <x-filament::badge color="info" size="sm">
                                            {{ $data['category_name'] }}
                                        </x-filament::badge>
                                    </div>

                                    {{-- Nama Indikator - Max 2 baris dengan ellipsis --}}
                                    <div class="font-medium line-clamp-2 text-xs leading-tight"
                                        title="{{ strip_tags($data['indicator_name']) }}">
                                        {{ strip_tags($data['indicator_name']) }}
                                    </div>
                                </div>
                            </td>

                            {{-- Total Data (Numerator | Denumerator) - DIPERKECIL --}}
                            <td class="px-3 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                            {{ $data['numerator'] }}
                                        </div>
                                        <div class="text-[10px] text-gray-500 dark:text-gray-400">N</div>
                                    </div>

                                    <div class="text-gray-400 dark:text-gray-600">|</div>

                                    <div class="text-center">
                                        <div class="text-lg font-bold text-purple-600 dark:text-purple-400">
                                            {{ $data['denominator'] }}
                                        </div>
                                        <div class="text-[10px] text-gray-500 dark:text-gray-400">D</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Persentase --}}
                            <td class="px-3 py-3 text-center">
                                @php
                                    $persentase = $data['persentase'];
                                    $color = $persentase >= 80 ? 'success' : ($persentase >= 60 ? 'warning' : 'danger');
                                @endphp
                                <x-filament::badge :color="$color" size="lg">
                                    {{ $persentase }}%
                                </x-filament::badge>
                            </td>

                            {{-- Hasil Validasi --}}
                            <td class="px-3 py-3 text-center">
                                @if ($data['hasil_validasi'] === 'Sudah Validasi')
                                    <x-filament::badge color="success" size="sm">
                                        <div class="flex items-center gap-1">
                                            <x-heroicon-o-check-circle class="w-3 h-3" />
                                            <span>Sudah</span>
                                        </div>
                                    </x-filament::badge>
                                @else
                                    <x-filament::badge color="warning" size="sm">
                                        <div class="flex items-center gap-1">
                                            <x-heroicon-o-clock class="w-3 h-3" />
                                            <span>Belum</span>
                                        </div>
                                    </x-filament::badge>
                                @endif
                            </td>

                            {{-- Analisa --}}
                            <td class="px-3 py-3 text-center">
                                @if ($data['analisa'] === 'Sudah dianalisa')
                                    <x-filament::badge color="success" size="sm">
                                        <div class="flex items-center gap-1">
                                            <x-heroicon-o-document-check class="w-3 h-3" />
                                            <span>Sudah</span>
                                        </div>
                                    </x-filament::badge>
                                @else
                                    <x-filament::badge color="gray" size="sm">
                                        <div class="flex items-center gap-1">
                                            <x-heroicon-o-document class="w-3 h-3" />
                                            <span>Belum</span>
                                        </div>
                                    </x-filament::badge>
                                @endif
                            </td>

                            {{-- Aksi: 2 Tombol --}}
                            <td class="px-3 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    {{-- Tombol Detail --}}
                                    <x-filament::button color="info" size="xs"
                                        wire:click="openDetailModal({{ $data['indicator_id'] }})" icon="heroicon-o-eye"
                                        :tooltip="'Lihat Detail'">
                                        Detail
                                    </x-filament::button>

                                    {{-- Tombol Validasi --}}
                                    <x-filament::button color="success" size="xs"
                                        wire:click="openValidasiModal({{ $data['indicator_id'] }})"
                                        icon="heroicon-o-clipboard-document-check" :tooltip="'Validasi Data'">
                                        Validasi
                                    </x-filament::button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-full mb-4">
                                        <x-heroicon-o-inbox class="w-12 h-12 text-gray-400 dark:text-gray-500" />
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">Tidak ada
                                        data</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Silakan pilih filter untuk
                                        menampilkan data</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (count($validationData) > 0)
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                    <div>
                        Menampilkan <span
                            class="font-semibold text-gray-900 dark:text-gray-100">{{ count($validationData) }}</span>
                        data indikator
                    </div>
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-information-circle class="w-4 h-4" />
                        <span>Periode: {{ $selectedYear }}-{{ str_pad($selectedMonth, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>
            </div>
        @endif
    </x-filament::card>
</x-filament-panels::page>
