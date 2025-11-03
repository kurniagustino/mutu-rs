<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Info Card --}}
        <x-filament::card>
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    <div
                        class="flex items-center justify-center w-12 h-12 rounded-lg bg-primary-100 dark:bg-primary-900/20">
                        <x-filament::icon icon="heroicon-o-chart-bar"
                            class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-950 dark:text-white">
                        Dashboard Grafik Mutu
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Visualisasi data indikator mutu unit Anda dalam berbagai bentuk grafik
                    </p>
                    <p class="mt-3 text-sm text-gray-800 dark:text-gray-200 font-medium">
                        {{-- Tampilkan unit user yang benar --}}
                        @php
                            $user = auth()->user();
                            $unitName = $user->departemen->nama_ruang ?? 'Tidak ada unit';
                        @endphp
                        Unit Anda: {{ $unitName }} | RS Bhayangkara Jambi
                    </p>
                </div>
            </div>
        </x-filament::card>

        {{-- Widgets Container --}}
        <div class="space-y-6">
            {{-- Widgets akan dirender otomatis oleh Filament melalui getHeaderWidgets() --}}
        </div>
    </div>
</x-filament-panels::page>
