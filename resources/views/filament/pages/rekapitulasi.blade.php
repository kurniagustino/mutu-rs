<x-filament-panels::page>
    {{-- Cards: Indikator Count per Date --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach ($this->getIndicatorsByDate() as $data)
            <x-filament::card>
                <div class="text-center">
                    <div class="text-5xl font-bold text-primary-600 dark:text-primary-400 mb-2">
                        {{ $data->count }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Indikator
                    </div>
                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                        Pendataan Tanggal<br>
                        {{ \Carbon\Carbon::parse($data->date)->format('Y-m-d') }}
                    </div>
                </div>
            </x-filament::card>
        @endforeach
    </div>

    {{-- Table: Daftar Indikator Mutu Unit Anda --}}
    <x-filament::card>
        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-gray-950 dark:text-white">
                Daftar Indikator Mutu Unit Anda
            </h2>

            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Hari ini: <span
                        class="font-semibold text-gray-950 dark:text-white">{{ now()->format('Y-m-d') }}</span>
                </div>
            </div>

            <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-white/10">
                <div class="overflow-x-auto">
                    <table class="w-full table-auto divide-y divide-gray-200 dark:divide-white/10">
                        <thead class="bg-gray-50 dark:bg-white/5">
                            <tr>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-950 dark:text-white">
                                    #
                                </th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-950 dark:text-white">
                                    Area
                                </th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-950 dark:text-white">
                                    Judul Indikator
                                </th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-950 dark:text-white">
                                    Hari ini<br><span
                                        class="text-xs font-normal text-gray-600 dark:text-gray-400">{{ now()->format('Y-m-d') }}</span>
                                </th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-950 dark:text-white">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/10">

                            @forelse($this->getAllIndicators() as $index => $indicator)
                                <tr
                                    class="transition duration-75 hover:bg-gray-50 dark:hover:bg-white/5 bg-white dark:bg-gray-900">
                                    <td class="px-3 py-4 text-sm text-gray-950 dark:text-white">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-3 py-4">
                                        <x-filament::badge color="info">
                                            {{ $indicator->units->first()->nama_unit ?? 'N/A' }}
                                        </x-filament::badge>
                                    </td>
                                    <td class="px-3 py-4">
                                        <div class="space-y-1">
                                            <div class="text-sm font-medium text-gray-950 dark:text-white">
                                                {{ $indicator->indicator_name }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                Tipe: {{ $indicator->indicator_type ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-4">
                                        @php
                                            $status = $this->checkIndicatorStatus($indicator->indicator_id);
                                        @endphp
                                        @if ($status === 'Sudah Dilaksanakan')
                                            <x-filament::badge color="success">
                                                ✓ {{ $status }}
                                            </x-filament::badge>
                                        @else
                                            <x-filament::badge color="danger">
                                                ✗ {{ $status }}
                                            </x-filament::badge>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4">
                                        <x-filament::button tag="a" :href="route('filament.admin.pages.input-mutu-indikator')" color="primary"
                                            size="sm" icon="heroicon-m-eye">
                                            Lihat
                                        </x-filament::button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="mb-4 rounded-full bg-gray-100 dark:bg-gray-500/20 p-3">
                                                <svg class="h-6 w-6 text-gray-500 dark:text-gray-400"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </div>
                                            <h4 class="text-base font-semibold text-gray-950 dark:text-white">
                                                Tidak ada data
                                            </h4>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                Tidak ada data indikator untuk unit Anda
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
</x-filament-panels::page>
