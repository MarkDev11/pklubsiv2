<x-app-layout>
    <x-slot name="title">Dashboard Mentor</x-slot>

    <div class="space-y-6">

        {{-- Welcome Header --}}
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Selamat Datang, {{ $user->name }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-building-shield text-xs"></i> Mentor Industri
                </span>
                <span class="mx-1">·</span>
                <span>{{ now()->format('l, d F Y') }}</span>
            </p>
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
                        <i class="fa-solid fa-calendar-xmark"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-red-800 dark:text-red-300">Masa Penilaian Berakhir</p>
                        <p class="text-xs text-red-700 dark:text-red-400 mt-0.5">Sistem penutupan nilai aktif sejak {{ $closeNilai->format('d M Y') }}. Hubungi Admin jika ada kendala.</p>
                    </div>
                </div>
            @elseif($now->gte($openNilai))
                <div class="flex items-center gap-3 p-4 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/30">
                    <div class="w-10 h-10 rounded-md bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">Periode Penilaian Aktif</p>
                        <p class="text-xs text-amber-700 dark:text-amber-400 mt-0.5">Sisa <strong class="tabular-nums">{{ intval($now->diffInDays($closeNilai)) }} hari</strong> sampai {{ $closeNilai->format('d M Y') }}.</p>
                    </div>
                </div>
            @endif
        @endif

        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Bimbingan</h3>
                    <div class="w-9 h-9 rounded-md bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <p class="text-3xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $jumlahMahasiswa }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">mahasiswa</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sudah Dinilai</h3>
                    <div class="w-9 h-9 rounded-md bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
                <p class="text-3xl font-semibold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ $sudahDinilai }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">selesai diberi nilai</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Belum Dinilai</h3>
                    <div class="w-9 h-9 rounded-md bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                </div>
                <p class="text-3xl font-semibold text-amber-600 dark:text-amber-400 tabular-nums">{{ $belumDinilai }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">menunggu nilai</p>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('mentor.mahasiswa.pkl') }}"
               class="group bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 hover:border-blue-300 dark:hover:border-blue-700 hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-md bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Manajemen PKL Magang</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola mahasiswa jalur magang reguler dan pantau kelengkapan dokumen.</p>
                        <span class="inline-flex items-center gap-1 mt-3 text-sm font-medium text-blue-600 dark:text-blue-400">
                            Buka <i class="fa-solid fa-arrow-right text-xs"></i>
                        </span>
                    </div>
                </div>
            </a>

            <a href="{{ route('mentor.mahasiswa.msib') }}"
               class="group bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 hover:border-blue-300 dark:hover:border-blue-700 hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-md bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-rocket"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Manajemen MSIB / PMK</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Monitoring peserta Kampus Merdeka dan verifikasi sertifikat.</p>
                        <span class="inline-flex items-center gap-1 mt-3 text-sm font-medium text-blue-600 dark:text-blue-400">
                            Buka <i class="fa-solid fa-arrow-right text-xs"></i>
                        </span>
                    </div>
                </div>
            </a>
        </div>

    </div>
</x-app-layout>
