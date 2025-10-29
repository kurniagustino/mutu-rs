<x-filament-panels::page>
    {{-- Back Button --}}
    <div class="mb-4">
        <x-filament::button color="gray" icon="heroicon-o-arrow-left" tag="a"
            href="{{ route('filament.admin.pages.validasi') }}">
            Kembali ke Validasi
        </x-filament::button>
    </div>

    @if (!empty($detailData))
        {{-- Header Info --}}
        <x-filament::card class="mb-6">
            <div
                class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-950/30 dark:to-purple-950/30 rounded-lg p-6">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-3">
                            <x-filament::badge color="info" size="lg">
                                ID: {{ $detailData['indicator_id'] }}
                            </x-filament::badge>
                            <x-filament::badge color="purple" size="lg">
                                {{ $detailData['category_name'] }}
                            </x-filament::badge>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-3">
                            🔍 {!! $detailData['indicator_name'] !!}
                        </h2>
                        @if ($detailData['monitoring_area'])
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-medium">Area Monitor:</span> {{ $detailData['monitoring_area'] }}
                            </p>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Target</div>
                        <div class="text-4xl font-bold text-green-600 dark:text-green-400">
                            {{ $detailData['target'] }}%
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::card>

        {{-- Data Table --}}
        <x-filament::card>
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Data Per Unit/Ruangan
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Periode {{ $year }}-{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}
                </p>
            </div>

            @if (!empty($detailData['results_by_unit']))
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                    #</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                    Unit/Ruangan</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                    Area Monitor</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                    Numerator</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                    Denominator</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                    Persentase</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($detailData['results_by_unit'] as $index => $unit)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $index + 1 }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $unit['unit_name'] }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $unit['area_monitor'] }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200">
                                            {{ $unit['numerator'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-purple-100 dark:bg-purple-900/50 text-purple-800 dark:text-purple-200">
                                            {{ $unit['denominator'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @php
                                            $p = $unit['persentase'];
                                            $color = $p >= 80 ? 'success' : ($p >= 60 ? 'warning' : 'danger');
                                        @endphp
                                        <x-filament::badge :color="$color" size="lg">
                                            {{ $p }}%
                                        </x-filament::badge>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <x-heroicon-o-inbox class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                    <p class="text-gray-500 dark:text-gray-400">Tidak ada data untuk periode ini</p>
                </div>
            @endif
        </x-filament::card>
    @else
        <x-filament::card>
            <div class="text-center py-12">
                <p class="text-gray-500">Data tidak ditemukan</p>
            </div>
        </x-filament::card>
    @endif
</x-filament-panels::page>
