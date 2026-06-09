<x-app-layout>
    <x-slot name="title">Dashboard Admin</x-slot>

    <div class="space-y-8 pb-10">

        {{-- Welcome Header - Clean & Professional --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 lg:p-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                        Halo, {{ collect(explode(' ', auth()->user()->name))->reject(fn($n) => str_ends_with($n, '.') || strlen($n) <= 2)->first() ?? auth()->user()->name }}
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Panel Administrator — Sistem PKL UBSI
                    </p>
                </div>
                
                {{-- Quick Actions --}}
                <div class="flex gap-3">
                    <a href="{{ route('admin.akun.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        <i class="fa-solid fa-users-gear"></i>
                        <span>Kelola Akun</span>
                    </a>
                    <a href="{{ route('admin.import.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 border border-blue-600 rounded-md text-sm font-medium text-white transition-colors">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Import Data</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Stats Grid - PostHog Style --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $stats = [
                    ['label' => 'Total Mahasiswa', 'value' => $jumlahMhs, 'icon' => 'fa-graduation-cap', 'color' => 'blue'],
                    ['label' => 'Total Dosen PA', 'value' => $jumlahDosen, 'icon' => 'fa-chalkboard-user', 'color' => 'emerald'],
                    ['label' => 'Total Mentor', 'value' => $jumlahMentor, 'icon' => 'fa-building', 'color' => 'amber'],
                    ['label' => 'Seluruh Akun', 'value' => $totalAkun, 'icon' => 'fa-users', 'color' => 'purple'],
                ];
            @endphp
            @foreach($stats as $stat)
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $stat['label'] }}</p>
                        <p class="text-3xl font-semibold text-gray-900 dark:text-white mt-2 tabular-nums">{{ $stat['value'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-md bg-{{ $stat['color'] }}-50 dark:bg-{{ $stat['color'] }}-500/10 flex items-center justify-center">
                        <i class="fa-solid {{ $stat['icon'] }} text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400"></i>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Main Dashboard Layout Grid --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- Left Column: Progress & Performance --}}
            <div class="xl:col-span-2 space-y-6">
                
                {{-- Progress Panel - Data Dense --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <div class="flex items-start gap-6">
                        @php $pct = $jumlahMhs > 0 ? round(($sudahInput / $jumlahMhs) * 100) : 0; @endphp
                        
                        {{-- Circular Progress --}}
                        <div class="relative flex items-center justify-center shrink-0">
                            <svg class="w-32 h-32 transform -rotate-90">
                                <circle class="text-gray-100 dark:text-gray-700" stroke-width="8" stroke="currentColor" fill="transparent" r="56" cx="64" cy="64"></circle>
                                <circle class="{{ $pct >= 80 ? 'text-emerald-500' : ($pct >= 40 ? 'text-blue-600' : 'text-red-500') }}" 
                                        stroke-width="8" stroke-dasharray="352" stroke-dashoffset="{{ 352 - (352 * $pct) / 100 }}" stroke-linecap="round" stroke="currentColor" fill="transparent" r="56" cx="64" cy="64"></circle>
                            </svg>
                            <div class="absolute flex flex-col items-center justify-center">
                                <span class="text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $pct }}%</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Selesai</span>
                            </div>
                        </div>

                        {{-- Progress Details --}}
                        <div class="flex-1 space-y-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Progress Input Data PKL</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pantau kesiapan administratif mahasiswa dalam melengkapi form pendaftaran.</p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-md p-4">
                                    <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Sudah Input</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white mt-1 tabular-nums">{{ $sudahInput }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">mahasiswa</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-md p-4">
                                    <p class="text-xs font-medium text-red-600 dark:text-red-400 uppercase tracking-wider">Belum Input</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white mt-1 tabular-nums">{{ $belumInput }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">mahasiswa</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Performance Metrics --}}
                <div>
                    <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Performance Website</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        {{-- Total Traffic --}}
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Traffic</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white mt-1 tabular-nums">{{ number_format($totalActivities + $totalErrors) }}</p>
                                </div>
                                <div class="w-8 h-8 rounded-md bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center">
                                    <i class="fa-solid fa-server text-blue-600 dark:text-blue-400 text-sm"></i>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/30 rounded p-2">
                                    <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400">Berhasil</p>
                                    <p class="text-lg font-semibold text-emerald-700 dark:text-emerald-300 tabular-nums">{{ number_format($totalActivities) }}</p>
                                </div>
                                <div class="bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/30 rounded p-2">
                                    <p class="text-xs font-medium text-red-600 dark:text-red-400">Gagal</p>
                                    <p class="text-lg font-semibold text-red-700 dark:text-red-300 tabular-nums">{{ number_format($totalErrors) }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- System Errors --}}
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">System Errors</p>
                                    <p class="text-2xl font-semibold {{ $totalErrors > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }} mt-1 tabular-nums">{{ number_format($totalErrors) }}</p>
                                    <p class="text-xs {{ $errorsToday > 0 ? 'text-red-500' : 'text-gray-400' }} mt-1">{{ $errorsToday }} hari ini</p>
                                </div>
                                <a href="{{ route('admin.logs.error') }}" class="w-8 h-8 rounded-md {{ $totalErrors > 0 ? 'bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400' : 'bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }} flex items-center justify-center hover:bg-opacity-80 transition-colors">
                                    <i class="fa-solid fa-arrow-right text-sm"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            {{-- Right Column: Timeline --}}
            <div class="space-y-6">
                
                {{-- Calendar Timeline --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-calendar-day text-blue-600 dark:text-blue-400"></i>
                            Kalender Sistem
                        </h3>
                        <a href="{{ route('admin.tanggal.index') }}" class="w-8 h-8 rounded-md bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <i class="fa-solid fa-pen text-xs"></i>
                        </a>
                    </div>

                    @if($deadline)
                    <div class="space-y-6">
                        @php
                            $now = now();
                            $timeline = [
                                ['title' => 'Pendaftaran & Input Data', 'open' => $deadline->open_time, 'close' => $deadline->close_time],
                                ['title' => 'Upload Laporan Akhir', 'open' => $deadline->open_laporan, 'close' => $deadline->close_laporan],
                                ['title' => 'Input Nilai Dosen', 'open' => $deadline->open_nilai, 'close' => $deadline->close_nilai],
                            ];
                        @endphp
                        @foreach($timeline as $t)
                        <div class="border-l-2 border-gray-200 dark:border-gray-700 pl-4">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $t['title'] }}</h4>
                            
                            @if($t['open'] && $t['close'])
                                @php $close = \Carbon\Carbon::parse($t['close']); $open = \Carbon\Carbon::parse($t['open']); @endphp
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $open->format('d M') }} — {{ $close->format('d M Y') }}
                                </p>
                                
                                <div class="mt-2">
                                    @if($now->gt($close))
                                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs font-medium">
                                            <i class="fa-solid fa-lock text-[10px]"></i>
                                            Ditutup
                                        </span>
                                    @elseif($now->lt($open))
                                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 text-xs font-medium border border-amber-200 dark:border-amber-800/30">
                                            <i class="fa-solid fa-clock text-[10px]"></i>
                                            Belum Buka
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 text-xs font-medium border border-emerald-200 dark:border-emerald-800/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Buka ({{ (int)$now->diffInDays($close) }} hari)
                                        </span>
                                    @endif
                                </div>
                            @else
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 italic">Jadwal belum ditentukan</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8 px-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 border-dashed rounded-lg">
                        <i class="fa-solid fa-calendar-xmark text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Konfigurasi tanggal belum dibuat.</p>
                        <a href="{{ route('admin.tanggal.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md mt-4 transition-colors">
                            Buat Jadwal
                        </a>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
