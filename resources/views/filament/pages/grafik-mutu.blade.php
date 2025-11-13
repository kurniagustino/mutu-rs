<x-filament-panels::page>
    <div class="flex flex-col gap-4">
        {{-- HEADER DAN FILTER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 md:gap-6">
            <div>
                <h1 class="text-2xl font-bold leading-tight">Grafik Mutu</h1>
                <div class="text-gray-600 text-sm mt-1">
                    Unit Anda: <span class="font-semibold">{{ $unitName }}</span> | RS Bhayangkara Jambi
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 md:gap-3">
                <label for="indikator" class="text-sm font-medium">Indikator:</label>
                <select wire:model.live="indikator" id="indikator" class="border rounded px-2 py-1 text-sm">
                    @foreach ($indicatorOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                <label for="tahun" class="text-sm font-medium ml-2">Tahun:</label>
                <select wire:model.live="tahun" id="tahun" class="border rounded px-2 py-1 text-sm">
                    @php
                        $tahunSekarang = date('Y');
                        $tahunList = range($tahunSekarang - 4, $tahunSekarang);
                    @endphp
                    @foreach ($tahunList as $th)
                        <option value="{{ $th }}">{{ $th }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- KARTU STATISTIK --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-filament::card class="text-center">
                <div class="text-xs text-gray-500">Rata-rata Capaian</div>
                <div class="text-2xl font-bold text-primary-600">{{ $rataRataCapaian }}%</div>
            </x-filament::card>
            <x-filament::card class="text-center">
                <div class="text-xs text-gray-500">Bulan Tercapai Target (≥{{ $target }}%)</div>
                <div class="text-2xl font-bold text-green-600">{{ $bulanTercapai }}</div>
            </x-filament::card>
            <x-filament::card class="text-center">
                <div class="text-xs text-gray-500">Data Masuk</div>
                <div class="text-2xl font-bold text-blue-600">{{ $dataMasuk }}/12</div>
            </x-filament::card>
            <x-filament::card class="text-center">
                <div class="text-xs text-gray-500">Capaian Tertinggi</div>
                <div class="text-2xl font-bold text-yellow-600">{{ $capaianTertinggi }}%</div>
            </x-filament::card>
        </div>

        {{-- GRAFIK --}}
        <x-filament::card class="mt-2">
            <div
                x-data="{}"
                x-init="() => {
                    // Destroy any existing chart instance on the canvas.
                    let existingChart = Chart.getChart($refs.canvas);
                    if (existingChart) {
                        existingChart.destroy();
                    }

                    // Create a new chart and store the instance on the DOM element ($el)
                    // to avoid Alpine's reactivity causing a 'too much recursion' error.
                    $el.chart = new Chart($refs.canvas, {
                        type: 'line',
                        data: {
                            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
                            datasets: [
                                {
                                    label: 'Capaian (%)',
                                    data: @json($chartData),
                                    borderColor: 'rgba(59, 130, 246, 0.8)',
                                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                                    fill: true,
                                    tension: 0.4,
                                },
                                {
                                    label: 'Target (%)',
                                    data: Array(12).fill(@json($target)),
                                    borderColor: 'rgba(239, 68, 68, 0.8)',
                                    borderDash: [5, 5],
                                    fill: false,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100
                                }
                            }
                        }
                    });
                }"
                @update-chart-data.window="
                    if ($el.chart) {
                        $el.chart.data.datasets[0].data = $event.detail.chartData;
                        $el.chart.data.datasets[1].data = Array(12).fill($event.detail.target);
                        $el.chart.update();
                    }
                "
                wire:ignore
            >
                <div class="h-72">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>
        </x-filament::card>

        {{-- TABEL DATA --}}
        <x-filament::card class="mt-2">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-2 py-1">Bulan</th>
                            <th class="px-2 py-1">Numerator</th>
                            <th class="px-2 py-1">Denominator</th>
                            <th class="px-2 py-1">Capaian (%)</th>
                            <th class="px-2 py-1">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tableData as $data)
                            <tr>
                                <td class="px-2 py-1">{{ $data['bulan'] }}</td>
                                <td class="px-2 py-1">{{ $data['numerator'] }}</td>
                                <td class="px-2 py-1">{{ $data['denominator'] }}</td>
                                <td class="px-2 py-1">{{ $data['capaian'] }}</td>
                                <td class="px-2 py-1">
                                    @if ($data['status'] === 'Tercapai')
                                        <span
                                            class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded">
                                            {{ $data['status'] }}
                                        </span>
                                    @elseif ($data['status'] === 'Tidak Tercapai')
                                        <span
                                            class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded">
                                            {{ $data['status'] }}
                                        </span>
                                    @else
                                        {{ $data['status'] }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    Tidak ada data untuk ditampilkan pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::card>
    </div>

    {{-- ======================== PEMBATAS ======================== --}}
    <div class="my-8 border-t border-dashed border-gray-400 text-center">
        <span class="inline-block bg-white px-4 text-gray-500 text-xs tracking-widest">LAPORAN REKAP PENDATAAN</span>
    </div>


    {{-- ======= Laporan Rekap (tanpa form filter, agar tidak error) ======= --}}
    <div class="space-y-6">
        {{-- Filter laporan dinonaktifkan karena tidak ada $form di GrafikMutu --}}
        {{-- Tampilkan hanya tabel rekap jika variabel tersedia --}}

        {{-- Results Table --}}
        @if (isset($showResults) && $showResults && isset($indicators))
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
                            <h2 class="text-lg font-semibold text-gray-950 dark:text-white">📊 Daftar Pendataan IMUT
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
                                            No</th>
                                        <th
                                            class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
                                            Indikator Mutu</th>
                                        <th
                                            class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
                                            Total Pendataan</th>
                                        <th
                                            class="px-4 py-3.5 text-center text-xs font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                                    @forelse($indicators as $index => $indicator)
                                        <tr
                                            class="transition duration-75 hover:bg-primary-50/30 dark:hover:bg-primary-900/10 {{ $index % 2 === 0 ? 'bg-white dark:bg-gray-900' : 'bg-gray-50/50 dark:bg-gray-800/30' }}">
                                            <td class="px-4 py-4 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ $index + 1 }}</td>
                                            <td class="px-4 py-4">
                                                <div class="space-y-2">
                                                    <div class="flex items-center gap-2">
                                                        <x-filament::badge color="info"
                                                            size="sm">{{ $indicator['area'] }}</x-filament::badge>
                                                        <x-filament::badge color="gray"
                                                            size="xs">{{ $indicator['type'] }}</x-filament::badge>
                                                    </div>
                                                    <div class="text-sm font-medium text-gray-950 dark:text-white">
                                                        {{ $indicator['title'] }}</div>
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
                                                        Tidak ada data</h4>
                                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tidak ada
                                                        data pendataan untuk periode yang dipilih</p>
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

        {{-- Modal detail indikator, jika ingin ditampilkan, pastikan variabelnya tersedia --}}
        {{-- ...modal detail indikator bisa diletakkan di sini jika diperlukan... --}}
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush

</x-filament-panels::page>
