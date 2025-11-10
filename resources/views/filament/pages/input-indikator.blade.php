<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Input Indikator Mutu
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Kelola indikator mutu untuk unit Anda:
                {{ $userUnits->pluck('nama_unit')->join(', ') ?? 'Tidak ada' }}
            </p>
        </div>

        {{-- Card Filter --}}
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        📍 Area IMUT
                    </label>
                    <select wire:model.live="filterCategory"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Area IMUT</option>
                        @foreach ($availableCategories as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Unit --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        🏥 Unit
                    </label>
                    <select wire:model.live="filterUnit"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Unit Saya</option>
                        @foreach ($userUnits ?? [] as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-filament::card>

        {{-- Info User & Unit --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 mb-4 rounded">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                👤 <strong>User:</strong> {{ Auth::user()->name }} |
                🏥 <strong>Unit Anda:</strong>
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
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-lg transition flex flex-col">

                    <div class="p-6 flex flex-col flex-1">

                        {{-- Judul Indikator --}}
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 leading-tight">
                            {{ $indicator->indicator_name }}
                        </h3>

                        {{-- Badges Section --}}
                        <div class="flex flex-wrap gap-2 mb-4">
                            {{-- Badge Tipe Indikator --}}
                            @php
                                $indicatorTypeTooltip = !empty($indicator->badge_tooltips['indicator_type'])
                                    ? $indicator->badge_tooltips['indicator_type']
                                    : null;
                            @endphp
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200"
                                @if ($indicatorTypeTooltip) title="{{ $indicatorTypeTooltip }}" @endif>
                                {{ $indicator->indicator_type }}
                            </span>

                            {{-- Badge Kategori Area IMUT --}}
                            @if ($indicator->imutCategory)
                                @php
                                    $imutCategoryTooltip = !empty($indicator->badge_tooltips['imut_category'])
                                        ? $indicator->badge_tooltips['imut_category']
                                        : null;
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                                    @if ($imutCategoryTooltip) title="{{ $imutCategoryTooltip }}" @endif>
                                    Area: {{ $indicator->imutCategory->imut_name_category }}
                                </span>
                            @endif

                            {{-- Unit Badges dengan tooltip --}}
                            @if ($indicator->units->isNotEmpty())
                                @php
                                    $unitFirst = $indicator->units->first();
                                    $unitNamaFirst = $unitFirst ? $unitFirst->nama_unit : null;
                                    $otherUnits =
                                        $indicator->units->count() > 1
                                            ? $indicator->units->slice(1)->pluck('nama_unit')->join(', ')
                                            : null;
                                    $titleTooltip = $unitNamaFirst;
                                    if ($otherUnits) {
                                        $titleTooltip .= ', ' . $otherUnits;
                                    }
                                    $unitTooltip = !empty($indicator->badge_tooltips['unit'])
                                        ? $indicator->badge_tooltips['unit']
                                        : $titleTooltip;
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200"
                                    @if ($unitTooltip) title="{{ $unitTooltip }}" @endif>
                                    Unit: {{ $unitNamaFirst }}
                                    @if ($indicator->units->count() > 1)
                                        (+{{ $indicator->units->count() - 1 }})
                                    @endif
                                </span>
                            @endif
                        </div>

                        {{-- Info Tambahan (Target, Frekuensi) --}}
                        <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm mb-4">
                            <div class="text-gray-600 dark:text-gray-400 font-medium">Target:</div>
                            @php
                                $targetTooltip = !empty($indicator->badge_tooltips['target'])
                                    ? $indicator->badge_tooltips['target']
                                    : null;
                            @endphp
                            <div class="text-gray-900 dark:text-white font-semibold"
                                @if ($targetTooltip) title="{{ $targetTooltip }}" @endif>
                                {{ $indicator->indicator_target ?? '0' }}
                                @if ($indicator->satuan_pengukuran == 'Persentase')
                                    %
                                @else
                                    {{ $indicator->satuan_pengukuran }}
                                @endif
                            </div>

                            <div class="text-gray-600 dark:text-gray-400 font-medium">Frekuensi:</div>
                            @php
                                $frequencyTooltip = !empty($indicator->badge_tooltips['frequency'])
                                    ? $indicator->badge_tooltips['frequency']
                                    : null;
                            @endphp
                            <div class="text-gray-900 dark:text-white"
                                @if ($frequencyTooltip) title="{{ $frequencyTooltip }}" @endif>
                                {{ $indicator->indicator_frequency ?? 'N/A' }}</div>
                        </div>

                        {{-- Kategori Status Badges (jika ada) --}}
                        @if ($indicator->statuses->isNotEmpty())
                            <div class="mb-4">
                                <div class="flex items-start gap-1.5 mb-1">
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Status:</span>
                                </div>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($indicator->statuses as $status)
                                        @php
                                            $statusTooltip = !empty($status->badge_tooltip)
                                                ? $status->badge_tooltip
                                                : null;
                                        @endphp
                                        <span
                                            class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold text-white"
                                            style="background-color: {{ $status->warna_badge }};"
                                            @if ($statusTooltip) title="{{ $statusTooltip }}" @endif>
                                            {{ $status->nama_status }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Download Form Manual --}}
                        <div class="mt-auto pt-4 border-t border-gray-200 dark:border-gray-700">
                            @if ($indicator->files)
                                @php
                                    $filesTooltip = !empty($indicator->badge_tooltips['files'])
                                        ? $indicator->badge_tooltips['files']
                                        : null;
                                @endphp
                                <a href="{{ Storage::disk('public')->url($indicator->files) }}" target="_blank"
                                    class="inline-flex items-center gap-1.5 text-xs text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium hover:underline"
                                    @if ($filesTooltip) title="{{ $filesTooltip }}" @endif>
                                    <span class="inline-block w-4 h-4">⬇️</span> Unduh Form Manual
                                </a>
                            @else
                                <div
                                    class="inline-flex items-center gap-1.5 text-xs text-warning-600 dark:text-warning-400">
                                    <span class="inline-block w-4 h-4">⚠️</span> Manual Belum Ada
                                </div>
                            @endif
                        </div>

                        {{-- ✅ FITUR BARU: Statistik Indikator --}}
                        @php
                            $stats = $this->getIndicatorStats($indicator->indicator_id);
                            $isWajib = strtolower($indicator->imutCategory?->imut_name_category ?? '') === 'wajib';
                        @endphp

                        @if ($stats)
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700 space-y-2">
                                {{-- Info Terakhir Input & Total Record --}}
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-gray-700 dark:text-gray-300">
                                        📅 Terakhir Input:
                                    </span>
                                    <span class="font-semibold text-gray-900 dark:text-white">
                                        @if ($stats['last_input_formatted'])
                                            {{ $stats['last_input_formatted'] }}
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">Belum ada</span>
                                        @endif
                                    </span>
                                </div>

                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-gray-700 dark:text-gray-300">
                                        📊 Record Bulan Ini:
                                    </span>
                                    <span class="font-semibold text-gray-900 dark:text-white">
                                        {{ $stats['total_records'] }} data
                                    </span>
                                </div>

                                {{-- Progress Bar & Pencapaian --}}
                                @if ($stats['total_records'] > 0 && $indicator->indicator_target)
                                    <div class="space-y-1">
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="text-gray-700 dark:text-gray-300">Pencapaian:</span>
                                            <span
                                                class="font-bold {{ $stats['achievement'] >= $indicator->indicator_target ? 'text-green-600 dark:text-green-400' : 'text-orange-600 dark:text-orange-400' }}">
                                                {{ number_format($stats['achievement'], 2) }}%
                                            </span>
                                        </div>
                                        {{-- Progress Bar --}}
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="h-2 rounded-full {{ $stats['achievement'] >= $indicator->indicator_target ? 'bg-green-500 dark:bg-green-400' : 'bg-orange-500 dark:bg-orange-400' }}"
                                                style="width: {{ min($stats['achievement'], 100) }}%">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Badge Status Pencapaian --}}
                                    @if ($stats['achievement'] >= $indicator->indicator_target)
                                        <div
                                            class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                            ✅ Target Tercapai
                                        </div>
                                    @elseif ($stats['achievement'] >= $indicator->indicator_target * 0.8)
                                        <div
                                            class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100">
                                            ⚡ Mendekati Target
                                        </div>
                                    @else
                                        <div
                                            class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                            ⚠️ Belum Tercapai
                                        </div>
                                    @endif
                                @endif

                                {{-- ✅ WARNING DEADLINE untuk Indikator WAJIB --}}
                                @if ($isWajib && $stats['deadline_warning'])
                                    @php
                                        $warning = $stats['deadline_warning'];
                                    @endphp

                                    @if ($warning['level'] === 'expired')
                                        <div
                                            class="mt-2 p-2 rounded-lg bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700">
                                            <div
                                                class="flex items-center gap-2 text-xs font-bold text-red-800 dark:text-red-200">
                                                <span class="text-base">🚨</span>
                                                <span>TERLAMBAT {{ $warning['days'] }} HARI!</span>
                                            </div>
                                            <div class="text-xs text-red-700 dark:text-red-300 mt-1">
                                                Deadline input sudah terlewat
                                            </div>
                                        </div>
                                    @elseif ($warning['level'] === 'danger')
                                        <div
                                            class="mt-2 p-2 rounded-lg bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700">
                                            <div
                                                class="flex items-center gap-2 text-xs font-bold text-red-800 dark:text-red-200">
                                                <span class="text-base">⏰</span>
                                                <span>DEADLINE {{ $warning['days'] }} HARI LAGI!</span>
                                            </div>
                                            <div class="text-xs text-red-700 dark:text-red-300 mt-1">
                                                Batas input: 10 bulan depan
                                            </div>
                                        </div>
                                    @elseif ($warning['level'] === 'warning')
                                        <div
                                            class="mt-2 p-2 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 border border-yellow-300 dark:border-yellow-700">
                                            <div
                                                class="flex items-center gap-2 text-xs font-bold text-yellow-800 dark:text-yellow-200">
                                                <span class="text-base">⚠️</span>
                                                <span>Deadline {{ $warning['days'] }} hari lagi</span>
                                            </div>
                                            <div class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">
                                                Segera lengkapi data bulan ini
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Tombol Aksi Footer --}}
                    <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                        <div class="grid grid-cols-3 gap-2">
                            @php
                                $prosesButtonTooltip = !empty($indicator->badge_tooltips['proses_button'])
                                    ? $indicator->badge_tooltips['proses_button']
                                    : 'Input data indikator';
                                $rekapButtonTooltip = !empty($indicator->badge_tooltips['rekap_button'])
                                    ? $indicator->badge_tooltips['rekap_button']
                                    : 'Lihat rekap & kelola data';
                                $persentaseButtonTooltip = !empty($indicator->badge_tooltips['persentase_button'])
                                    ? $indicator->badge_tooltips['persentase_button']
                                    : 'Lihat grafik persentase';
                            @endphp

                            <button type="button"
                                class="inline-flex items-center justify-center px-3 py-2 rounded-md text-sm font-medium bg-yellow-100 text-yellow-800 hover:bg-yellow-200 dark:bg-yellow-400 dark:text-gray-900 dark:hover:bg-yellow-300 transition-colors"
                                wire:click="openProsesModal({{ $indicator->indicator_id }})"
                                title="{{ $prosesButtonTooltip }}">
                                Proses
                            </button>

                            <button type="button"
                                class="inline-flex items-center justify-center px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800 transition-colors"
                                wire:click="openRekapModal({{ $indicator->indicator_id }})"
                                title="{{ $rekapButtonTooltip }}">
                                Rekap
                            </button>

                            <button type="button"
                                class="inline-flex items-center justify-center px-3 py-2 rounded-md text-sm font-medium bg-pink-100 text-pink-800 hover:bg-pink-200 dark:bg-pink-900 dark:text-pink-200 dark:hover:bg-pink-800 transition-colors"
                                wire:click="openPersentaseModal({{ $indicator->indicator_id }})"
                                title="{{ $persentaseButtonTooltip }}">
                                Persentase
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="w-16 h-16 mx-auto text-gray-400 mb-4 text-4xl">📥</div>
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
                <span class="text-xl">📊</span>
                <span>{{ $editMode ? '✏️ Edit Data' : 'Input Data' }}</span>
            </div>
        </x-slot>

        @if ($selectedIndicator)
            {{-- 
                ============================================================
                PERUBAHAN STRUKTUR: 
                Grid sekarang hanya untuk input dan sidebar.
                Definisi akan diletakkan di luar grid di bawah.
                ============================================================
            --}}

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- KOLOM KIRI (UTAMA) --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Section 1: Input Data Variabel --}}
                    <x-filament::section collapsible icon="heroicon-o-table-cells">
                        <x-slot name="header">
                            Input Data Variabel
                        </x-slot>
                        <x-slot name="description">
                            Isi nilai numerator dan denominator pada tabel di bawah ini.
                        </x-slot>

                        {{-- Isi section: Tabel yang sudah ada --}}
                        @if ($selectedIndicator->variables && count($selectedIndicator->variables) > 0)
                            <div class="overflow-x-auto border border-gray-300 dark:border-gray-600 rounded-lg">
                                <table class="w-full">
                                    {{-- ... (Isi tabel tetap sama) ... --}}
                                    <thead class="bg-gray-100 dark:bg-gray-800">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-300 dark:border-gray-600">
                                                No
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-300 dark:border-gray-600">
                                                Nama Variabel
                                            </th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-300 dark:border-gray-600">
                                                Tipe
                                            </th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-300 dark:border-gray-600 w-32">
                                                Nilai
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $index = 1; @endphp
                                        @foreach ($selectedIndicator->variables as $variable)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                                <td
                                                    class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $index }}
                                                </td>
                                                <td class="border-b border-gray-200 dark:border-gray-700 px-4 py-3">
                                                    @php $badgeTooltip = !empty($variable->badge_tooltip) ? $variable->badge_tooltip : null; @endphp
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100"
                                                        @if ($badgeTooltip) title="{{ $badgeTooltip }}" @endif>
                                                        {{ $variable->variable_name ?? '-' }}
                                                    </div>
                                                    @if ($variable->variable_description)
                                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                            {{ $variable->variable_description }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($variable->variable_type === 'N')
                                                        @php $numeratorTooltip = !empty($variable->badge_tooltip_numerator) ? $variable->badge_tooltip_numerator : null; @endphp
                                                        <div
                                                            class="mt-2 p-2 rounded-lg bg-red-100 dark:bg-red-900/50 border border-red-300 dark:border-red-600">
                                                            <div
                                                                class="flex items-center gap-2 text-xs font-bold text-red-900 dark:text-red-100">
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                                                                    @if ($numeratorTooltip) title="{{ $numeratorTooltip }}" @endif>
                                                                    Numerator
                                                                </span>
                                                            </div>
                                                            <div class="text-xs text-red-800 dark:text-red-200 mt-1">
                                                                <!-- Additional numerator info if needed -->
                                                            </div>
                                                        </div>
                                                    @else
                                                        @php $denominatorTooltip = !empty($variable->badge_tooltip_denominator) ? $variable->badge_tooltip_denominator : null; @endphp
                                                        <div
                                                            class="mt-2 p-2 rounded-lg bg-yellow-100 dark:bg-yellow-900/50 border border-yellow-300 dark:border-yellow-600">
                                                            <div
                                                                class="flex items-center gap-2 text-xs font-bold text-yellow-900 dark:text-yellow-100">
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-200 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100"
                                                                    @if ($denominatorTooltip) title="{{ $denominatorTooltip }}" @endif>
                                                                    Denominator
                                                                </span>
                                                            </div>
                                                            <div
                                                                class="text-xs text-yellow-800 dark:text-yellow-200 mt-1">
                                                                <!-- Additional denominator info if needed -->
                                                            </div>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="border-b border-gray-200 dark:border-gray-700 px-4 py-3">
                                                    @php
                                                        $inputTooltip = !empty($variable->input_tooltip)
                                                            ? $variable->input_tooltip
                                                            : null;
                                                    @endphp
                                                    <input type="number"
                                                        wire:model="prosesData.{{ $variable->variable_type === 'N' ? 'numerator' : 'denominator' }}_{{ $variable->variable_id }}"
                                                        placeholder="0" step="any" min="0"
                                                        class="w-full px-3 py-2 text-center border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition"
                                                        required
                                                        @if ($inputTooltip) title="{{ $inputTooltip }}" @endif />
                                                </td>
                                            </tr>
                                            @php $index++; @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div
                                class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                    ⚠️ Indikator ini belum memiliki variabel (Numerator/Denominator).
                                </p>
                            </div>
                        @endif
                    </x-filament::section>

                    {{-- Tombol Aksi --}}
                    <div class="flex justify-end gap-2">
                        <button type="button"
                            class="inline-flex items-center px-3 py-1.5 rounded-md text-sm bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-600"
                            wire:click="$dispatch('close-modal', { id: 'proses-data-modal' })">Batal</button>
                        <button type="button"
                            class="inline-flex items-center px-3 py-1.5 rounded-md text-sm bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100 hover:bg-green-200 dark:hover:bg-green-600"
                            wire:click="simpanDanLanjut">Simpan & Lanjut</button>
                        <button type="button"
                            class="inline-flex items-center px-3 py-1.5 rounded-md text-sm bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100 hover:bg-blue-200 dark:hover:bg-blue-600"
                            wire:click="simpanData">Simpan</button>
                    </div>

                    {{-- 
                        ============================================================
                        PERUBAHAN UTAMA: Section Profil Indikator DIHAPUS dari sini
                        ============================================================
                    --}}

                </div>

                {{-- KOLOM KANAN (SIDEBAR) --}}
                <div class="lg:col-span-1 space-y-6">

                    {{-- Info Indikator & Unit --}}
                    <div
                        class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <h3 class="font-bold text-lg text-blue-900 dark:text-blue-100 mb-1">
                            {{ strtoupper($selectedIndicator->indicator_name ?? '') }}</h3>
                        <p class="text-sm text-blue-700 dark:text-blue-300">( Unit: <span class="font-semibold">
                                {{ $userUnits->first()?->nama_unit ?? 'N/A' }}
                            </span> )
                        </p>
                    </div>

                    {{-- Section 3: Detail Laporan --}}
                    <x-filament::section collapsible icon="heroicon-o-calendar-days">
                        <x-slot name="header">
                            Detail Laporan
                        </x-slot>
                        <x-slot name="description">
                            Pilih tanggal pelaporan untuk data yang Anda masukkan.
                        </x-slot>

                        {{-- Isi section: Form Tanggal Lapor --}}
                        {{ $this->prosesDataForm }}
                    </x-filament::section>

                </div>
            </div>
            {{-- 
                ============================================================
                PERUBAHAN UTAMA: Section Profil Indikator DIPINDAHKAN ke sini
                ============================================================
            --}}
            <div class="mt-6">
                @if ($selectedIndicator)
                    <x-filament::section collapsible collapsed icon="heroicon-o-information-circle">
                        <x-slot name="header">
                            📋 Profil Indikator
                        </x-slot>
                        <x-slot name="description">
                            Lihat definisi dan sumber data sebagai panduan pengisian.
                        </x-slot>

                        {{-- Isi section: Informasi Lengkap Indikator --}}
                        <div class="space-y-4">
                            {{-- Definisi --}}
                            @if ($selectedIndicator->indicator_definition)
                                <div
                                    class="grid grid-cols-12 gap-4 text-sm pb-3 border-b border-gray-200 dark:border-gray-700">
                                    <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">
                                        📖 Definisi
                                    </div>
                                    <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                        {{ $selectedIndicator->indicator_definition }}
                                    </div>
                                </div>
                            @endif

                            {{-- Dimensi Mutu --}}
                            @if ($selectedIndicator->dimensi_mutu)
                                <div
                                    class="grid grid-cols-12 gap-4 text-sm pb-3 border-b border-gray-200 dark:border-gray-700">
                                    <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">
                                        🎯 Dimensi Mutu
                                    </div>
                                    <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                        {{ $selectedIndicator->dimensi_mutu }}
                                    </div>
                                </div>
                            @endif

                            {{-- Tujuan --}}
                            @if ($selectedIndicator->tujuan)
                                <div
                                    class="grid grid-cols-12 gap-4 text-sm pb-3 border-b border-gray-200 dark:border-gray-700">
                                    <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">
                                        🎓 Tujuan
                                    </div>
                                    <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                        {{ $selectedIndicator->tujuan }}
                                    </div>
                                </div>
                            @endif

                            {{-- Kriteria Inklusif --}}
                            @if ($selectedIndicator->indicator_criteria_inclusive)
                                <div
                                    class="grid grid-cols-12 gap-4 text-sm pb-3 border-b border-gray-200 dark:border-gray-700">
                                    <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">
                                        ✅ Kriteria Inklusif
                                    </div>
                                    <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                        {{ $selectedIndicator->indicator_criteria_inclusive }}
                                    </div>
                                </div>
                            @endif

                            {{-- Kriteria Eksklusif --}}
                            @if ($selectedIndicator->indicator_criteria_exclusive)
                                <div
                                    class="grid grid-cols-12 gap-4 text-sm pb-3 border-b border-gray-200 dark:border-gray-700">
                                    <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">
                                        ❌ Kriteria Eksklusif
                                    </div>
                                    <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                        {{ $selectedIndicator->indicator_criteria_exclusive }}
                                    </div>
                                </div>
                            @endif

                            {{-- Sumber Data --}}
                            @if ($selectedIndicator->indicator_source_of_data)
                                <div
                                    class="grid grid-cols-12 gap-4 text-sm pb-3 border-b border-gray-200 dark:border-gray-700">
                                    <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">
                                        📊 Sumber Data
                                    </div>
                                    <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                        {{ $selectedIndicator->indicator_source_of_data }}
                                    </div>
                                </div>
                            @endif

                            {{-- Satuan Pengukuran --}}
                            @if ($selectedIndicator->satuan_pengukuran)
                                <div
                                    class="grid grid-cols-12 gap-4 text-sm pb-3 border-b border-gray-200 dark:border-gray-700">
                                    <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">
                                        📏 Satuan Pengukuran
                                    </div>
                                    <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                        {{ $selectedIndicator->satuan_pengukuran }}
                                    </div>
                                </div>
                            @endif

                            {{-- Target --}}
                            @if ($selectedIndicator->indicator_target)
                                <div
                                    class="grid grid-cols-12 gap-4 text-sm pb-3 border-b border-gray-200 dark:border-gray-700">
                                    <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">
                                        🎯 Target
                                    </div>
                                    <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            {{ $selectedIndicator->indicator_target }}%
                                        </span>
                                    </div>
                                </div>
                            @endif

                            {{-- Tipe Indikator --}}
                            @if ($selectedIndicator->indicator_type)
                                <div
                                    class="grid grid-cols-12 gap-4 text-sm pb-3 border-b border-gray-200 dark:border-gray-700">
                                    <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">
                                        🏷️ Tipe Indikator
                                    </div>
                                    <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                        {{ $selectedIndicator->indicator_type }}
                                    </div>
                                </div>
                            @endif

                            {{-- Frekuensi Monitoring --}}
                            @if ($selectedIndicator->indicator_frequency)
                                <div
                                    class="grid grid-cols-12 gap-4 text-sm pb-3 border-b border-gray-200 dark:border-gray-700">
                                    <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">
                                        🔄 Frekuensi Monitoring
                                    </div>
                                    <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                        {{ $selectedIndicator->indicator_frequency }}
                                    </div>
                                </div>
                            @endif

                            {{-- Area Monitoring --}}
                            @if ($selectedIndicator->indicator_monitoring_area)
                                <div class="grid grid-cols-12 gap-4 text-sm">
                                    <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">
                                        🏥 Area Monitoring
                                    </div>
                                    <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                        {{ $selectedIndicator->indicator_monitoring_area }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </x-filament::section>
                @endif
            </div>

        @endif

        {{-- Slot footerActions sudah dikosongkan pada langkah sebelumnya --}}

    </x-filament::modal>

    {{-- Modal Persentase --}}
    <x-filament::modal id="persentase-modal" width="7xl">
        <x-slot name="heading">
            <div class="flex items-center space-x-2">
                <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
                <span>📊 Data Persentase Indikator</span>
            </div>
        </x-slot>
        @if ($selectedIndicatorForReport)
            <div class="mb-6">
                <div
                    class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-lg border border-blue-200 dark:border-blue-800 p-5">
                    <h3 class="font-bold text-xl text-gray-900 dark:text-gray-100 mb-2">Indikator Mutu</h3>
                    <h2 class="font-bold text-2xl text-blue-900 dark:text-blue-100 mb-3">
                        {{ $selectedIndicatorForReport->indicator_name }}</h2>
                    <div class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                        @if ($selectedIndicatorForReport->indicator_type)
                            <p><span class="font-semibold text-pink-600 dark:text-pink-400">Tipe:
                                    {{ $selectedIndicatorForReport->indicator_type }}</span></p>
                        @endif
                        <p class="text-gray-600 dark:text-gray-400">
                            Unit: <span class="font-semibold">
                                @if ($selectedIndicatorForReport->units && $selectedIndicatorForReport->units->count() > 0)
                                    {{ $selectedIndicatorForReport->units->pluck('nama_unit')->join(', ') }}
                                @else
                                    Semua
                                @endif
                            </span></p>
                        <p class="text-gray-600 dark:text-gray-400">
                            Target: <span class="font-semibold text-green-600 dark:text-green-400">
                                {{ $selectedIndicatorForReport->indicator_target ?? '0' }}
                                @if ($selectedIndicatorForReport->satuan_pengukuran == 'Persentase')
                                    %
                                @else
                                    {{ $selectedIndicatorForReport->satuan_pengukuran }}
                                @endif
                            </span></p>
                    </div>
                </div>
            </div>

            {{-- Filter Tahun dan Bulan --}}
            <div
                class="mb-6 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            📅 Tahun
                        </label>
                        <select wire:model.live="selectedYear"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-pink-500 dark:bg-gray-700 dark:text-white">
                            @for ($year = now()->year; $year >= now()->year - 5; $year--)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            🗓️ Bulan
                        </label>
                        <select wire:model.live="selectedMonth"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-pink-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Semua Bulan</option>
                            @for ($month = 1; $month <= 12; $month++)
                                <option value="{{ $month }}">
                                    {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            {{-- Profil Indikator di Modal Persentase --}}
            <div class="mb-6">
                <x-filament::section collapsible collapsed icon="heroicon-o-information-circle">
                    <x-slot name="header">
                        📋 Profil Indikator
                    </x-slot>
                    <x-slot name="description">
                        Informasi lengkap tentang indikator ini.
                    </x-slot>

                    <div class="space-y-4">
                        @if ($selectedIndicatorForReport->indicator_definition)
                            <div
                                class="grid grid-cols-12 gap-4 text-sm pb-3 border-b border-gray-200 dark:border-gray-700">
                                <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">📖 Definisi
                                </div>
                                <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                    {{ $selectedIndicatorForReport->indicator_definition }}</div>
                            </div>
                        @endif
                        @if ($selectedIndicatorForReport->dimensi_mutu)
                            <div
                                class="grid grid-cols-12 gap-4 text-sm pb-3 border-b border-gray-200 dark:border-gray-700">
                                <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">🎯 Dimensi Mutu
                                </div>
                                <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                    {{ $selectedIndicatorForReport->dimensi_mutu }}</div>
                            </div>
                        @endif
                        @if ($selectedIndicatorForReport->tujuan)
                            <div
                                class="grid grid-cols-12 gap-4 text-sm pb-3 border-b border-gray-200 dark:border-gray-700">
                                <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">🎓 Tujuan</div>
                                <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                    {{ $selectedIndicatorForReport->tujuan }}</div>
                            </div>
                        @endif
                        @if ($selectedIndicatorForReport->indicator_criteria_inclusive)
                            <div
                                class="grid grid-cols-12 gap-4 text-sm pb-3 border-b border-gray-200 dark:border-gray-700">
                                <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">✅ Kriteria
                                    Inklusif</div>
                                <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                    {{ $selectedIndicatorForReport->indicator_criteria_inclusive }}</div>
                            </div>
                        @endif
                        @if ($selectedIndicatorForReport->indicator_criteria_exclusive)
                            <div
                                class="grid grid-cols-12 gap-4 text-sm pb-3 border-b border-gray-200 dark:border-gray-700">
                                <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">❌ Kriteria
                                    Eksklusif</div>
                                <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                    {{ $selectedIndicatorForReport->indicator_criteria_exclusive }}</div>
                            </div>
                        @endif
                        @if ($selectedIndicatorForReport->indicator_source_of_data)
                            <div class="grid grid-cols-12 gap-4 text-sm">
                                <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">📊 Sumber Data
                                </div>
                                <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                    {{ $selectedIndicatorForReport->indicator_source_of_data }}</div>
                            </div>
                        @endif
                    </div>
                </x-filament::section>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Tabel Data Persentase --}}
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600">
                        <div
                            class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">Data Persentase per Hari</h4>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-100 dark:bg-gray-800">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-300 dark:border-gray-600">
                                            Tanggal
                                        </th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-300 dark:border-gray-600">
                                            Numerator
                                        </th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-300 dark:border-gray-600">
                                            Denominator
                                        </th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-300 dark:border-gray-600">
                                            Persentase
                                        </th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-300 dark:border-gray-600">
                                            Input
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reportData as $data)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td
                                                class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-gray-900 dark:text-gray-100 font-medium">
                                                {{ $data['tanggal_formatted'] }}
                                            </td>
                                            <td
                                                class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-center text-gray-900 dark:text-gray-100 font-semibold">
                                                {{ number_format($data['total_numerator'], 0, ',', '.') }}
                                            </td>
                                            <td
                                                class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-center text-gray-900 dark:text-gray-100 font-semibold">
                                                {{ number_format($data['total_denominator'], 0, ',', '.') }}
                                            </td>
                                            <td
                                                class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-center">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $data['persentase'] >= ($selectedIndicatorForReport->indicator_target ?? 0) ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                    {{ number_format($data['persentase'], 2, ',', '.') }}%
                                                </span>
                                            </td>
                                            <td
                                                class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-center text-sm text-gray-600 dark:text-gray-400">
                                                {{ $data['jumlah_input'] }}x
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5"
                                                class="border-b border-gray-200 dark:border-gray-700 px-4 py-12 text-center">
                                                <div class="flex flex-col items-center">
                                                    <div class="w-12 h-12 mx-auto text-gray-400 mb-3 text-3xl">📥</div>
                                                    <p class="text-gray-500 dark:text-gray-400">
                                                        Tidak ada data untuk periode yang dipilih
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

                {{-- Statistik --}}
                <div class="space-y-4">
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600 p-5">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">📈 Statistik Periode</h4>
                        <div class="space-y-3">
                            <div
                                class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Total Input</span>
                                <span
                                    class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $reportStatistics['total_records'] ?? 0 }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Total Numerator</span>
                                <span
                                    class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ number_format($reportStatistics['total_numerator'] ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Total Denominator</span>
                                <span
                                    class="text-lg font-bold text-green-600 dark:text-green-400">{{ number_format($reportStatistics['total_denominator'] ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Rata-rata %</span>
                                <span
                                    class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ number_format($reportStatistics['rata_rata_persentase'] ?? 0, 2, ',', '.') }}%</span>
                            </div>
                            <div
                                class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Min %</span>
                                <span
                                    class="text-sm font-bold text-orange-600 dark:text-orange-400">{{ number_format($reportStatistics['min_persentase'] ?? 0, 2, ',', '.') }}%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Max %</span>
                                <span
                                    class="text-sm font-bold text-pink-600 dark:text-pink-400">{{ number_format($reportStatistics['max_persentase'] ?? 0, 2, ',', '.') }}%</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600 p-5">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">🎯 Target Indikator</h4>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">
                            {{ $selectedIndicatorForReport->indicator_target ?? '0' }}
                            @if ($selectedIndicatorForReport->satuan_pengukuran == 'Persentase')
                                %
                            @else
                                {{ $selectedIndicatorForReport->satuan_pengukuran }}
                            @endif
                        </div>
                        @if (isset($reportStatistics['rata_rata_persentase']))
                            @php
                                $target = (float) ($selectedIndicatorForReport->indicator_target ?? 0);
                                $rataRata = (float) ($reportStatistics['rata_rata_persentase'] ?? 0);
                                $selisih = $rataRata - $target;
                            @endphp
                            <div class="text-sm">
                                @if ($selisih >= 0)
                                    <span class="text-green-600 dark:text-green-400 font-semibold">
                                        ✅ Melebihi target (+{{ number_format($selisih, 2, ',', '.') }}%)
                                    </span>
                                @else
                                    <span class="text-red-600 dark:text-red-400 font-semibold">
                                        ⚠️ Di bawah target ({{ number_format($selisih, 2, ',', '.') }}%)
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </x-filament::modal>

    {{-- Modal Rekap --}}
    <x-filament::modal id="rekap-modal" width="7xl">
        <x-slot name="heading">
            <div class="flex items-center space-x-2">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
                <span>📊 Rekap Data Indikator</span>
            </div>
        </x-slot>
        @if ($selectedIndicatorForRekap)
            <div class="mb-6">
                <div
                    class="bg-gradient-to-r from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 rounded-lg border border-purple-200 dark:border-purple-800 p-5">
                    <h3 class="font-bold text-lg text-purple-900 dark:text-purple-100 mb-2">Rekap Pengisian IMUT</h3>
                    <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 mb-3">
                        {{ $selectedIndicatorForRekap->indicator_name }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div><span class="font-semibold text-gray-700 dark:text-gray-300">Unit:</span> <span
                                class="text-gray-900 dark:text-gray-100">
                                {{ $userUnits->first()?->nama_unit ?? 'N/A' }}
                            </span>
                        </div>
                        <div><span class="font-semibold text-gray-700 dark:text-gray-300">Total Data:</span> <span
                                class="text-gray-900 dark:text-gray-100 font-bold">{{ count($rekapData) }}
                                Record</span></div>
                    </div>
                </div>
            </div>

            {{-- Filter Tahun dan Bulan --}}
            <div
                class="mb-6 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            📅 Tahun
                        </label>
                        <select wire:model.live="rekapYear"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                            @for ($year = now()->year; $year >= now()->year - 5; $year--)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            🗓️ Bulan
                        </label>
                        <select wire:model.live="rekapMonth"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Semua Bulan</option>
                            @for ($month = 1; $month <= 12; $month++)
                                <option value="{{ $month }}">
                                    {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            {{-- Profil Indikator di Modal Rekap --}}
            <div class="mb-6">
                <x-filament::section collapsible collapsed icon="heroicon-o-information-circle">
                    <x-slot name="header">
                        📋 Profil Indikator
                    </x-slot>
                    <x-slot name="description">
                        Informasi lengkap tentang indikator ini.
                    </x-slot>

                    <div class="space-y-4">
                        @if ($selectedIndicatorForRekap->indicator_definition)
                            <div
                                class="grid grid-cols-12 gap-4 text-sm pb-3 border-b border-gray-200 dark:border-gray-700">
                                <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">📖 Definisi
                                </div>
                                <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                    {{ $selectedIndicatorForRekap->indicator_definition }}</div>
                            </div>
                        @endif
                        @if ($selectedIndicatorForRekap->dimensi_mutu)
                            <div
                                class="grid grid-cols-12 gap-4 text-sm pb-3 border-b border-gray-200 dark:border-gray-700">
                                <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">🎯 Dimensi Mutu
                                </div>
                                <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                    {{ $selectedIndicatorForRekap->dimensi_mutu }}</div>
                            </div>
                        @endif
                        @if ($selectedIndicatorForRekap->tujuan)
                            <div
                                class="grid grid-cols-12 gap-4 text-sm pb-3 border-b border-gray-200 dark:border-gray-700">
                                <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">🎓 Tujuan</div>
                                <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                    {{ $selectedIndicatorForRekap->tujuan }}</div>
                            </div>
                        @endif
                        @if ($selectedIndicatorForRekap->indicator_criteria_inclusive)
                            <div
                                class="grid grid-cols-12 gap-4 text-sm pb-3 border-b border-gray-200 dark:border-gray-700">
                                <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">✅ Kriteria
                                    Inklusif</div>
                                <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                    {{ $selectedIndicatorForRekap->indicator_criteria_inclusive }}</div>
                            </div>
                        @endif
                        @if ($selectedIndicatorForRekap->indicator_criteria_exclusive)
                            <div
                                class="grid grid-cols-12 gap-4 text-sm pb-3 border-b border-gray-200 dark:border-gray-700">
                                <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">❌ Kriteria
                                    Eksklusif</div>
                                <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                    {{ $selectedIndicatorForRekap->indicator_criteria_exclusive }}</div>
                            </div>
                        @endif
                        @if ($selectedIndicatorForRekap->indicator_source_of_data)
                            <div class="grid grid-cols-12 gap-4 text-sm">
                                <div class="col-span-3 font-semibold text-gray-700 dark:text-gray-400">📊 Sumber Data
                                </div>
                                <div class="col-span-9 text-gray-900 dark:text-gray-100">
                                    {{ $selectedIndicatorForRekap->indicator_source_of_data }}</div>
                            </div>
                        @endif
                    </div>
                </x-filament::section>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Tabel Data Rekap --}}
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600">
                        <div
                            class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">Daftar Data</h4>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-100 dark:bg-gray-800">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-300 dark:border-gray-600">
                                            Tanggal
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-300 dark:border-gray-600">
                                            Unit/Ruangan
                                        </th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-300 dark:border-gray-600">
                                            Numerator
                                        </th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-300 dark:border-gray-600">
                                            Denominator
                                        </th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-300 dark:border-gray-600">
                                            Persentase
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-300 dark:border-gray-600">
                                            Input Oleh
                                        </th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider border-b border-gray-300 dark:border-gray-600">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rekapData as $data)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td
                                                class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-gray-900 dark:text-gray-100">
                                                <div class="font-medium">{{ $data['tanggal_formatted'] }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    Input: {{ $data['tanggal_input'] }}
                                                </div>
                                            </td>
                                            <td
                                                class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-gray-900 dark:text-gray-100">
                                                <div class="font-medium text-sm">{{ $data['unit'] }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $data['ruangan'] }}
                                                </div>
                                            </td>
                                            <td
                                                class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-center text-gray-900 dark:text-gray-100 font-semibold">
                                                {{ number_format($data['numerator'], 0, ',', '.') }}
                                            </td>
                                            <td
                                                class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-center text-gray-900 dark:text-gray-100 font-semibold">
                                                {{ number_format($data['denominator'], 0, ',', '.') }}
                                            </td>
                                            <td
                                                class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-center">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $data['persentase'] >= ($selectedIndicatorForRekap->indicator_target ?? 0) ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                    {{ number_format($data['persentase'], 2, ',', '.') }}%
                                                </span>
                                            </td>
                                            <td
                                                class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                                {{ $data['editor'] }}
                                            </td>
                                            <td
                                                class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 text-center">
                                                <div class="flex justify-center gap-2">
                                                    @php
                                                        // Cek hak akses: super_admin bisa edit/hapus semua
                                                        // User biasa hanya bisa edit/hapus data dari unit mereka
                                                        $canEditDelete = false;
                                                        $currentUser = auth()->user();

                                                        if ($currentUser) {
                                                            if ($currentUser->hasRole('super_admin')) {
                                                                $canEditDelete = true;
                                                            } else {
                                                                // Cek apakah result_department_id ada di ruangan user
                                                                $userRuanganIds = $currentUser->ruangans
                                                                    ->pluck('id_ruang')
                                                                    ->map(fn($id) => (string) $id)
                                                                    ->toArray();
                                                                // Ambil result_department_id dari data
                                                                $resultDeptId = \App\Models\HospitalSurveyIndicatorResult::find(
                                                                    $data['result_id'],
                                                                )?->result_department_id;
                                                                $canEditDelete = in_array(
                                                                    $resultDeptId,
                                                                    $userRuanganIds,
                                                                );
                                                            }
                                                        }
                                                    @endphp

                                                    @if ($canEditDelete)
                                                        <button type="button"
                                                            class="inline-flex items-center px-2 py-1 rounded text-xs bg-sky-100 text-sky-800 hover:bg-sky-200 dark:bg-sky-900 dark:text-sky-200 dark:hover:bg-sky-800"
                                                            wire:click="openEditModal({{ $data['result_id'] }})">Edit</button>
                                                        <button type="button"
                                                            class="inline-flex items-center px-2 py-1 rounded text-xs bg-red-100 text-red-800 hover:bg-red-200 dark:bg-red-900 dark:text-red-200 dark:hover:bg-red-800"
                                                            wire:click="hapusData({{ $data['result_id'] }})"
                                                            wire:confirm="Apakah Anda yakin ingin menghapus data ini?">Hapus</button>
                                                    @else
                                                        <span
                                                            class="text-xs text-gray-400 dark:text-gray-500 italic">Tidak
                                                            ada akses</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7"
                                                class="border-b border-gray-200 dark:border-gray-700 px-4 py-12 text-center">
                                                <div class="flex flex-col items-center">
                                                    <div class="w-12 h-12 mx-auto text-gray-400 mb-3 text-3xl">📥</div>
                                                    <p class="text-gray-500 dark:text-gray-400">
                                                        Tidak ada data untuk periode yang dipilih
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

                {{-- Statistik --}}
                <div class="space-y-4">
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600 p-5">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">📈 Statistik</h4>
                        <div class="space-y-3">
                            <div
                                class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Total Record</span>
                                <span
                                    class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $rekapStatistics['total_records'] ?? 0 }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Total Numerator</span>
                                <span
                                    class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ number_format($rekapStatistics['total_numerator'] ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Total Denominator</span>
                                <span
                                    class="text-lg font-bold text-green-600 dark:text-green-400">{{ number_format($rekapStatistics['total_denominator'] ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Rata-rata %</span>
                                <span
                                    class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ number_format($rekapStatistics['average_persentase'] ?? 0, 2, ',', '.') }}%</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600 p-5">
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">🎯 Target Indikator</h4>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $selectedIndicatorForRekap->indicator_target ?? '0' }}
                            @if ($selectedIndicatorForRekap->satuan_pengukuran == 'Persentase')
                                %
                            @else
                                {{ $selectedIndicatorForRekap->satuan_pengukuran }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </x-filament::modal>

    {{-- Script (Sudah benar) --}}
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
