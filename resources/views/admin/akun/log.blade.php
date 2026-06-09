<x-app-layout>
    <x-slot name="title">Log Aktivitas</x-slot>
    <div class="space-y-6 animate-fade-in">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.akun.index') }}" class="text-gray-400 dark:text-gray-500 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                <i class="fa-solid fa-users-gear mr-1"></i> Manajemen Akun
            </a>
            <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 dark:text-gray-600"></i>
            <span class="text-gray-700 dark:text-gray-300 font-medium">Log Aktivitas</span>
        </div>

        {{-- User Header --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden shadow-sm">
            <div class="bg-gradient-to-r from-primary-500 to-primary-700 px-6 py-6 relative">
                <div class="flex items-center justify-between relative z-10">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-white/15 backdrop-blur rounded-2xl flex items-center justify-center text-white text-lg font-bold shadow-lg border border-white/10">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">{{ $user->name }}</h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-slate-300/80 text-sm">{{ $user->username }}</span>
                                <span class="w-1 h-1 rounded-full bg-white/30"></span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white/15 text-white text-xs font-semibold uppercase tracking-wide">
                        <i class="{{ $user->role->value === 'admin' ? 'fa-solid fa-shield-halved' : ($user->role->value === 'dosen' ? 'fa-solid fa-chalkboard-user' : ($user->role->value === 'mentor' ? 'fa-solid fa-building' : 'fa-solid fa-graduation-cap')) }} text-[10px]"></i>
                        {{ $user->role->value }}
                    </span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('admin.akun.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white/15 hover:bg-white/25 backdrop-blur border border-white/10 rounded-xl text-sm font-semibold text-white transition-all">
                        <i class="fa-solid fa-arrow-left text-xs"></i> Kembali
                    </a>
                </div>
                <div class="absolute -right-6 -top-6 w-36 h-36 bg-white/5 rounded-full blur-2xl"></div>
            </div>
        </div>

        {{-- Stats Strip --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 text-center shadow-sm">
                <p class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $logs->count() }}</p>
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mt-0.5">Total Aktivitas</p>
            </div>
            @if($logs->count())
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 text-center shadow-sm">
                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $logs->first()->waktu?->format('d M Y') }}</p>
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mt-0.5">Aktivitas Terakhir</p>
            </div>
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 text-center shadow-sm">
                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $logs->last()->waktu?->format('d M Y') }}</p>
                <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mt-0.5">Aktivitas Pertama</p>
            </div>
            @endif
        </div>

        {{-- Log Table --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            @if($logs->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gradient-to-r from-slate-50 to-gray-50 dark:from-surface-800 dark:to-surface-900 border-b-2 border-gray-200 dark:border-gray-600">
                            <th class="px-5 py-3.5 text-left font-bold text-gray-500 dark:text-gray-400 uppercase text-[11px] tracking-wider w-14">#</th>
                            <th class="px-5 py-3.5 text-left font-bold text-gray-500 dark:text-gray-400 uppercase text-[11px] tracking-wider">Aktivitas</th>
                            <th class="px-5 py-3.5 text-left font-bold text-gray-500 dark:text-gray-400 uppercase text-[11px] tracking-wider w-48">Waktu</th>
                            <th class="px-5 py-3.5 text-left font-bold text-gray-500 dark:text-gray-400 uppercase text-[11px] tracking-wider w-32">Relatif</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $i => $log)
                        <tr class="border-b border-gray-100 dark:border-gray-700/50 hover:bg-blue-50/50 dark:hover:bg-primary-900/10 transition-colors {{ $i % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50/70 dark:bg-gray-800/50' }}">
                            <td class="px-5 py-3 text-gray-400 font-medium text-xs">{{ $i + 1 }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-primary-50 dark:bg-primary-900/20 rounded-lg flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-clock-rotate-left text-xs text-primary-600 dark:text-primary-400"></i>
                                    </div>
                                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ $log->kegiatan }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">
                                <i class="fa-regular fa-calendar text-[10px] mr-1"></i> {{ $log->waktu?->format('d M Y H:i:s') }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-700 text-xs font-medium text-gray-500 dark:text-gray-400">
                                    {{ $log->waktu?->diffForHumans() }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-xs text-gray-500 dark:text-gray-400 flex flex-col sm:flex-row justify-between items-center gap-3">
                <span>Menampilkan <strong>{{ $logs->count() }}</strong> dari <strong>{{ $logs->total() }}</strong> log aktivitas untuk <strong>{{ $user->name }}</strong></span>
                <div class="mt-2 sm:mt-0">
                    {{ $logs->links() }}
                </div>
            </div>
            @else
            <div class="text-center py-16 bg-white dark:bg-gray-800">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-inbox text-xl text-gray-400"></i>
                </div>
                <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada log aktivitas</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Aktivitas user akan tercatat di sini</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
