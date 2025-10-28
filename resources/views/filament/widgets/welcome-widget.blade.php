<x-filament-widgets::widget>
    {{-- ✅ Background adapt ke theme --}}
    <div
        class="bg-gradient-to-r from-blue-500 to-purple-600 dark:from-blue-700 dark:to-purple-800 rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                {{-- ✅ Text selalu putih --}}
                <h2 class="text-2xl font-bold text-white mb-2">
                    Selamat Datang, {{ $this->getViewData()['name'] }}
                </h2>

                <div class="space-y-1">
                    <p class="text-white/90 text-sm">
                        <span class="font-medium">Unit yang anda kelola:</span>
                        <span class="font-semibold">{{ $this->getViewData()['unit'] }}</span>
                    </p>
                    <p class="text-white/90 text-sm">
                        <span class="font-medium">Role:</span>
                        <span
                            class="font-semibold">{{ ucwords(str_replace('_', ' ', $this->getViewData()['role'])) }}</span>
                    </p>
                </div>

                <a href="#"
                    class="inline-flex items-center text-sm text-white/80 hover:text-white underline mt-3 transition-colors">
                    Klik Tombol untuk lihat detil
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            {{-- ✅ Icon with better styling --}}
            <div class="hidden md:flex items-center justify-center">
                <div class="bg-white/10 rounded-full p-4">
                    <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
