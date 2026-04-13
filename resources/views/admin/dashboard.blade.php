<x-app-layout>
    <x-slot name="title">Dashboard Admin</x-slot>

    <div class="space-y-8 animate-fade-in pb-10">

        {{-- Welcome Banner - Premium Glassmorphism & Mesh Gradient --}}
        <div class="relative overflow-hidden rounded-[2rem] p-8 lg:p-10 border border-white/20 shadow-2xl bg-gradient-to-br from-indigo-900 via-blue-900 to-sky-900 isolate">
            {{-- Background decorative shapes --}}
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-[80px] opacity-60 animate-pulse"></div>
            <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-[80px] opacity-60 animate-pulse" style="animation-delay: 2s;"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    {{-- Avatar/Greeting Icon --}}
                    <div class="w-20 h-20 rounded-2xl bg-white/10 border border-white/20 shadow-[0_0_40px_rgba(255,255,255,0.1)] flex items-center justify-center backdrop-blur-xl shrink-0 group hover:scale-105 transition-transform duration-500">
                        <i class="fa-solid fa-user-astronaut text-3xl text-white group-hover:text-sky-300 transition-colors"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white to-sky-200 tracking-tight">
                            Halo, {{ collect(explode(' ', auth()->user()->name))->reject(fn($n) => str_ends_with($n, '.') || strlen($n) <= 2)->first() ?? auth()->user()->name }}!
                        </h2>
                        <p class="text-sky-100/80 font-medium text-sm md:text-base mt-1.5 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 shadow-[0_0_10px_rgba(52,211,153,0.8)]"></span>
                            Panel Administrator — Sistem PKL UBSI
                        </p>
                    </div>
                </div>
                
                {{-- Quick Header Actions --}}
                <div class="flex gap-3 mt-4 md:mt-0">
                    <a href="{{ route('admin.akun.index') }}" class="group relative px-5 py-3 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-sm transition-all duration-300 backdrop-blur-md overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-[150%] xl:group-hover:animate-[shimmer_1.5s_infinite]"></div>
                        <span class="flex items-center gap-2 relative z-10">
                            <i class="fa-solid fa-users-gear text-lg"></i> Kelola Akun
                        </span>
                    </a>
                    <a href="{{ route('admin.import.index') }}" class="group relative px-5 py-3 rounded-xl bg-indigo-500 hover:bg-indigo-400 border border-indigo-400 text-white font-semibold text-sm shadow-[0_0_20px_rgba(99,102,241,0.4)] hover:shadow-[0_0_30px_rgba(99,102,241,0.6)] transition-all duration-300">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-cloud-arrow-up text-lg"></i> Import Data
                        </span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Stats Grid - Floating Sleek Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 stagger-fade">
            @php
                $stats = [
                    [
                        'label' => 'Total Mahasiswa', 'value' => $jumlahMhs, 'icon' => 'fa-graduation-cap', 
                        'glow' => 'bg-sky-500/10 dark:bg-sky-400/10',
                        'icon_bg' => 'bg-sky-50 dark:bg-sky-500/10 group-hover:bg-sky-500',
                        'icon_text' => 'text-sky-500 dark:text-sky-400 group-hover:text-white',
                        'icon_border' => 'border-sky-100 dark:border-sky-500/20',
                        'val_hover' => 'group-hover:text-sky-500 dark:group-hover:text-sky-400',
                    ],
                    [
                        'label' => 'Total Dosen PA', 'value' => $jumlahDosen, 'icon' => 'fa-chalkboard-user', 
                        'glow' => 'bg-emerald-500/10 dark:bg-emerald-400/10',
                        'icon_bg' => 'bg-emerald-50 dark:bg-emerald-500/10 group-hover:bg-emerald-500',
                        'icon_text' => 'text-emerald-500 dark:text-emerald-400 group-hover:text-white',
                        'icon_border' => 'border-emerald-100 dark:border-emerald-500/20',
                        'val_hover' => 'group-hover:text-emerald-500 dark:group-hover:text-emerald-400',
                    ],
                    [
                        'label' => 'Total Mentor', 'value' => $jumlahMentor, 'icon' => 'fa-building', 
                        'glow' => 'bg-amber-500/10 dark:bg-amber-400/10',
                        'icon_bg' => 'bg-amber-50 dark:bg-amber-500/10 group-hover:bg-amber-500',
                        'icon_text' => 'text-amber-500 dark:text-amber-400 group-hover:text-white',
                        'icon_border' => 'border-amber-100 dark:border-amber-500/20',
                        'val_hover' => 'group-hover:text-amber-500 dark:group-hover:text-amber-400',
                    ],
                    [
                        'label' => 'Seluruh Akun', 'value' => $totalAkun, 'icon' => 'fa-users', 
                        'glow' => 'bg-purple-500/10 dark:bg-purple-400/10',
                        'icon_bg' => 'bg-purple-50 dark:bg-purple-500/10 group-hover:bg-purple-500',
                        'icon_text' => 'text-purple-500 dark:text-purple-400 group-hover:text-white',
                        'icon_border' => 'border-purple-100 dark:border-purple-500/20',
                        'val_hover' => 'group-hover:text-purple-500 dark:group-hover:text-purple-400',
                    ],
                ];
            @endphp
            @foreach($stats as $stat)
            <div class="bg-white/80 dark:bg-surface-800/80 backdrop-blur-xl border border-gray-200/50 dark:border-surface-700/50 p-6 sm:p-8 rounded-[1.5rem] shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group relative overflow-hidden">
                {{-- Decorative background glow --}}
                <div class="absolute -right-6 -top-6 w-24 h-24 {{ $stat['glow'] }} rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
                
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center {{ $stat['icon_bg'] }} {{ $stat['icon_text'] }} shadow-inner border {{ $stat['icon_border'] }} transition-colors duration-300">
                        <i class="fa-solid {{ $stat['icon'] }} text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">{{ $stat['label'] }}</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white tracking-tight mt-1 {{ $stat['val_hover'] }} transition-colors">{{ $stat['value'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Main Dashboard Layout Grid --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

            {{-- Center/Left Column: Progress & Core Features --}}
            <div class="xl:col-span-2 space-y-8">
                
                {{-- Progress Panel - Premium Visual --}}
                <div class="bg-white dark:bg-surface-800 rounded-[2rem] p-8 lg:p-10 shadow-sm border border-gray-100 dark:border-surface-700 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-transparent dark:from-blue-900/10 dark:to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
                        @php $pct = $jumlahMhs > 0 ? round(($sudahInput / $jumlahMhs) * 100) : 0; @endphp
                        
                        {{-- Circular Progress Widget --}}
                        <div class="relative flex items-center justify-center shrink-0">
                            <svg class="w-40 h-40 transform -rotate-90">
                                <circle class="text-gray-100 dark:text-surface-700" stroke-width="12" stroke="currentColor" fill="transparent" r="70" cx="80" cy="80"></circle>
                                <circle class="{{ $pct >= 80 ? 'text-emerald-500' : ($pct >= 40 ? 'text-indigo-500' : 'text-rose-500') }} transition-all duration-1000 ease-out" 
                                        stroke-width="12" stroke-dasharray="440" stroke-dashoffset="{{ 440 - (440 * $pct) / 100 }}" stroke-linecap="round" stroke="currentColor" fill="transparent" r="70" cx="80" cy="80"></circle>
                            </svg>
                            <div class="absolute flex flex-col items-center justify-center">
                                <span class="text-3xl font-black text-gray-900 dark:text-white">{{ $pct }}<span class="text-lg text-gray-400">%</span></span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1">Selesai</span>
                            </div>
                        </div>

                        {{-- Progress Info Details --}}
                        <div class="flex-1 w-full space-y-6">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Progress Input Data PKL</h3>
                                <p class="text-base text-gray-500 mt-2 leading-relaxed">Pantau kesiapan administratif mahasiswa dalam melengkapi form pendaftaran dan data institusi.</p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-5">
                                <div class="bg-gray-50 dark:bg-surface-900 rounded-xl p-5 border border-gray-100 dark:border-surface-700/50 flex justify-between items-center group/card hover:-translate-y-0.5 transition-transform">
                                    <div>
                                        <p class="text-xs font-bold text-emerald-500 uppercase tracking-wider mb-1">Sudah Input</p>
                                        <p class="text-3xl font-black text-gray-900 dark:text-white">{{ $sudahInput }} <span class="text-sm font-medium text-gray-400">mhs</span></p>
                                    </div>
                                    <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 flex items-center justify-center shadow-inner group-hover/card:scale-110 transition-transform">
                                        <i class="fa-solid fa-check text-lg"></i>
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-surface-900 rounded-xl p-5 border border-gray-100 dark:border-surface-700/50 flex justify-between items-center group/card hover:-translate-y-0.5 transition-transform">
                                    <div>
                                        <p class="text-xs font-bold text-rose-500 uppercase tracking-wider mb-1">Belum Input</p>
                                        <p class="text-3xl font-black text-gray-900 dark:text-white">{{ $belumInput }} <span class="text-sm font-medium text-gray-400">mhs</span></p>
                                    </div>
                                    <div class="w-12 h-12 rounded-full bg-rose-100 dark:bg-rose-500/10 text-rose-600 flex items-center justify-center shadow-inner group-hover/card:scale-110 transition-transform">
                                        <i class="fa-solid fa-user-clock text-lg"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Performance Website Widget --}}
                <div>
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4 pl-2">Performance Website</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        
                        {{-- Log Aktivitas / Requests --}}
                        <div class="bg-white dark:bg-surface-800 rounded-2xl border border-gray-100 dark:border-surface-700 p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-sky-50 dark:bg-sky-500/10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="relative z-10 flex flex-col h-full justify-between">
                                <div class="mb-5">
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2">
                                        <i class="fa-solid fa-server text-sky-500"></i> Total Traffic (All Time)
                                    </p>
                                    <h4 class="text-3xl font-black text-gray-900 dark:text-white mt-2">{{ number_format($totalActivities + $totalErrors) }}</h4>
                                    <p class="text-[10px] text-gray-400 font-bold mt-1 uppercase tracking-wider">Keseluruhan Ping Ke Database</p>
                                </div>
                                <div class="grid grid-cols-2 gap-2 mt-auto">
                                    <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-lg p-2.5 border border-emerald-100 dark:border-emerald-800/30">
                                        <p class="text-[10px] uppercase font-bold text-emerald-600 dark:text-emerald-500 mb-0.5"><i class="fa-solid fa-check"></i> Berhasil</p>
                                        <p class="text-lg font-black text-emerald-700 dark:text-emerald-400">{{ number_format($totalActivities) }}</p>
                                    </div>
                                    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-2.5 border border-red-100 dark:border-red-800/30">
                                        <p class="text-[10px] uppercase font-bold text-red-600 dark:text-red-500 mb-0.5"><i class="fa-solid fa-xmark"></i> Gagal</p>
                                        <p class="text-lg font-black text-red-700 dark:text-red-400">{{ number_format($totalErrors) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Error Logger / Exceptions --}}
                        <div class="bg-white dark:bg-surface-800 rounded-2xl border border-gray-100 dark:border-surface-700 p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                            <div class="absolute -right-6 -bottom-6 w-24 h-24 {{ $totalErrors > 0 ? 'bg-red-50 dark:bg-red-500/10' : 'bg-emerald-50 dark:bg-emerald-500/10' }} rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="relative z-10 flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2">
                                        <i class="fa-solid fa-bug {{ $totalErrors > 0 ? 'text-red-500' : 'text-emerald-500' }}"></i> System Errors
                                    </p>
                                    <h4 class="text-3xl font-black {{ $totalErrors > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }} mt-2">{{ number_format($totalErrors) }}</h4>
                                    <p class="text-xs {{ $errorsToday > 0 ? 'text-red-500' : 'text-gray-400' }} font-bold mt-1 uppercase"><i class="fa-solid fa-triangle-exclamation"></i> {{ $errorsToday }} Hari Ini</p>
                                </div>
                                <a href="{{ route('admin.logs.error') }}" class="w-10 h-10 rounded-full {{ $totalErrors > 0 ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-surface-700 dark:text-gray-300 dark:hover:bg-surface-600' }} flex items-center justify-center transition-colors shadow-sm" title="Lihat Detail Error">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            {{-- Right Column: Schedules & Timelines --}}
            <div class="space-y-8 h-full">
                
                {{-- Jadwal & Timeline Panel --}}
                <div class="bg-white dark:bg-surface-800 rounded-[2rem] p-8 shadow-sm border border-gray-100 dark:border-surface-700 h-full">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="font-bold text-gray-900 dark:text-white text-lg flex items-center gap-3">
                            <i class="fa-solid fa-calendar-day text-indigo-500 text-xl"></i> Kalender Sistem
                        </h3>
                        <a href="{{ route('admin.tanggal.index') }}" class="w-10 h-10 rounded-full bg-gray-50 dark:bg-surface-700 flex items-center justify-center text-gray-500 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                            <i class="fa-solid fa-pen text-sm"></i>
                        </a>
                    </div>

                    @if($deadline)
                    <div class="relative border-l-2 border-gray-100 dark:border-surface-700 ml-3 space-y-8 pb-2">
                        @php
                            $now = now();
                            $timeline = [
                                ['title' => 'Pendaftaran & Input Data', 'open' => $deadline->open_time, 'close' => $deadline->close_time, 'icon' => 'fa-edit'],
                                ['title' => 'Upload Laporan Akhir', 'open' => $deadline->open_laporan, 'close' => $deadline->close_laporan, 'icon' => 'fa-file-upload'],
                                ['title' => 'Input Nilai Dosen', 'open' => $deadline->open_nilai, 'close' => $deadline->close_nilai, 'icon' => 'fa-star'],
                            ];
                        @endphp
                        @foreach($timeline as $t)
                        <div class="relative pl-8">
                            {{-- Timeline Dot --}}
                            <div class="absolute -left-[11px] top-1 w-5 h-5 rounded-full bg-white dark:bg-surface-800 border-[5px] border-indigo-500 shadow-[0_0_12px_rgba(99,102,241,0.5)]"></div>
                            
                            <h4 class="font-bold text-gray-800 dark:text-gray-200 text-base">{{ $t['title'] }}</h4>
                            
                            @if($t['open'] && $t['close'])
                                @php $close = \Carbon\Carbon::parse($t['close']); $open = \Carbon\Carbon::parse($t['open']); @endphp
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 mb-3 font-medium">
                                    {{ $open->format('d M') }} — {{ $close->format('d M Y') }}
                                </p>
                                
                                @if($now->gt($close))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-surface-700 text-gray-500 dark:text-gray-400 text-xs font-bold uppercase tracking-wider">
                                        <i class="fa-solid fa-lock text-[10px]"></i> Ditutup
                                    </span>
                                @elseif($now->lt($open))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-900/10 text-amber-600 dark:text-amber-500 text-xs font-bold uppercase tracking-wider">
                                        <i class="fa-solid fa-clock text-[10px]"></i> Belum Buka
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/10 text-emerald-600 dark:text-emerald-400 text-xs font-bold uppercase tracking-wider border border-emerald-100 dark:border-emerald-800/30">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Buka (Sisa {{ (int)$now->diffInDays($close) }} hr)
                                    </span>
                                @endif
                            @else
                                <p class="text-sm text-gray-400 mt-1 italic">Jadwal belum ditentukan</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8 px-6 bg-gray-50 dark:bg-surface-900/50 rounded-2xl border border-gray-100 dark:border-surface-700 border-dashed">
                        <i class="fa-solid fa-calendar-xmark text-4xl text-gray-300 dark:text-surface-600 mb-4 block"></i>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Konfigurasi tanggal belum dibuat. Atur sekarang agar jadwal tertata.</p>
                        <a href="{{ route('admin.tanggal.index') }}" class="btn-primary mt-5 inline-flex px-5 py-2.5 text-sm">Buat Jadwal</a>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
