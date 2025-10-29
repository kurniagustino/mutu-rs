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
        {{-- ... (Isi table card tetap sama seperti kode Anda) ... --}}
        <div class="flex items-center justify-between mb-4">
            {{-- ... (Isi header table card) ... --}}
        </div>

        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        {{-- ... (Semua <th> Anda) ... --}}
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
                            {{-- ... (Semua <td> Anda, tombol "Validasi" sudah benar) ... --}}
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
                        {{-- ... (Kode @empty Anda) ... --}}
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (count($validationData) > 0)
            {{-- ... (Kode footer table Anda) ... --}}
        @endif
    </x-filament::card>


    {{-- ========================================================= --}}
    {{-- 🚀 MODAL VALIDASI - FIXED VERSION 🚀 --}}
    {{-- ========================================================= --}}
    <x-filament::modal id="validasi-modal" width="3xl" :close-by-clicking-away="false">

        {{-- Header Modal --}}
        <x-slot name="heading">
            <div class="flex items-center space-x-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Formulir Validasi Indikator</span>
            </div>
            @if ($selectedIndicatorData)
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-1"
                    title="{{ strip_tags($selectedIndicatorData['indicator_name']) }}">
                    #{{ $selectedIndicatorData['indicator_id'] }} -
                    {{ strip_tags($selectedIndicatorData['indicator_name']) }}
                </p>
            @endif
        </x-slot>

        {{-- Body Modal --}}
        @if ($selectedIndicatorData)
            <div class="space-y-6">
                {{-- Info Indikator --}}
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <h3 class="font-bold text-lg text-blue-900 dark:text-blue-100 mb-1">
                        {{ strtoupper($selectedIndicatorData['indicator_name'] ?? 'INDIKATOR') }}
                    </h3>
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        Periode: {{ $selectedYear }}-{{ str_pad($selectedMonth, 2, '0', STR_PAD_LEFT) }} |
                        Unit: <span class="font-semibold">{{ auth()->user()->departemen->nama_ruang ?? 'N/A' }}</span>
                    </p>
                </div>

                {{-- Data Rill (dari RS) --}}
                <div class="p-4 border rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
                        📊 Data Rill (Laporan RS)
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Numerator Rill --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Numerator Rill <span class="text-red-500">*</span>
                            </label>
                            <input type="number" wire:model.defer="validasiForm.numerator_rill"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Masukkan N Rill" required min="0">
                            @error('validasiForm.numerator_rill')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Denominator Rill --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Denominator Rill <span class="text-red-500">*</span>
                            </label>
                            <input type="number" wire:model.defer="validasiForm.denominator_rill"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Masukkan D Rill" required min="0">
                            @error('validasiForm.denominator_rill')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Toggle Status Cocok --}}
                <div
                    class="flex items-center justify-between p-4 border rounded-lg border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <div class="flex-1">
                        <label class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            Apakah data Rill Sesuai dengan Data Sampling?
                        </label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Aktifkan jika data sudah sesuai
                        </p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model.live="validasiForm.status_cocok" class="sr-only peer">
                        <div
                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600">
                        </div>
                    </label>
                </div>

                {{-- Data Sampling (Conditional) --}}
                @if (!($validasiForm['status_cocok'] ?? true))
                    <div
                        class="p-4 border-2 border-warning-300 rounded-lg bg-warning-50 dark:bg-warning-900/10 dark:border-warning-700 animate-in fade-in-0 duration-300">
                        <h4
                            class="mb-3 text-sm font-semibold text-warning-800 dark:text-warning-300 flex items-center gap-2">
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                            Data Sampling (Jika Tidak Sesuai)
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            {{-- Numerator Sampling --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Numerator Sampling <span class="text-red-500">*</span>
                                </label>
                                <input type="number" wire:model.defer="validasiForm.numerator_sampling"
                                    class="w-full px-3 py-2 border border-warning-400 dark:border-warning-600 rounded-lg focus:ring-2 focus:ring-warning-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Masukkan N Sampling" required min="0">
                                @error('validasiForm.numerator_sampling')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Denominator Sampling --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Denominator Sampling <span class="text-red-500">*</span>
                                </label>
                                <input type="number" wire:model.defer="validasiForm.denominator_sampling"
                                    class="w-full px-3 py-2 border border-warning-400 dark:border-warning-600 rounded-lg focus:ring-2 focus:ring-warning-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Masukkan D Sampling" required min="0">
                                @error('validasiForm.denominator_sampling')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Footer Modal --}}
        <x-slot name="footerActions">
            <x-filament::button color="gray" wire:click="$dispatch('close-modal', { id: 'validasi-modal' })">
                Batal
            </x-filament::button>

            <x-filament::button color="success" wire:click="saveValidasi" wire:loading.attr="disabled"
                icon="heroicon-o-check-circle">
                <span wire:loading.remove wire:target="saveValidasi">Simpan Validasi</span>
                <span wire:loading wire:target="saveValidasi">
                    <svg class="inline w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Menyimpan...
                </span>
            </x-filament::button>
        </x-slot>

    </x-filament::modal>
    {{-- ========================================================= --}}
    {{-- 🛑 AKHIR KODE MODAL 🛑 --}}
    {{-- ========================================================= --}}


</x-filament-panels::page>
