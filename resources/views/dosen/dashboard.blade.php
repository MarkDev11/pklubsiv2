<x-app-layout>
    <x-slot name="title">Dashboard Dosen PA</x-slot>

    <div class="space-y-6">

        {{-- Welcome Header --}}
        <div>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Halo, {{ $user->name }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    <span class="inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-chalkboard-user text-xs"></i> Dosen Pembimbing Akademik
                    </span>
                    <span class="mx-1">·</span>
                    <span>{{ now()->format('l, d F Y') }}</span>
                </p>
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
                <div class="flex items-center gap-3 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/30">
                    <div class="w-10 h-10 rounded-md bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-red-800 dark:text-red-300">Portal Penilaian Ditutup</p>
                        <p class="text-xs text-red-700 dark:text-red-400 mt-0.5">Sesi pengisian formulir nilai PKL/MSIB telah ditutup oleh Administrator.</p>
                    </div>
                </div>
            @elseif($now->gte($openNilai))
                <div class="flex items-center gap-3 p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/30">
                    <div class="w-10 h-10 rounded-md bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-blue-800 dark:text-blue-300">Tugas Penilaian Dibuka</p>
                            <span class="text-xs px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-medium tabular-nums">Sisa {{ intval($now->diffInDays($closeNilai)) }} hari</span>
                        </div>
                        <p class="text-xs text-blue-700 dark:text-blue-400 mt-0.5">Mohon validasi dokumen Laporan PKL/MSIB dan selesaikan penilaian sebelum {{ $closeNilai->format('d M Y') }}.</p>
                    </div>
                </div>
            @endif
        @endif

        {{-- Stat Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Bimbingan</h3>
                    <div class="w-9 h-9 rounded-md bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <p class="text-3xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $jumlahMahasiswa }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sudah Dinilai</h3>
                    <div class="w-9 h-9 rounded-md bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <i class="fa-solid fa-check-double"></i>
                    </div>
                </div>
                <p class="text-3xl font-semibold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ $sudahDinilai }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Belum Dinilai</h3>
                    <div class="w-9 h-9 rounded-md bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                        <i class="fa-solid fa-clipboard-question"></i>
                    </div>
                </div>
                <p class="text-3xl font-semibold text-amber-600 dark:text-amber-400 tabular-nums">{{ $belumDinilai }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Belum Input</h3>
                    <div class="w-9 h-9 rounded-md bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                        <i class="fa-solid fa-user-clock"></i>
                    </div>
                </div>
                <p class="text-3xl font-semibold text-rose-600 dark:text-rose-400 tabular-nums">{{ $belumInputForm }}</p>
            </div>
        </div>

        {{-- Timeline / Jadwal Akademik --}}
        @if($deadline)
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
                <i class="fa-solid fa-calendar-days text-blue-500"></i>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Jadwal Akademik Periode PKL</h2>
            </div>

            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @php $now = now(); @endphp

                {{-- Periode Input Nilai (Paling penting untuk dosen) --}}
                @if($deadline->open_nilai && $deadline->close_nilai)
                    @php 
                        $closeNilai = \Carbon\Carbon::parse($deadline->close_nilai);
                        $openNilai = \Carbon\Carbon::parse($deadline->open_nilai);
                    @endphp

                    <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 {{ $now->between($openNilai, $closeNilai) ? 'bg-blue-50/50 dark:bg-blue-900/5' : '' }}">
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">Input Nilai Mahasiswa</p>
                                @if($now->between($openNilai, $closeNilai))
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                                        <i class="fa-solid fa-star text-[10px]"></i> Prioritas
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $openNilai->format('d M Y') }} — {{ $closeNilai->format('d M Y') }}</p>
                        </div>
                        <div>
                            @if($now->gt($closeNilai))
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                                    <i class="fa-solid fa-lock mr-1 text-[10px]"></i> Ditutup
                                </span>
                            @elseif($now->lt($openNilai))
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800/30">
                                    <i class="fa-solid fa-clock mr-1 text-[10px]"></i> Belum Buka
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30">
                                    <i class="fa-solid fa-unlock mr-1 text-[10px]"></i> Buka · {{ (int)$now->diffInDays($closeNilai) }} hari lagi
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Periode Input Data PKL (Mahasiswa) --}}
                @if($deadline->open_time && $deadline->close_time)
                    @php 
                        $closeInput = \Carbon\Carbon::parse($deadline->close_time);
                        $openInput = \Carbon\Carbon::parse($deadline->open_time);
                    @endphp

                    <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Input Data PKL (Mahasiswa)</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $openInput->format('d M Y') }} — {{ $closeInput->format('d M Y') }}</p>
                        </div>
                        <div>
                            @if($now->gt($closeInput))
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">Ditutup</span>
                            @elseif($now->lt($openInput))
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800/30">Belum Buka</span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30">Buka · {{ (int)$now->diffInDays($closeInput) }} hari lagi</span>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Periode Upload Laporan (Mahasiswa) --}}
                @if($deadline->open_laporan && $deadline->close_laporan)
                    @php 
                        $closeLaporan = \Carbon\Carbon::parse($deadline->close_laporan);
                        $openLaporan = \Carbon\Carbon::parse($deadline->open_laporan);
                    @endphp

                    <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Upload Laporan (Mahasiswa)</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $openLaporan->format('d M Y') }} — {{ $closeLaporan->format('d M Y') }}</p>
                        </div>
                        <div>
                            @if($now->gt($closeLaporan))
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">Ditutup</span>
                            @elseif($now->lt($openLaporan))
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800/30">Belum Buka</span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30">Buka · {{ (int)$now->diffInDays($closeLaporan) }} hari lagi</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Quick Actions --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {{-- Data Mahasiswa --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-md bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <i class="fa-solid fa-users-viewfinder"></i>
                    </div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Data Mahasiswa</h2>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('dosen.mahasiswa.pkl') }}" class="flex items-center justify-between p-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/10 hover:border-blue-300 dark:hover:border-blue-700 group transition-colors">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400">Mahasiswa PKL Magang</span>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-400 group-hover:text-blue-500"></i>
                    </a>
                    <a href="{{ route('dosen.mahasiswa.msib') }}" class="flex items-center justify-between p-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/10 hover:border-blue-300 dark:hover:border-blue-700 group transition-colors">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400">Mahasiswa MSIB / PMK</span>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-400 group-hover:text-blue-500"></i>
                    </a>
                </div>
            </div>

            {{-- Penilaian --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-md bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Penilaian Akhir</h2>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('dosen.nilai.pkl') }}" class="flex items-center justify-between p-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-emerald-50 dark:hover:bg-emerald-900/10 hover:border-emerald-300 dark:hover:border-emerald-700 group transition-colors">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">Verifikasi Nilai PKL</span>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-400 group-hover:text-emerald-500"></i>
                    </a>
                    <a href="{{ route('dosen.nilai.msib') }}" class="flex items-center justify-between p-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-emerald-50 dark:hover:bg-emerald-900/10 hover:border-emerald-300 dark:hover:border-emerald-700 group transition-colors">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">Konversi Nilai MSIB</span>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-400 group-hover:text-emerald-500"></i>
                    </a>
                </div>
            </div>

            {{-- Export --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-md bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                        <i class="fa-solid fa-file-export"></i>
                    </div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Export</h2>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('dosen.exports.pkl.index') }}" class="flex items-center justify-between p-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-rose-50 dark:hover:bg-rose-900/10 hover:border-rose-300 dark:hover:border-rose-700 group transition-colors">
                        <span class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-rose-600 dark:group-hover:text-rose-400">
                            <i class="fa-solid fa-file-export text-rose-500"></i> Export PKL
                        </span>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-400 group-hover:text-rose-500"></i>
                    </a>
                    <a href="{{ route('dosen.exports.msib.index') }}" class="flex items-center justify-between p-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-rose-50 dark:hover:bg-rose-900/10 hover:border-rose-300 dark:hover:border-rose-700 group transition-colors">
                        <span class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-rose-600 dark:group-hover:text-rose-400">
                            <i class="fa-solid fa-file-export text-rose-500"></i> Export MSIB
                        </span>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-400 group-hover:text-rose-500"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>

</x-app-layout>
