<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Indikator Mutu RS. Bhayangkara Jambi
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Kelola indikator mutu untuk unit Anda: {{ Auth::user()->departemen->nama_unit ?? 'Tidak ada' }}
            </p>
        </div>

        {{-- Search Bar --}}
        <div class="flex gap-4 items-center">
            <div class="flex-1">
                <x-filament::input.wrapper>
                    <x-filament::input type="search" wire:model.live.debounce.500ms="search"
                        placeholder="Cari indikator..." />
                </x-filament::input.wrapper>
            </div>
        </div>

        {{-- Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($indicators as $indicator)
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition flex flex-col">
                    <div class="p-6 flex flex-col flex-1">
                        {{-- (Bagian card Anda sudah benar, saya biarkan saja) --}}
                        <div class="mb-4 space-y-2">
                            {{-- ✅ TAMBAHKAN BAGIAN JENIS IMUT DI SINI --}}
                            @if ($indicator->indicator_imut_type)
                                <div class="flex items-start gap-2">
                                    <span
                                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 min-w-[80px]">Jenis
                                        IMUT:</span>
                                    <span
                                        class="inline-flex items-center rounded-md bg-indigo-100 dark:bg-indigo-900 px-2.5 py-1 text-xs font-semibold text-indigo-700 dark:text-indigo-300">
                                        {{ $indicator->indicator_imut_type }}
                                    </span>
                                </div>
                            @endif

                            @if ($indicator->imutCategory)
                                <div class="flex items-start gap-2">
                                    <span
                                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 min-w-[80px]">IMUT
                                        Area:</span>
                                    <span class="text-xs font-medium text-primary-600 dark:text-primary-400">
                                        {{ $indicator->imutCategory->imut }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-start gap-3 mb-4">
                            @if ($indicator->status_kunci == 1)
                                <x-filament::icon icon="heroicon-o-key"
                                    class="w-5 h-5 text-warning-500 flex-shrink-0 mt-0.5" />
                            @endif

                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-bold text-gray-900 dark:text-white leading-tight mb-2">
                                    {{ $indicator->indicator_element }}
                                </h3>
                            </div>
                        </div>

                        <div class="space-y-1 mb-3">
                            <p class="text-xs text-pink-600 dark:text-pink-400">
                                Tipe Indikator: {{ $indicator->indicator_type }} |
                                Area Monitor: {{ $indicator->indicator_monitoring_area ?? 'N/A' }}
                            </p>
                        </div>

                        {{-- Kategori Badges --}}
                        <div class="mb-4 min-h-[50px]">
                            <div class="flex items-start gap-1.5 mb-2">
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Kategori:</span>
                            </div>

                            <div class="flex flex-wrap gap-1.5">
                                @forelse($indicator->statuses as $status)
                                    <span
                                        class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold text-white"
                                        style="background-color: {{ $status->warna_badge }};">
                                        {{ $status->nama_status }}
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-400 italic">Belum ada kategori</span>
                                @endforelse
                            </div>
                        </div>



                        {{-- Download Form Manual --}}
                        <div class="mt-auto">
                            @if ($indicator->files)
                                <a href="{{ Storage::disk('public')->url($indicator->files) }}" target="_blank"
                                    class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium hover:underline">
                                    📥 Unduh Form Manual
                                </a>
                            @else
                                <div class="inline-flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400">
                                    ⚠️ Manual Belum diupload
                                </div>
                            @endif
                        </div>

                    </div>

                    {{-- Action Buttons --}}
                    <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <div class="flex flex-col gap-2">
                            {{-- Baris 1: Tombol Proses & Rekap --}}
                            <div class="flex gap-2">
                                <x-filament::button color="warning" size="sm"
                                    icon="heroicon-o-clipboard-document-check"
                                    wire:click="openProsesModal({{ $indicator->indicator_id }})" class="flex-1">
                                    Proses
                                </x-filament::button>

                                <x-filament::button color="primary" size="sm" icon="heroicon-o-document-chart-bar"
                                    wire:click="openRekapModal({{ $indicator->indicator_id }})" class="flex-1">
                                    Rekap
                                </x-filament::button>
                            </div>

                            {{-- Baris 2: Tombol Persentase (Full Width) --}}
                            <x-filament::button color="danger" size="sm" icon="heroicon-o-chart-pie"
                                wire:click="openPersentaseModal({{ $indicator->indicator_id }})" class="w-full">
                                Persentase
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <x-filament::icon icon="heroicon-o-inbox" class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                    <p class="text-gray-500 dark:text-gray-400">
                        Tidak ada indikator yang ditugaskan untuk unit Anda saat ini.
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 
    =============================================================
     MODAL PROSES DATA - CUSTOM TABLE LAYOUT
    =============================================================
--}}
    <x-filament::modal id="proses-data-modal" width="6xl" :close-by-clicking-away="false">

        <x-slot name="heading">
            <div class="flex items-center space-x-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                {{-- ✅ TITLE DINAMIS BERDASARKAN MODE --}}
                <span>{{ $editMode ? '✏️ Edit Data Indikator' : '📊 Input Data Indikator' }}</span>
            </div>
        </x-slot>


        @if ($selectedIndicator)
            {{-- Header Info Indikator --}}
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <h3 class="font-bold text-lg text-blue-900 dark:text-blue-100 mb-1">
                    {{ strtoupper($selectedIndicator->indicator_element ?? 'INDIKATOR') }}
                </h3>
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    Pendataan Indikator Mutu Tanggal {{ now()->format('Y-m-d') }}
                    ( Unit: <span class="font-semibold">{{ Auth::user()->departemen->nama_unit ?? 'N/A' }}</span> )
                </p>
            </div>

            <div class="space-y-6">
                {{-- Field Tanggal dari Form Builder --}}
                <div>
                    {{ $this->prosesDataForm }}
                </div>

                {{-- TABEL VARIABEL - MIRIP GAMBAR --}}
                @if ($selectedIndicator->variables && count($selectedIndicator->variables) > 0)
                    <div>
                        <h4 class="font-bold text-base mb-3 text-gray-900 dark:text-gray-100">Data Variabel</h4>

                        <div class="overflow-x-auto border border-gray-300 dark:border-gray-600 rounded-lg">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-700">
                                        <th class="border-b border-gray-300 dark:border-gray-600 px-4 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300"
                                            style="width: 80px">
                                            #
                                        </th>
                                        <th class="border-b border-gray-300 dark:border-gray-600 px-4 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300"
                                            style="width: 60px">
                                            No
                                        </th>
                                        <th
                                            class="border-b border-gray-300 dark:border-gray-600 px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                            Indikator Variabel
                                        </th>
                                        <th class="border-b border-gray-300 dark:border-gray-600 px-4 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300"
                                            style="width: 200px">
                                            Input Data
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $index = 1; @endphp
                                    @foreach ($selectedIndicator->variables as $variable)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                            {{-- Badge Type N/D --}}
                                            <td
                                                class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-center">
                                                @if ($variable->variable_type === 'N')
                                                    <span
                                                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 font-bold text-sm">
                                                        N
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 font-bold text-sm">
                                                        D
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Nomor Urut --}}
                                            <td
                                                class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-center font-medium text-gray-900 dark:text-gray-100">
                                                {{ $index }}
                                            </td>

                                            {{-- Nama Variabel --}}
                                            <td
                                                class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-gray-800 dark:text-gray-200">
                                                {{ $variable->variable_name }}
                                            </td>

                                            {{-- Input Field --}}
                                            <td class="border-b border-gray-200 dark:border-gray-700 px-4 py-3">
                                                <input type="number"
                                                    wire:model="prosesData.{{ $variable->variable_type === 'N' ? 'numerator' : 'denominator' }}_{{ $variable->id }}"
                                                    placeholder="0"
                                                    class="w-full px-3 py-2 text-center border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                                    required />
                                            </td>
                                        </tr>
                                        @php $index++; @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Field Keterangan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        📝 Catatan / Keterangan (Opsional)
                    </label>
                    <textarea wire:model="prosesData.keterangan" rows="3" placeholder="Tambahkan catatan jika diperlukan..."
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                {{-- ✅ PROFIL INDIKATOR DIPINDAH KE SINI (BAWAH) --}}
                @if ($selectedIndicator->indicator_definition)
                    <div class="border-l-4 border-blue-500 pl-4 py-3 bg-gray-50 dark:bg-gray-800 rounded-r">
                        <h4 class="font-bold text-base mb-3 text-gray-900 dark:text-gray-100">Profil Indikator</h4>
                        <div class="grid grid-cols-12 gap-4 text-sm">
                            <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">Definisi</div>
                            <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                {{ $selectedIndicator->indicator_definition }}
                            </div>
                            @if ($selectedIndicator->indicator_source_of_data)
                                <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">Sumber Data
                                </div>
                                <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                    {{ $selectedIndicator->indicator_source_of_data }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- ✅ FOOTER DENGAN TOMBOL DINAMIS BERDASARKAN MODE --}}
        <x-slot name="footerActions">
            <div
                class="flex justify-between items-center gap-3 w-full pt-4 border-t border-gray-300 dark:border-gray-600">
                {{-- Tombol Batal di Kiri --}}
                <x-filament::button type="button" color="gray"
                    wire:click="$dispatch('close-modal', { id: 'proses-data-modal' })" icon="heroicon-o-x-mark">
                    Batal
                </x-filament::button>

                {{-- Tombol Simpan di Kanan --}}
                <div class="flex gap-2">
                    {{-- ✅ TOMBOL SIMPAN & TAMBAH LAGI (HANYA MUNCUL SAAT CREATE/TAMBAH DATA) --}}
                    @if (!$editMode)
                        <x-filament::button type="button" color="success" wire:click="simpanDanLanjut"
                            icon="heroicon-o-arrow-path">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            Simpan & Tambah Lagi
                        </x-filament::button>
                    @endif

                    {{-- TOMBOL SIMPAN (LABEL DINAMIS BERDASARKAN MODE) --}}
                    <x-filament::button type="button" color="primary" wire:click="simpanData"
                        icon="heroicon-o-check">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                            </path>
                        </svg>
                        {{-- ✅ LABEL BERUBAH SESUAI MODE --}}
                        {{ $editMode ? 'Update Data' : 'Simpan & Tutup' }}
                    </x-filament::button>
                </div>
            </div>
        </x-slot>

    </x-filament::modal>


    {{-- 
=============================================================
 MODAL LAPORAN PERSENTASE
=============================================================
--}}
    <x-filament::modal id="persentase-modal" width="7xl">
        <x-slot name="heading">
            <div class="flex items-center space-x-2">
                <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                </svg>
                <span>📊 PERSENTASE</span>
            </div>
        </x-slot>

        @if ($selectedIndicatorForReport)
            {{-- Header Info Indikator --}}
            <div class="mb-6">
                <div
                    class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-lg border border-blue-200 dark:border-blue-800 p-5">
                    <h3 class="font-bold text-xl text-gray-900 dark:text-gray-100 mb-2">
                        Judul Indikator Mutu
                    </h3>
                    <h2 class="font-bold text-2xl text-blue-900 dark:text-blue-100 mb-3">
                        {{ $selectedIndicatorForReport->indicator_element }}
                    </h2>

                    <div class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                        @if ($selectedIndicatorForReport->indicator_type || $selectedIndicatorForReport->indicator_monitoring_area)
                            <p>
                                <span class="font-semibold text-pink-600 dark:text-pink-400">
                                    Tipe Indikator: {{ $selectedIndicatorForReport->indicator_type }} |
                                    Area Monitor:
                                    {{ $selectedIndicatorForReport->indicator_monitoring_area ?? 'Semua (RS.Bhayangkara Jambi)' }}
                                </span>
                            </p>
                        @endif

                        <p class="text-gray-600 dark:text-gray-400">
                            Unit Terhubung: <span class="font-semibold">
                                @if ($selectedIndicatorForReport->departemens && $selectedIndicatorForReport->departemens->count() > 0)
                                    {{ $selectedIndicatorForReport->departemens->pluck('nama_unit')->join(', ') }}
                                @else
                                    Semua Unit
                                @endif
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Filter Section --}}
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Filter Tahun --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Pilih Tahun
                        </label>
                        <select wire:model.live="selectedYear"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            @for ($year = now()->year; $year >= 2020; $year--)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                    </div>

                    {{-- Filter Bulan --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Pilih Bulan (Opsional)
                        </label>
                        <select wire:model.live="selectedMonth"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <option value="">Semua Bulan</option>
                            @foreach (['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'] as $num => $name)
                                <option value="{{ $num }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tombol Print --}}
                    <div class="flex items-end">
                        <x-filament::button icon="heroicon-o-printer" color="gray" onclick="window.print()"
                            class="w-full">
                            Print
                        </x-filament::button>
                    </div>
                </div>
            </div>

            {{-- Tabel Data Persentase --}}
            <div class="overflow-x-auto border border-gray-300 dark:border-gray-600 rounded-lg">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600">
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center font-bold text-gray-900 dark:text-gray-100"
                                rowspan="2" style="min-width: 120px">
                                Judul \ Bulan Ke
                            </th>
                            @for ($month = 1; $month <= 12; $month++)
                                <th class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center font-semibold text-gray-900 dark:text-gray-100"
                                    style="min-width: 80px">
                                    {{ str_pad($month, 2, '0', STR_PAD_LEFT) }} {{ $selectedYear }}
                                </th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Row NUMERATOR --}}
                        <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/20">
                            <td
                                class="border border-gray-300 dark:border-gray-600 px-4 py-3 font-bold text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800">
                                NEMURATOR
                            </td>
                            @for ($month = 1; $month <= 12; $month++)
                                @php
                                    $key = str_pad($month, 2, '0', STR_PAD_LEFT) . ' ' . $selectedYear;
                                    $data = $reportData->get($key);
                                @endphp
                                <td
                                    class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center text-gray-900 dark:text-gray-100">
                                    {{ $data['numerator'] ?? '-' }}
                                </td>
                            @endfor
                        </tr>

                        {{-- Row DENUMARATOR --}}
                        <tr class="hover:bg-green-50 dark:hover:bg-green-900/20">
                            <td
                                class="border border-gray-300 dark:border-gray-600 px-4 py-3 font-bold text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800">
                                DENUMARATOR
                            </td>
                            @for ($month = 1; $month <= 12; $month++)
                                @php
                                    $key = str_pad($month, 2, '0', STR_PAD_LEFT) . ' ' . $selectedYear;
                                    $data = $reportData->get($key);
                                @endphp
                                <td
                                    class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center text-gray-900 dark:text-gray-100">
                                    {{ $data['denominator'] ?? '-' }}
                                </td>
                            @endfor
                        </tr>

                        {{-- Row RATA-RATA (%) --}}
                        <tr class="bg-yellow-50 dark:bg-yellow-900/20 hover:bg-yellow-100 dark:hover:bg-yellow-900/30">
                            <td
                                class="border border-gray-300 dark:border-gray-600 px-4 py-3 font-bold text-gray-900 dark:text-gray-100">
                                RATA-RATA (%)
                            </td>
                            @for ($month = 1; $month <= 12; $month++)
                                @php
                                    $key = str_pad($month, 2, '0', STR_PAD_LEFT) . ' ' . $selectedYear;
                                    $data = $reportData->get($key);
                                @endphp
                                <td
                                    class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center font-bold text-blue-700 dark:text-blue-300">
                                    {{ isset($data['persentase']) ? number_format($data['persentase'], 2) : '-' }}
                                </td>
                            @endfor
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Info Tambahan --}}
            @if ($reportData->isEmpty())
                <div
                    class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-700 rounded-lg">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                        <div>
                            <h4 class="font-bold text-yellow-800 dark:text-yellow-200">Belum Ada Data</h4>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                Belum ada data yang diinput untuk tahun {{ $selectedYear }}. Silakan input data
                                terlebih dahulu melalui tombol "Proses".
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tombol Aksi --}}
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-300 dark:border-gray-600 mt-6">
                <x-filament::button type="button" color="gray"
                    wire:click="$dispatch('close-modal', { id: 'persentase-modal' })">
                    Tutup
                </x-filament::button>
            </div>
        @endif
    </x-filament::modal>

    {{-- 
=============================================================
 MODAL REKAPITULASI INDIKATOR MUTU
=============================================================
--}}
    <x-filament::modal id="rekap-modal" width="7xl">
        <x-slot name="heading">
            <div class="flex items-center space-x-2">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <span>📋 Rekapitulasi Pengisian IMUT</span>
            </div>
        </x-slot>

        @if ($selectedIndicatorForRekap)
            {{-- Header Info --}}
            <div class="mb-6">
                <div
                    class="bg-gradient-to-r from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 rounded-lg border border-purple-200 dark:border-purple-800 p-5">
                    <h3 class="font-bold text-lg text-purple-900 dark:text-purple-100 mb-2">
                        Rekapitulasi Pengisian IMUT
                    </h3>
                    <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 mb-3">
                        {{ $selectedIndicatorForRekap->indicator_element }}
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="font-semibold text-gray-700 dark:text-gray-300">Unit:</span>
                            <span class="text-gray-900 dark:text-gray-100">
                                {{ Auth::user()->departemen->nama_unit ?? 'N/A' }}
                            </span>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700 dark:text-gray-300">Total Data:</span>
                            <span class="text-gray-900 dark:text-gray-100 font-bold">
                                {{ count($rekapData) }} Record
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter & Action Section --}}
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Filter Tahun --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Tahun
                        </label>
                        <select wire:model.live="rekapYear"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            @for ($year = now()->year; $year >= 2020; $year--)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                    </div>

                    {{-- Filter Bulan --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Bulan (Opsional)
                        </label>
                        <select wire:model.live="rekapMonth"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <option value="">Semua Bulan</option>
                            @foreach (['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'] as $num => $name)
                                <option value="{{ $num }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tombol Print --}}
                    <div class="flex items-end">
                        <x-filament::button icon="heroicon-o-printer" color="gray" onclick="window.print()"
                            class="w-full">
                            Print
                        </x-filament::button>
                    </div>

                    {{-- Tombol Export (Optional) --}}
                    <div class="flex items-end">
                        <x-filament::button icon="heroicon-o-arrow-down-tray" color="success" disabled
                            class="w-full">
                            Export Excel
                        </x-filament::button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main Content - Tabel Rekapitulasi --}}
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600">
                        <div class="px-5 py-4 border-b border-gray-300 dark:border-gray-600">
                            <h3 class="font-bold text-lg text-gray-900 dark:text-gray-100">
                                Rekapitulasi Indikator Mutu
                            </h3>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-700">
                                        <th
                                            class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center font-semibold text-gray-900 dark:text-gray-100">
                                            Judul Indikator
                                        </th>
                                        <th
                                            class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center font-semibold text-gray-900 dark:text-gray-100">
                                            Unit
                                        </th>
                                        <th
                                            class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center font-semibold text-gray-900 dark:text-gray-100">
                                            Total
                                        </th>
                                        {{-- ✅ KOLOM AKSI BARU --}}
                                        <th class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center font-semibold text-gray-900 dark:text-gray-100"
                                            style="width: 180px">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rekapData as $data)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td
                                                class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-gray-100">
                                                {{ $selectedIndicatorForRekap->indicator_element }}
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    📅 Tanggal:
                                                    {{ \Carbon\Carbon::parse($data['tanggal'])->format('d M Y') }}
                                                </div>
                                            </td>
                                            <td
                                                class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center text-gray-900 dark:text-gray-100">
                                                {{ $data['unit'] }}
                                            </td>
                                            <td
                                                class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center">
                                                <div class="space-y-1">
                                                    <div class="text-gray-700 dark:text-gray-300 text-xs">
                                                        N: <span class="font-semibold">{{ $data['numerator'] }}</span>
                                                    </div>
                                                    <div class="text-gray-700 dark:text-gray-300 text-xs">
                                                        D: <span
                                                            class="font-semibold">{{ $data['denominator'] }}</span>
                                                    </div>
                                                    <div class="font-bold text-blue-700 dark:text-blue-300">
                                                        {{ number_format($data['persentase'], 2) }}%
                                                    </div>
                                                </div>
                                            </td>
                                            {{-- ✅ TOMBOL EDIT & HAPUS --}}
                                            <td class="border border-gray-300 dark:border-gray-600 px-4 py-3">
                                                <div class="flex gap-2 justify-center items-center">
                                                    {{-- Tombol Edit --}}
                                                    <x-filament::button size="xs" color="warning"
                                                        icon="heroicon-o-pencil"
                                                        wire:click="openEditModal({{ $data['id'] }})"
                                                        tooltip="Edit data ini">
                                                        Edit
                                                    </x-filament::button>

                                                    {{-- Tombol Hapus --}}
                                                    <x-filament::button size="xs" color="danger"
                                                        icon="heroicon-o-trash"
                                                        wire:click="hapusData({{ $data['id'] }})"
                                                        wire:confirm="Apakah Anda yakin ingin menghapus data tanggal {{ \Carbon\Carbon::parse($data['tanggal'])->format('d M Y') }}?"
                                                        tooltip="Hapus data ini">
                                                        Hapus
                                                    </x-filament::button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4"
                                                class="border border-gray-300 dark:border-gray-600 px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                                <div class="flex flex-col items-center gap-2">
                                                    <svg class="w-12 h-12 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                        </path>
                                                    </svg>
                                                    <p class="font-semibold">Tidak Ada Inputan</p>
                                                    <p class="text-xs">Belum ada data untuk periode yang dipilih</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Tanggal Pengisian Section --}}
                    <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600">
                        <div class="px-5 py-4 border-b border-gray-300 dark:border-gray-600">
                            <h3 class="font-bold text-lg text-gray-900 dark:text-gray-100">
                                Tanggal Pengisian
                            </h3>
                        </div>

                        <div class="p-5">
                            @if (count($rekapData) > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($rekapData as $data)
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            📅 {{ \Carbon\Carbon::parse($data['tanggal'])->format('d M Y') }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
                                    Belum ada data pengisian
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Sidebar - Statistik --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- Statistik Card 1 --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600">
                        <div class="px-5 py-4 border-b border-gray-300 dark:border-gray-600">
                            <h3 class="font-bold text-base text-gray-900 dark:text-gray-100">
                                Statistik
                            </h3>
                        </div>

                        <div class="p-5">
                            @if (count($rekapStatistics) > 0)
                                <div class="space-y-3">
                                    @foreach ($rekapStatistics as $stat)
                                        <div
                                            class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ $stat['bulan'] }}
                                            </span>
                                            <span class="text-sm font-bold text-blue-700 dark:text-blue-300">
                                                {{ $stat['jumlah_pengisian'] }} Data
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
                                    Belum ada statistik
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Info Card --}}
                    <div
                        class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-lg border border-blue-200 dark:border-blue-700 p-5">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="font-bold text-sm text-blue-900 dark:text-blue-100 mb-1">
                                    Informasi
                                </h4>
                                <p class="text-xs text-blue-800 dark:text-blue-200">
                                    Data rekapitulasi menampilkan semua pengisian indikator mutu yang telah dilakukan
                                    untuk periode yang dipilih. Klik tombol Edit untuk mengubah atau Hapus untuk
                                    menghapus data.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Summary Stats --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600 p-5">
                        <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100 mb-4">
                            Ringkasan
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-600 dark:text-gray-400">Total Pengisian</span>
                                <span class="text-lg font-bold text-blue-700 dark:text-blue-300">
                                    {{ count($rekapData) }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-600 dark:text-gray-400">Periode</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $rekapMonth ? \Carbon\Carbon::createFromFormat('m', $rekapMonth)->format('F') : 'Semua' }}
                                    {{ $rekapYear }}
                                </span>
                            </div>
                            @if (count($rekapData) > 0)
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-gray-600 dark:text-gray-400">Rata-rata</span>
                                    <span class="text-lg font-bold text-green-700 dark:text-green-300">
                                        {{ number_format(collect($rekapData)->avg('persentase'), 2) }}%
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-300 dark:border-gray-600 mt-6">
                <x-filament::button type="button" color="gray"
                    wire:click="$dispatch('close-modal', { id: 'rekap-modal' })">
                    Tutup
                </x-filament::button>
            </div>
        @endif
    </x-filament::modal>



</x-filament-panels::page>

@push('scripts')
    <script>
        // ✅ LISTENER UNTUK BUKA MODAL EDIT SETELAH MODAL REKAP TUTUP
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('open-edit-after-close', () => {
                // Delay 300ms untuk pastikan modal rekap sudah tertutup sempurna
                setTimeout(() => {
                    Livewire.dispatch('open-modal', {
                        id: 'proses-data-modal'
                    });
                }, 300);
            });
        });
    </script>
@endpush
