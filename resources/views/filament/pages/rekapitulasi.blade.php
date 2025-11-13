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

    {{-- Table: Daftar Indikator Mutu Unit Anda dengan Modal Detail --}}
    <div x-data="{
        showModal: false,
        detail: {},
        open(indikator) {
            this.detail = indikator;
            this.showModal = true;
        },
        close() { this.showModal = false; }
    }">
        <x-filament::card>
            <div class="space-y-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-950 dark:text-white">
                            Daftar Indikator Mutu Unit Anda
                        </h2>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $this->getSubheading() }}
                        </div>
                    </div>
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
                                    <th
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-950 dark:text-white">
                                        #</th>
                                    <th
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-950 dark:text-white">
                                        Area</th>
                                    <th
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-950 dark:text-white">
                                        Judul Indikator</th>
                                    <th
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-950 dark:text-white">
                                        Status<br><span
                                            class="text-xs font-normal text-gray-600 dark:text-gray-400">{{ now()->format('Y-m-d') }}</span>
                                    </th>
                                    <th
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-950 dark:text-white">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                                @forelse($this->getAllIndicators() as $index => $indicator)
                                    @php
                                        $user = Auth::user();
                                        $userUnitName =
                                            $user->ruangans->first()->unit->nama_unit ??
                                            ($user->ruangans->first()->nama_ruang ?? 'N/A');
                                    @endphp
                                    <tr
                                        class="transition duration-75 hover:bg-primary-50/40 dark:hover:bg-primary-900/10 {{ $index % 2 === 0 ? 'bg-white dark:bg-gray-900' : 'bg-gray-50 dark:bg-gray-800/60' }}">
                                        <td class="px-3 py-4 text-sm text-gray-950 dark:text-white">{{ $index + 1 }}
                                        </td>
                                        <td class="px-3 py-4">
                                            <x-filament::badge color="info">
                                                {{ $userUnitName }}
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
                                            @php $status = $this->checkIndicatorStatus($indicator->indicator_id); @endphp
                                            @if ($status === 'Sudah Dilaksanakan')
                                                <x-filament::badge color="success">
                                                    <svg class="inline h-4 w-4 mr-1 text-green-600" fill="none"
                                                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    {{ $status }}
                                                </x-filament::badge>
                                            @else
                                                <x-filament::badge color="danger">
                                                    <svg class="inline h-4 w-4 mr-1 text-red-600" fill="none"
                                                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    {{ $status }}
                                                </x-filament::badge>
                                            @endif
                                        </td>
                                        <td class="px-3 py-4">
                                            @php
                                                $detail = [
                                                    'nama_unit' => $indicator->units->first()->nama_unit ?? 'N/A',
                                                    'indicator_name' => $indicator->indicator_name,
                                                    'indicator_type' => $indicator->indicator_type ?? 'N/A',
                                                    'status' => $status,
                                                ];
                                            @endphp
                                            <button
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium rounded bg-primary-600 text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-400 gap-1"
                                                x-on:click='open(@json($detail))'>
                                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor"
                                                    stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Lihat
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="mb-4 rounded-full bg-gray-100 dark:bg-gray-500/20 p-3">
                                                    <svg class="h-8 w-8 text-gray-400 dark:text-gray-500"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                                <h4 class="text-base font-semibold text-gray-950 dark:text-white">Tidak
                                                    ada data</h4>
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tidak ada data
                                                    indikator untuk unit Anda</p>
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

        <!-- Modal Detail Indikator -->
        <div x-show="showModal" style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg w-full max-w-md p-6 relative"
                @click.away="close()">
                <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700" @click="close()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <h3 class="text-lg font-bold mb-2 text-primary-700 dark:text-primary-300">Detail Indikator</h3>
                <div class="space-y-2">
                    <div><span class="font-semibold">Unit:</span> <span x-text="detail.nama_unit"></span></div>
                    <div><span class="font-semibold">Nama Indikator:</span> <span x-text="detail.indicator_name"></span>
                    </div>
                    <div><span class="font-semibold">Tipe:</span> <span x-text="detail.indicator_type"></span></div>
                    <div><span class="font-semibold">Status Hari Ini:</span> <span x-text="detail.status"></span></div>
                </div>
                <div class="mt-6 text-right">
                    <button class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700"
                        @click="close()">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
