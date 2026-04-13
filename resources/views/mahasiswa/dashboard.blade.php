<x-app-layout>
    <x-slot name="title">Dashboard Mahasiswa</x-slot>

    <div class="space-y-8 animate-fade-in pb-10">

        {{-- Welcome Banner - Premium Glassmorphism & Mesh Gradient --}}
        <div class="relative overflow-hidden rounded-[2rem] p-8 lg:p-10 border border-white/20 shadow-2xl bg-gradient-to-br from-indigo-900 via-blue-900 to-sky-900 isolate">
            {{-- Background decorative shapes --}}
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-[80px] opacity-60 animate-pulse"></div>
            <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-[80px] opacity-60 animate-pulse" style="animation-delay: 2s;"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 rounded-2xl bg-white/10 border border-white/20 shadow-[0_0_40px_rgba(255,255,255,0.1)] flex items-center justify-center backdrop-blur-xl shrink-0 group hover:scale-105 transition-transform duration-500">
                        <i class="fa-solid fa-user-graduate text-3xl text-white group-hover:text-amber-300 transition-colors"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white to-sky-200 tracking-tight">
                            Halo, {{ collect(explode(' ', auth()->user()->name))->reject(fn($n) => str_ends_with($n, '.') || strlen($n) <= 2)->first() ?? auth()->user()->name }}!
                        </h2>
                        <p class="text-sky-100/80 font-medium text-sm md:text-base mt-1.5 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-400 shadow-[0_0_10px_rgba(251,191,36,0.8)]"></span>
                            Panel Mahasiswa PKL — {{ auth()->user()->username }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Grid --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            
            {{-- Main Content Column --}}
            <div class="xl:col-span-2 space-y-8">
                @if($proposal)
                <div class="bg-white dark:bg-surface-800 rounded-[2rem] p-8 shadow-sm border border-gray-100 dark:border-surface-700 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-50/50 to-transparent dark:from-indigo-900/10 dark:to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center bg-indigo-50 dark:bg-indigo-500/10 text-indigo-500 dark:text-indigo-400 shadow-inner border border-indigo-100 dark:border-indigo-500/20 group-hover:bg-indigo-500 group-hover:text-white transition-colors duration-300">
                                    <i class="fa-solid fa-briefcase text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">Status Pengajuan PKL</h3>
                                    <p class="text-sm font-medium text-gray-500 mt-1">NIM: <span class="font-bold text-gray-800 dark:text-gray-300">{{ $proposal->nim }}</span></p>
                                </div>
                            </div>
                            
                            {{-- Nilai Status --}}
                            @if($proposal->nilai > 0)
                            <div class="flex flex-col items-end">
                                <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mb-1 hidden md:block">Nilai Akhir</p>
                                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 shadow-[0_0_20px_rgba(52,211,153,0.4)] flex items-center justify-center border-4 border-white dark:border-surface-800 hover:scale-110 transition-transform">
                                    <span class="text-2xl font-black text-white">{{ $proposal->nilai }}</span>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 dark:bg-surface-900 rounded-2xl p-5 border border-gray-100 dark:border-surface-700/50 flex flex-col justify-center transform transition-transform hover:-translate-y-1 hover:shadow-md">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Kelengkapan Administrasi</p>
                                @if($proposal->skm)
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                        <p class="font-bold text-gray-800 dark:text-gray-200">Surat Keterangan Magang <br/><span class="text-emerald-500 text-xs font-semibold">(Selesai)</span></p>
                                    </div>
                                @else
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-rose-100 dark:bg-rose-500/10 text-rose-600 flex items-center justify-center shrink-0">
                                            <i class="fa-solid fa-xmark"></i>
                                        </div>
                                        <p class="font-bold text-gray-800 dark:text-gray-200 text-sm">Menunggu Verifikasi Administrasi</p>
                                    </div>
                                @endif
                            </div>

                            <div class="bg-gray-50 dark:bg-surface-900 rounded-2xl p-5 border border-gray-100 dark:border-surface-700/50 flex flex-col justify-center transform transition-transform hover:-translate-y-1 hover:shadow-md group/doc">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Status Upload Dokumen</p>
                                @php
                                    $uploadCount = 0;
                                    if($proposal->lp) $uploadCount++;
                                    if($proposal->lpp) $uploadCount++;
                                    if($proposal->skp) $uploadCount++;
                                @endphp
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full {{ $uploadCount === 3 ? 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600' : 'bg-amber-100 dark:bg-amber-500/10 text-amber-600' }} flex items-center justify-center shrink-0">
                                            <i class="fa-solid fa-file-pdf"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800 dark:text-gray-200">{{ $uploadCount }} dari 3 Dokumen</p>
                                            <p class="text-[10px] font-semibold text-gray-500">Laporan Akhir</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('mahasiswa.laporan.index') }}" class="w-10 h-10 rounded-xl bg-white dark:bg-surface-800 border border-gray-200 dark:border-surface-700 flex items-center justify-center group-hover/doc:text-white group-hover/doc:bg-indigo-500 group-hover/doc:border-indigo-500 transition-colors">
                                        <i class="fa-solid fa-arrow-right text-sm"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-white dark:bg-surface-800 rounded-[2rem] p-10 shadow-sm border border-gray-100 dark:border-surface-700 text-center flex flex-col items-center justify-center min-h-[300px]">
                    <div class="w-24 h-24 bg-gradient-to-br from-indigo-100 to-sky-200 dark:from-indigo-900/30 dark:to-sky-800/20 rounded-full flex items-center justify-center mb-6 shadow-inner relative group">
                        <div class="absolute inset-0 rounded-full border border-white/50 dark:border-white/10 group-hover:scale-110 transition-transform duration-500"></div>
                        <i class="fa-solid fa-file-pen text-4xl text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform duration-500"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-2 tracking-tight">Belum Ada Data Pengajuan</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto leading-relaxed text-sm">
                        Anda belum mendaftarkan lokasi Praktik Kerja Lapangan. Silakan lengkapi formulir pendaftaran terlebih dahulu untuk memulai proses administrasi.
                    </p>
                    <a href="{{ route('mahasiswa.proposal.index') }}" class="group relative px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 border border-indigo-500 text-white font-bold text-sm shadow-[0_0_20px_rgba(79,70,229,0.4)] hover:shadow-[0_0_30px_rgba(79,70,229,0.6)] transition-all duration-300">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-plus transition-transform group-hover:rotate-90"></i> Input Data PKL
                        </span>
                    </a>
                </div>
                @endif
            </div>

            {{-- Right Column (Timeline & Deadline) --}}
            <div class="space-y-8">
                <div class="bg-white dark:bg-surface-800 rounded-[2rem] p-8 shadow-sm border border-gray-100 dark:border-surface-700">
                    <div class="flex items-center gap-3 mb-8">
                        <i class="fa-solid fa-clock text-amber-500 text-xl"></i>
                        <h3 class="font-bold text-gray-900 dark:text-white text-lg">Batas Waktu Sistem</h3>
                    </div>

                    @if($deadline)
                    <div class="relative border-l-2 border-gray-100 dark:border-surface-700 ml-3 space-y-8 pb-2">
                        @php $now = now(); @endphp
                        
                        {{-- Timeline 1: Pendaftaran --}}
                        <div class="relative pl-8">
                            <div class="absolute -left-[11px] top-1 w-5 h-5 rounded-full bg-white dark:bg-surface-800 border-[5px] {{ ($deadline->open_time && $now->lt(\Carbon\Carbon::parse($deadline->open_time))) ? 'border-amber-500' : ($now->gt(\Carbon\Carbon::parse($deadline->close_time)) ? 'border-gray-400' : 'border-indigo-500') }} shadow-sm"></div>
                            <h4 class="font-bold text-gray-800 dark:text-gray-200 text-base">Pendaftaran PKL</h4>
                            @if($deadline->open_time && $deadline->close_time)
                                @php $close = \Carbon\Carbon::parse($deadline->close_time); $open = \Carbon\Carbon::parse($deadline->open_time); @endphp
                                <p class="text-sm text-gray-500 mt-1 mb-3 font-medium">{{ $open->format('d M') }} — {{ $close->format('d M Y') }}</p>
                                @if($now->gt($close))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-surface-700 text-gray-500 text-xs font-bold uppercase tracking-wider"><i class="fa-solid fa-lock text-[10px]"></i> Ditutup</span>
                                @elseif($now->lt($open))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-900/10 text-amber-600 text-xs font-bold uppercase tracking-wider"><i class="fa-solid fa-clock text-[10px]"></i> Belum Buka</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/10 text-emerald-600 dark:text-emerald-400 text-xs font-bold uppercase tracking-wider border border-emerald-100 dark:border-emerald-800/30"><span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Buka (Sisa {{ (int)$now->diffInDays($close) }}hr)</span>
                                @endif
                            @else
                                <p class="text-sm text-gray-400 mt-1 italic">Belum ditentukan</p>
                            @endif
                        </div>

                        {{-- Timeline 2: Laporan --}}
                        <div class="relative pl-8">
                            <div class="absolute -left-[11px] top-1 w-5 h-5 rounded-full bg-white dark:bg-surface-800 border-[5px] {{ ($deadline->open_laporan && $now->lt(\Carbon\Carbon::parse($deadline->open_laporan))) ? 'border-amber-500' : ($now->gt(\Carbon\Carbon::parse($deadline->close_laporan)) ? 'border-gray-400' : 'border-indigo-500') }} shadow-sm"></div>
                            <h4 class="font-bold text-gray-800 dark:text-gray-200 text-base">Upload Laporan</h4>
                            @if($deadline->open_laporan && $deadline->close_laporan)
                                @php $closeLp = \Carbon\Carbon::parse($deadline->close_laporan); $openLp = \Carbon\Carbon::parse($deadline->open_laporan); @endphp
                                <p class="text-sm text-gray-500 mt-1 mb-3 font-medium">{{ $openLp->format('d M') }} — {{ $closeLp->format('d M Y') }}</p>
                                @if($now->gt($closeLp))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-surface-700 text-gray-500 text-xs font-bold uppercase tracking-wider"><i class="fa-solid fa-lock text-[10px]"></i> Ditutup</span>
                                @elseif($now->lt($openLp))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-900/10 text-amber-600 text-xs font-bold uppercase tracking-wider"><i class="fa-solid fa-clock text-[10px]"></i> Belum Buka</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/10 text-emerald-600 text-xs font-bold uppercase tracking-wider border border-emerald-100 dark:border-emerald-800/30"><span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Buka (Sisa {{ (int)$now->diffInDays($closeLp) }}hr)</span>
                                @endif
                            @else
                                <p class="text-sm text-gray-400 mt-1 italic">Belum ditentukan</p>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="text-center py-6 px-4 bg-gray-50 dark:bg-surface-900/50 rounded-2xl border border-gray-100 dark:border-surface-700 border-dashed">
                        <i class="fa-solid fa-calendar-xmark text-4xl text-gray-300 dark:text-surface-600 mb-4 block"></i>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Jadwal belum ditentukan oleh Administrator.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
