<x-app-layout>
    <x-slot name="title">Dashboard Mentor</x-slot>
    
    <div class="space-y-8 animate-fade-in pb-10">
        
        {{-- High-End Welcome Banner with Mesh Gradient --}}
        <div class="relative overflow-hidden rounded-[2.5rem] bg-slate-900 border border-slate-800 shadow-2xl group">
            {{-- Abstract Mesh Backgrounds --}}
            <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-indigo-500/20 blur-[100px] transition-transform duration-1000 group-hover:scale-110"></div>
            <div class="absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-cyan-500/10 blur-[100px] transition-transform duration-1000 group-hover:scale-110"></div>
            
            <div class="relative z-10 p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex flex-col md:flex-row items-center gap-6 text-center md:text-left">
                    <div class="relative">
                        <div class="w-20 h-20 md:w-24 md:h-24 rounded-3xl bg-indigo-600 flex items-center justify-center text-white shadow-xl shadow-indigo-600/20 ring-4 ring-slate-800 group-hover:rotate-6 transition-transform">
                            <i class="fa-solid fa-building-shield text-3xl md:text-4xl"></i>
                        </div>
                        <div class="absolute -bottom-2 -right-2 w-8 h-8 rounded-full bg-emerald-500 border-4 border-slate-900 flex items-center justify-center text-[10px] text-white animate-pulse">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">Selamat Datang, {{ collect(explode(' ', $user->name))->reject(fn($n) => str_ends_with($n, '.') || strlen($n) <= 2)->first() ?? $user->name }}</h2>
                        <div class="flex flex-wrap justify-center md:justify-start items-center gap-3 mt-3">
                            <span class="px-3 py-1 bg-indigo-500/10 border border-indigo-500/20 rounded-full text-indigo-400 text-xs font-bold uppercase tracking-widest">Mentor Industry</span>
                            <span class="px-3 py-1 bg-slate-800 border border-slate-700 rounded-full text-slate-400 text-xs font-medium">{{ now()->format('l, d F Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('mentor.mahasiswa.pkl') }}" class="px-6 py-3 bg-white hover:bg-indigo-50 text-indigo-900 font-bold rounded-2xl transition-all shadow-lg active:scale-95 flex items-center gap-2">
                        <i class="fa-solid fa-list-check"></i> Kelola PKL
                    </a>
                </div>
            </div>
        </div>

        {{-- Deadline Alert --}}
        @if($deadline && $deadline->open_nilai)
            @php
                $now = now();
                $closeNilai = \Carbon\Carbon::parse($deadline->close_nilai);
                $openNilai = \Carbon\Carbon::parse($deadline->open_nilai);
            @endphp
            @if($now->gt($closeNilai))
                <div class="flex items-center gap-4 p-5 rounded-3xl bg-rose-50 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-800/30 text-rose-600 dark:text-rose-400 animate-fade-in shadow-sm">
                    <div class="w-12 h-12 rounded-2xl bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-calendar-xmark text-xl"></i>
                    </div>
                    <div>
                        <p class="font-black text-sm uppercase tracking-wider">Masa Penilaian Berakhir</p>
                        <p class="text-sm opacity-80 font-medium">Sistem penutupan nilai telah aktif sejak {{ $closeNilai->format('d/m/Y') }}. Hubungi Admin jika ada darurat.</p>
                    </div>
                </div>
            @elseif($now->gte($openNilai))
                <div class="flex items-center gap-4 p-5 rounded-3xl bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-800/30 text-amber-600 dark:text-amber-400 animate-fade-in shadow-sm">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-clock-rotate-left animate-spin-slow"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-black text-sm uppercase tracking-wider">Batas Waktu Penilaian</p>
                        <p class="text-sm opacity-80 font-medium">Panel penilaian aktif hingga {{ $closeNilai->format('d M Y') }} — <strong>Sisa {{ intval($now->diffInDays($closeNilai)) }} Hari lagi</strong></p>
                    </div>
                    <div class="hidden sm:block">
                        <div class="h-2 w-32 bg-amber-200 dark:bg-amber-900/50 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500" style="width: {{ 100 - ($now->diffInDays($closeNilai) / 30 * 100) }}%"></div>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        {{-- Bento-style Statistic Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="relative group overflow-hidden p-8 rounded-[2.5rem] bg-white dark:bg-surface-800 border border-gray-100 dark:border-surface-700 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 flex items-center justify-center mb-6 shadow-sm"><i class="fa-solid fa-users-rays text-2xl"></i></div>
                <p class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">{{ $jumlahMahasiswa }}</p>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Total Anak Bimbing</p>
            </div>

            <div class="relative group overflow-hidden p-8 rounded-[2.5rem] bg-white dark:bg-surface-800 border border-gray-100 dark:border-surface-700 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 flex items-center justify-center mb-6 shadow-sm"><i class="fa-solid fa-circle-check text-2xl"></i></div>
                <p class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter text-emerald-600 dark:text-emerald-400">{{ $sudahDinilai }}</p>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Berhasil Dinilai</p>
            </div>

            <div class="relative group overflow-hidden p-8 rounded-[2.5rem] bg-white dark:bg-surface-800 border border-gray-100 dark:border-surface-700 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-900/20 text-amber-600 flex items-center justify-center mb-6 shadow-sm"><i class="fa-solid fa-file-signature text-2xl"></i></div>
                <p class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter text-amber-600">{{ $belumDinilai }}</p>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Menunggu Penilaian</p>
            </div>
        </div>

        {{-- Quick Hub / Feature Cards --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="p-8 rounded-[2.5rem] bg-indigo-600 text-white shadow-xl shadow-indigo-600/20 flex items-center justify-between group overflow-hidden relative">
                <div class="absolute right-0 bottom-0 opacity-10 translate-x-10 translate-y-10 group-hover:translate-x-0 group-hover:translate-y-0 transition-transform duration-700">
                    <i class="fa-solid fa-briefcase text-[10rem]"></i>
                </div>
                <div class="relative z-10 mr-4">
                    <h3 class="text-2xl font-black tracking-tight">Manajemen PKL</h3>
                    <p class="text-indigo-100/70 text-sm mt-1 max-w-xs">Kelola mahasiswa jalur Magang institusi. Monitoring progres laporan harian & mingguan.</p>
                    <a href="{{ route('mentor.mahasiswa.pkl') }}" class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white text-indigo-900 font-bold text-xs shadow-lg shadow-black/10 hover:-translate-y-1 transition-all active:scale-95">Mulai Sekarang <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="p-8 rounded-[2.5rem] bg-cyan-600 text-white shadow-xl shadow-cyan-600/20 flex items-center justify-between group overflow-hidden relative">
                <div class="absolute right-0 bottom-0 opacity-10 translate-x-10 translate-y-10 group-hover:translate-x-0 group-hover:translate-y-0 transition-transform duration-700">
                    <i class="fa-solid fa-rocket text-[10rem]"></i>
                </div>
                <div class="relative z-10 mr-4">
                    <h3 class="text-2xl font-black tracking-tight">Manajemen MSIB</h3>
                    <p class="text-cyan-100/70 text-sm mt-1 max-w-xs">Monitoring peserta Kampus Merdeka MSIB. Verifikasi sertifikat dan penilaian akhir program.</p>
                    <a href="{{ route('mentor.mahasiswa.msib') }}" class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white text-cyan-900 font-bold text-xs shadow-lg shadow-black/10 hover:-translate-y-1 transition-all active:scale-95">Mulai Sekarang <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
