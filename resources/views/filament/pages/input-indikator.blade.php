<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Indikator Mutu RS. Bhayangkara Jambi
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Kelola indikator mutu untuk unit Anda:
                {{ Auth::user()->departemens->pluck('nama_unit')->join(', ') ?? 'Tidak ada' }}
            </p>
        </div>

        {{-- ✅ Card Filter (dengan prefix yang benar) --}}
        <x-filament::card class="mb-4">
            <x-slot name="header">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Filter Indikator
                </h2>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Filter Pencarian --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        🔍 Cari Indikator
                    </label>
                    <input type="text" wire:model.live="search" placeholder="Ketik nama indikator..."
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" />
                </div>

                {{-- Filter Kategori Area IMUT --}}
                <div>
                    {{-- ✅ Label diubah --}}
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        📍 Area IMUT
                    </label>
                    {{-- ✅ wire:model diubah --}}
                    <select wire:model.live="filterCategory"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Area IMUT</option>
                        {{-- ✅ Foreach dan variabel diubah --}}
                        @foreach ($availableCategories as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Unit --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        🏥 Unit / Ruangan
                    </label>
                    <select wire:model.live="filterUnit"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Unit Saya</option>
                        {{-- Pastikan $userUnits dikirim --}}
                        @foreach ($userUnits ?? [] as $unit)
                            <option value="{{ $unit->id_ruang }}">{{ $unit->nama_unit }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-filament::card>
        {{-- Akhir Card Filter --}}


        {{-- Info User & Unit --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 mb-4 rounded">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                👤 <strong>User:</strong> {{ Auth::user()->name }} |
                🏥 <strong>Unit Anda:</strong>
                {{-- Pastikan $userUnits dikirim --}}
                @if (($userUnits ?? collect())->isNotEmpty())
                    {{ $userUnits->pluck('nama_unit')->join(', ') }}
                @else
                    <span class="text-red-500">Tidak ada unit terhubung</span>
                @endif
            </p>
        </div>

        {{-- Cards Grid Indikator --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($indicators as $indicator)
                {{-- ✅ Ganti class card luar --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-lg transition flex flex-col">

                    {{-- ✅ Konten utama card (tanpa header terpisah) --}}
                    <div class="p-6 flex flex-col flex-1">

                        {{-- Judul Indikator --}}
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 leading-tight">
                            {{ $indicator->indicator_element }}
                        </h3>

                        {{-- Badges Section --}}
                        <div class="flex flex-wrap gap-2 mb-4">
                            {{-- Badge Tipe Indikator (Pakai badge gray) --}}
                            <x-filament::badge color="gray" size="sm">
                                {{ $indicator->indicator_type }}
                            </x-filament::badge>

                            {{-- Badge Kategori Area IMUT (Pakai badge primary) --}}
                            @if ($indicator->imutCategory)
                                <x-filament::badge color="primary" size="sm">
                                    Area: {{ $indicator->imutCategory->getCategoryNameAttribute() }}
                                </x-filament::badge>
                            @endif

                            {{-- Badge Unit (Pakai badge success) --}}
                            @if ($indicator->departemens->isNotEmpty())
                                <x-filament::badge color="success" size="sm" :title="$indicator->departemens->pluck('nama_unit')->join(', ')">
                                    {{-- Tampilkan 1 unit, sisanya di tooltip --}}
                                    Unit: {{ $indicator->departemens->first()->nama_unit }}
                                    @if ($indicator->departemens->count() > 1)
                                        (+{{ $indicator->departemens->count() - 1 }})
                                    @endif
                                </x-filament::badge>
                            @endif
                        </div>

                        {{-- Info Tambahan (Target, Frekuensi) --}}
                        <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm mb-4">
                            <div class="text-gray-600 dark:text-gray-400 font-medium">Target:</div>
                            <div class="text-gray-900 dark:text-white font-semibold">
                                {{ $indicator->indicator_target ?? '0' }}%</div>

                            <div class="text-gray-600 dark:text-gray-400 font-medium">Frekuensi:</div>
                            <div class="text-gray-900 dark:text-white">{{ $indicator->indicator_frequency ?? 'N/A' }}
                            </div>

                            @if ($indicator->indicator_monitoring_area)
                                <div class="text-gray-600 dark:text-gray-400 font-medium">Area Monitor:</div>
                                <div class="text-gray-900 dark:text-white">{{ $indicator->indicator_monitoring_area }}
                                </div>
                            @endif
                        </div>

                        {{-- Kategori Status Badges (jika ada) --}}
                        @if ($indicator->statuses->isNotEmpty())
                            <div class="mb-4">
                                <div class="flex items-start gap-1.5 mb-1">
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Status:</span>
                                </div>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($indicator->statuses as $status)
                                        <span
                                            class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold text-white"
                                            style="background-color: {{ $status->warna_badge }};">
                                            {{ $status->nama_status }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Download Form Manual (Pindah ke paling bawah konten) --}}
                        <div class="mt-auto pt-4 border-t border-gray-200 dark:border-gray-700">
                            @if ($indicator->files)
                                <a href="{{ Storage::disk('public')->url($indicator->files) }}" target="_blank"
                                    class="inline-flex items-center gap-1.5 text-xs text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium hover:underline">
                                    <x-heroicon-o-arrow-down-tray class="w-4 h-4" /> Unduh Form Manual
                                </a>
                            @else
                                <div class="inline-flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                    <x-heroicon-o-x-circle class="w-4 h-4 text-danger-500" /> Manual Belum Ada
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Tombol Aksi Footer (Tetap sama) --}}
                    <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                        <div class="flex flex-col gap-2">
                            <div class="flex gap-2">
                                <x-filament::button color="warning" size="sm"
                                    icon="heroicon-o-clipboard-document-check"
                                    wire:click="openProsesModal({{ $indicator->indicator_id }})"
                                    class="flex-1">Proses</x-filament::button>
                                <x-filament::button color="primary" size="sm" icon="heroicon-o-document-chart-bar"
                                    wire:click="openRekapModal({{ $indicator->indicator_id }})"
                                    class="flex-1">Rekap</x-filament::button>
                            </div>
                            <x-filament::button color="danger" size="sm" icon="heroicon-o-chart-pie"
                                wire:click="openPersentaseModal({{ $indicator->indicator_id }})"
                                class="w-full">Persentase</x-filament::button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <x-filament::icon icon="heroicon-o-inbox" class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                    <p class="text-gray-500 dark:text-gray-400">
                        Tidak ada indikator yang sesuai dengan filter atau belum ditugaskan untuk unit Anda.
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Modal Proses Data --}}
    <x-filament::modal id="proses-data-modal" width="6xl" :close-by-clicking-away="false">
        <x-slot name="heading">
            <div class="flex items-center space-x-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <span>{{ $editMode ? '✏️ Edit Data' : '📊 Input Data' }}</span>
            </div>
        </x-slot>
        @if ($selectedIndicator)
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <h3 class="font-bold text-lg text-blue-900 dark:text-blue-100 mb-1">
                    {{ strtoupper($selectedIndicator->indicator_element ?? '') }}</h3>
                <p class="text-sm text-blue-700 dark:text-blue-300">( Unit: <span
                        class="font-semibold">{{ Auth::user()->departemens->first()?->nama_unit ?? 'N/A' }}</span> )
                </p>
            </div>
            <div class="space-y-6">
                <div>{{ $this->prosesDataForm }}</div>
                @if ($selectedIndicator->variables && count($selectedIndicator->variables) > 0)
                    <div>
                        <h4 class="font-bold text-base mb-3 text-gray-900 dark:text-gray-100">Data Variabel</h4>
                        <div class="overflow-x-auto border border-gray-300 dark:border-gray-600 rounded-lg">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-700">
                                        <th class="border-b border-gray-300 dark:border-gray-600 px-4 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300"
                                            style="width: 80px">#</th>
                                        <th class="border-b border-gray-300 dark:border-gray-600 px-4 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300"
                                            style="width: 60px">No</th>
                                        <th
                                            class="border-b border-gray-300 dark:border-gray-600 px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                            Variabel</th>
                                        <th class="border-b border-gray-300 dark:border-gray-600 px-4 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300"
                                            style="width: 200px">Input</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $index = 1; @endphp
                                    @foreach ($selectedIndicator->variables as $variable)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                            <td
                                                class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-center">
                                                @if ($variable->variable_type === 'N')
                                                    <span
                                                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 font-bold text-sm">N</span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 font-bold text-sm">D</span>
                                                @endif
                                            </td>
                                            <td
                                                class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-center font-medium text-gray-900 dark:text-gray-100">
                                                {{ $index }}</td>
                                            <td
                                                class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-gray-800 dark:text-gray-200">
                                                {{ $variable->variable_name }}</td>
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
                @if ($selectedIndicator->indicator_definition)
                    <div class="border-l-4 border-blue-500 pl-4 py-3 bg-gray-50 dark:bg-gray-800 rounded-r">
                        <h4 class="font-bold text-base mb-3 text-gray-900 dark:text-gray-100">Profil Indikator</h4>
                        <div class="grid grid-cols-12 gap-4 text-sm">
                            <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">Definisi</div>
                            <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                {{ $selectedIndicator->indicator_definition }}</div>
                            @if ($selectedIndicator->indicator_source_of_data)
                                <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">Sumber</div>
                                <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                    {{ $selectedIndicator->indicator_source_of_data }}</div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif
        <x-slot name="footerActions">
            <div
                class="flex justify-between items-center gap-3 w-full pt-4 border-t border-gray-300 dark:border-gray-600">
                <x-filament::button type="button" color="gray"
                    wire:click="$dispatch('close-modal', { id: 'proses-data-modal' })"
                    icon="heroicon-o-x-mark">Batal</x-filament::button>
                <div class="flex gap-2">
                    @if (!$editMode)
                        <x-filament::button type="button" color="success" wire:click="simpanDanLanjut"
                            icon="heroicon-o-arrow-path"><svg class="w-4 h-4 mr-1" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>Simpan & Lanjut</x-filament::button>
                    @endif
                    <x-filament::button type="button" color="primary" wire:click="simpanData"
                        icon="heroicon-o-check"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                            </path>
                        </svg>{{ $editMode ? 'Update' : 'Simpan' }}</x-filament::button>
                </div>
            </div>
        </x-slot>
    </x-filament::modal>

    {{-- Modal Persentase --}}
    <x-filament::modal id="persentase-modal" width="7xl">
        <x-slot name="heading">
            <div class="flex items-center space-x-2"><svg class="w-6 h-6 text-rose-600" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                </svg><span>📊 PERSENTASE</span></div>
        </x-slot>
        @if ($selectedIndicatorForReport)
            <div class="mb-6">
                <div
                    class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-lg border border-blue-200 dark:border-blue-800 p-5">
                    <h3 class="font-bold text-xl text-gray-900 dark:text-gray-100 mb-2">Indikator Mutu</h3>
                    <h2 class="font-bold text-2xl text-blue-900 dark:text-blue-100 mb-3">
                        {{ $selectedIndicatorForReport->indicator_element }}</h2>
                    <div class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                        @if ($selectedIndicatorForReport->indicator_type || $selectedIndicatorForReport->indicator_monitoring_area)
                            <p><span class="font-semibold text-pink-600 dark:text-pink-400">Tipe:
                                    {{ $selectedIndicatorForReport->indicator_type }} | Area:
                                    {{ $selectedIndicatorForReport->indicator_monitoring_area ?? 'N/A' }}</span></p>
                        @endif
                        <p class="text-gray-600 dark:text-gray-400">
                            Unit: <span class="font-semibold">
                                @if ($selectedIndicatorForReport->departemens && $selectedIndicatorForReport->departemens->count() > 0)
                                    {{ $selectedIndicatorForReport->departemens->pluck('nama_unit')->join(', ') }}
                                @else
                                    Semua
                                @endif
                            </span></p>
                    </div>
                </div>
            </div>
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div><label
                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tahun</label><select
                            wire:model.live="selectedYear"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            @for ($year = now()->year; $year >= 2020; $year--)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                    <div><label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Bulan
                            (Opsional)</label><select wire:model.live="selectedMonth"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <option value="">Semua Bulan</option>
                            @foreach (['01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu', '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des'] as $num => $name)
                                <option value="{{ $num }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end"><x-filament::button icon="heroicon-o-printer" color="gray"
                            onclick="window.print()" class="w-full">Print</x-filament::button></div>
                </div>
            </div>
            <div class="overflow-x-auto border border-gray-300 dark:border-gray-600 rounded-lg">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600">
                            <th class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center font-bold text-gray-900 dark:text-gray-100"
                                rowspan="2" style="min-width: 120px">Judul \ Bulan</th>
                            @for ($month = 1; $month <= 12; $month++)
                                <th class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center font-semibold text-gray-900 dark:text-gray-100"
                                    style="min-width: 80px">
                                    {{ \Carbon\Carbon::create()->month($month)->shortMonthName }} {{ $selectedYear }}
                                </th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/20">
                            <td
                                class="border border-gray-300 dark:border-gray-600 px-4 py-3 font-bold text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800">
                                NUMERATOR</td>
                            @for ($month = 1; $month <= 12; $month++)
                                @php
                                    $key = str_pad($month, 2, '0', STR_PAD_LEFT) . ' ' . $selectedYear;
                                    $data = $reportData->get($key);
                                @endphp
                                <td
                                    class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center text-gray-900 dark:text-gray-100">
                                    {{ $data['numerator'] ?? '-' }}</td>
                            @endfor
                        </tr>
                        <tr class="hover:bg-green-50 dark:hover:bg-green-900/20">
                            <td
                                class="border border-gray-300 dark:border-gray-600 px-4 py-3 font-bold text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-800">
                                DENOMINATOR</td>
                            @for ($month = 1; $month <= 12; $month++)
                                @php
                                    $key = str_pad($month, 2, '0', STR_PAD_LEFT) . ' ' . $selectedYear;
                                    $data = $reportData->get($key);
                                @endphp <td
                                    class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center text-gray-900 dark:text-gray-100">
                                    {{ $data['denominator'] ?? '-' }}</td>
                            @endfor
                        </tr>
                        <tr class="bg-yellow-50 dark:bg-yellow-900/20 hover:bg-yellow-100 dark:hover:bg-yellow-900/30">
                            <td
                                class="border border-gray-300 dark:border-gray-600 px-4 py-3 font-bold text-gray-900 dark:text-gray-100">
                                CAPAIAN (%)</td>
                            @for ($month = 1; $month <= 12; $month++)
                                @php
                                    $key = str_pad($month, 2, '0', STR_PAD_LEFT) . ' ' . $selectedYear;
                                    $data = $reportData->get($key);
                                @endphp <td
                                    class="border border-gray-300 dark:border-gray-600 px-3 py-2 text-center font-bold text-blue-700 dark:text-blue-300">
                                    {{ isset($data['persentase']) ? number_format($data['persentase'], 2) : '-' }}</td>
                            @endfor
                        </tr>
                    </tbody>
                </table>
            </div>
            @if ($reportData->isEmpty())
                <div
                    class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-700 rounded-lg">
                    <div class="flex items-center gap-3"><svg class="w-6 h-6 text-yellow-600" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                        <div>
                            <h4 class="font-bold text-yellow-800 dark:text-yellow-200">Belum Ada Data</h4>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300">Belum ada data untuk periode ini.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-300 dark:border-gray-600 mt-6">
                <x-filament::button type="button" color="gray"
                    wire:click="$dispatch('close-modal', { id: 'persentase-modal' })">Tutup</x-filament::button>
            </div>
        @endif
    </x-filament::modal>

    {{-- Modal Rekap --}}
    <x-filament::modal id="rekap-modal" width="7xl">
        <x-slot name="heading">
            <div class="flex items-center space-x-2"><svg class="w-6 h-6 text-purple-600" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg><span>📋 Rekap Pengisian</span></div>
        </x-slot>
        @if ($selectedIndicatorForRekap)
            <div class="mb-6">
                <div
                    class="bg-gradient-to-r from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 rounded-lg border border-purple-200 dark:border-purple-800 p-5">
                    <h3 class="font-bold text-lg text-purple-900 dark:text-purple-100 mb-2">Rekap Pengisian IMUT</h3>
                    <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 mb-3">
                        {{ $selectedIndicatorForRekap->indicator_element }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div><span class="font-semibold text-gray-700 dark:text-gray-300">Unit:</span> <span
                                class="text-gray-900 dark:text-gray-100">{{ Auth::user()->departemens->first()?->nama_unit ?? 'N/A' }}</span>
                        </div>
                        <div><span class="font-semibold text-gray-700 dark:text-gray-300">Total Data:</span> <span
                                class="text-gray-900 dark:text-gray-100 font-bold">{{ count($rekapData) }}
                                Record</span></div>
                    </div>
                </div>
            </div>
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div><label
                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tahun</label><select
                            wire:model.live="rekapYear"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            @for ($year = now()->year; $year >= 2020; $year--)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                    <div><label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Bulan
                            (Opsional)</label><select wire:model.live="rekapMonth"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <option value="">Semua Bulan</option>
                            @foreach (['01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu', '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des'] as $num => $name)
                                <option value="{{ $num }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end"><x-filament::button icon="heroicon-o-printer" color="gray"
                            onclick="window.print()" class="w-full">Print</x-filament::button></div>
                    <div class="flex items-end"><x-filament::button icon="heroicon-o-arrow-down-tray" color="success"
                            disabled class="w-full">Export</x-filament::button></div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600">
                        <div class="px-5 py-4 border-b border-gray-300 dark:border-gray-600">
                            <h3 class="font-bold text-lg text-gray-900 dark:text-gray-100">Rekap Data</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-700">
                                        <th
                                            class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center font-semibold text-gray-900 dark:text-gray-100">
                                            Judul</th>
                                        <th
                                            class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center font-semibold text-gray-900 dark:text-gray-100">
                                            Unit</th>
                                        <th
                                            class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center font-semibold text-gray-900 dark:text-gray-100">
                                            Total</th>
                                        <th class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center font-semibold text-gray-900 dark:text-gray-100"
                                            style="width: 180px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rekapData as $data)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td
                                                class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-gray-900 dark:text-gray-100">
                                                {{ $selectedIndicatorForRekap->indicator_element }}<div
                                                    class="text-xs text-gray-500 dark:text-gray-400 mt-1">📅
                                                    {{ \Carbon\Carbon::parse($data['tanggal'])->format('d M Y') }}
                                                </div>
                                            </td>
                                            <td
                                                class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center text-gray-900 dark:text-gray-100">
                                                {{ $data['unit'] }}</td>
                                            <td
                                                class="border border-gray-300 dark:border-gray-600 px-4 py-3 text-center">
                                                <div class="space-y-1">
                                                    <div class="text-gray-700 dark:text-gray-300 text-xs">N: <span
                                                            class="font-semibold">{{ $data['numerator'] }}</span>
                                                    </div>
                                                    <div class="text-gray-700 dark:text-gray-300 text-xs">D: <span
                                                            class="font-semibold">{{ $data['denominator'] }}</span>
                                                    </div>
                                                    <div class="font-bold text-blue-700 dark:text-blue-300">
                                                        {{ number_format($data['persentase'], 2) }}%</div>
                                                </div>
                                            </td>
                                            <td class="border border-gray-300 dark:border-gray-600 px-4 py-3">
                                                <div class="flex gap-2 justify-center items-center"><x-filament::button
                                                        size="xs" color="warning" icon="heroicon-o-pencil"
                                                        wire:click="openEditModal({{ $data['id'] }})"
                                                        tooltip="Edit">Edit</x-filament::button><x-filament::button
                                                        size="xs" color="danger" icon="heroicon-o-trash"
                                                        wire:click="hapusData({{ $data['id'] }})"
                                                        wire:confirm="Hapus data tanggal {{ \Carbon\Carbon::parse($data['tanggal'])->format('d M Y') }}?"
                                                        tooltip="Hapus">Hapus</x-filament::button></div>
                                            </td>
                                        </tr>
                                    @empty <tr>
                                            <td colspan="4"
                                                class="border border-gray-300 dark:border-gray-600 px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                                <div class="flex flex-col items-center gap-2"><svg
                                                        class="w-12 h-12 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                        </path>
                                                    </svg>
                                                    <p class="font-semibold">Tidak Ada Data</p>
                                                    <p class="text-xs">Belum ada data untuk periode ini.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600">
                        <div class="px-5 py-4 border-b border-gray-300 dark:border-gray-600">
                            <h3 class="font-bold text-lg text-gray-900 dark:text-gray-100">Tanggal Pengisian</h3>
                        </div>
                        <div class="p-5">
                            @if (count($rekapData) > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($rekapData as $data)
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">📅
                                            {{ \Carbon\Carbon::parse($data['tanggal'])->format('d M Y') }}</span>
                                    @endforeach
                                </div>
                            @else<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Belum ada
                                    data</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600">
                        <div class="px-5 py-4 border-b border-gray-300 dark:border-gray-600">
                            <h3 class="font-bold text-base text-gray-900 dark:text-gray-100">Statistik Bulanan</h3>
                        </div>
                        <div class="p-5">
                            @if (count($rekapStatistics) > 0)
                                <div class="space-y-3">
                                    @foreach ($rekapStatistics as $stat)
                                        <div
                                            class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                            <span
                                                class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $stat['bulan'] }}</span>
                                            <span
                                                class="text-sm font-bold text-blue-700 dark:text-blue-300">{{ $stat['jumlah_pengisian'] }}
                                                Data</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Belum ada
                                    statistik</p>
                            @endif
                        </div>
                    </div>
                    <div
                        class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-lg border border-blue-200 dark:border-blue-700 p-5">
                        <div class="flex items-start gap-3"><svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="font-bold text-sm text-blue-900 dark:text-blue-100 mb-1">Info</h4>
                                <p class="text-xs text-blue-800 dark:text-blue-200">Gunakan tombol Edit/Hapus untuk
                                    mengelola data.</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600 p-5">
                        <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100 mb-4">Ringkasan</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center"><span
                                    class="text-xs text-gray-600 dark:text-gray-400">Total Pengisian</span> <span
                                    class="text-lg font-bold text-blue-700 dark:text-blue-300">{{ count($rekapData) }}</span>
                            </div>
                            <div class="flex justify-between items-center"><span
                                    class="text-xs text-gray-600 dark:text-gray-400">Periode</span> <span
                                    class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $rekapMonth ? \Carbon\Carbon::createFromFormat('m', $rekapMonth)->format('F') : 'Semua' }}
                                    {{ $rekapYear }}</span></div>
                            @if (count($rekapData) > 0)
                                <div class="flex justify-between items-center"><span
                                        class="text-xs text-gray-600 dark:text-gray-400">Rata-rata Capaian</span> <span
                                        class="text-lg font-bold text-green-700 dark:text-green-300">{{ number_format(collect($rekapData)->avg('persentase'), 2) }}%</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-300 dark:border-gray-600 mt-6">
                <x-filament::button type="button" color="gray"
                    wire:click="$dispatch('close-modal', { id: 'rekap-modal' })">Tutup</x-filament::button>
            </div>
        @endif
    </x-filament::modal>

    {{-- Script --}}
    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('open-edit-after-close', () => {
                    setTimeout(() => {
                        Livewire.dispatch('open-modal', {
                            id: 'proses-data-modal'
                        });
                    }, 300);
                });
            });
        </script>
    @endpush
</x-filament-panels::page>
