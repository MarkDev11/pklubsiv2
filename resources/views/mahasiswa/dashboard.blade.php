<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <!-- Welcome Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
            Selamat Datang, {{ auth()->user()->name }}
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            <span class="font-mono text-blue-600 dark:text-blue-400">{{ auth()->user()->username }}</span>
            <span class="mx-1">·</span>
            {{ ucfirst(auth()->user()->role->value) }}
        </p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Administrasi -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Administrasi</h3>
                <div class="w-9 h-9 rounded-md flex items-center justify-center {{ $proposal?->skm ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' }}">
                    @if($proposal?->skm)
                        <i class="fa-solid fa-check"></i>
                    @else
                        <i class="fa-solid fa-hourglass-half"></i>
                    @endif
                </div>
            </div>
            @if($proposal?->skm)
                <p class="text-base font-semibold text-gray-900 dark:text-white">SKM Selesai</p>
                <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-0.5">Terverifikasi</p>
            @elseif($proposal)
                <p class="text-base font-semibold text-gray-900 dark:text-white">Menunggu</p>
                <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">Proses verifikasi</p>
            @else
                <p class="text-base font-semibold text-gray-900 dark:text-white">Belum Input</p>
                <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">Data PKL belum dibuat</p>
                <a href="{{ route('mahasiswa.proposal.index') }}" class="inline-flex items-center gap-1 mt-3 text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                    Input Data PKL <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            @endif
        </div>

        <!-- Dokumen -->
        @php
            $uploadCount = 0;
            if($proposal?->lp) $uploadCount++;
            if($proposal?->lpp) $uploadCount++;
            if($proposal?->skp) $uploadCount++;
        @endphp

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dokumen</h3>
                <div class="w-9 h-9 rounded-md bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <i class="fa-solid fa-file-lines"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-semibold tabular-nums {{ $uploadCount === 3 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-900 dark:text-white' }}">{{ $uploadCount }}</span>
                <span class="text-sm text-gray-500 dark:text-gray-400">/ 3 file diupload</span>
            </div>
            <a href="{{ $proposal ? route('mahasiswa.laporan.index') : route('mahasiswa.proposal.index') }}" class="inline-flex items-center gap-1 mt-3 text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                {{ $proposal ? 'Kelola dokumen' : 'Input Data PKL dulu' }} <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <!-- Nilai -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nilai Akhir</h3>
                <div class="w-9 h-9 rounded-md bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <i class="fa-solid fa-star"></i>
                </div>
            </div>
            @if($proposal && $proposal->nilai > 0)
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $proposal->nilai }}</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">/ 100</span>
                </div>
                <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-0.5">Telah dinilai</p>
            @elseif($proposal)
                <p class="text-base font-semibold text-gray-900 dark:text-white">Belum tersedia</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Menunggu input nilai</p>
            @else
                <p class="text-base font-semibold text-gray-900 dark:text-white">Belum tersedia</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Input Data PKL terlebih dulu</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Status Pengajuan -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Status Pengajuan PKL</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">NIM</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white font-mono">{{ $proposal?->nim ?? auth()->user()->username }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Nama</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Status SKM</p>
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border {{ $proposal?->skm ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30' : 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800/30' }}">
                            {{ $proposal ? ($proposal->skm ? 'Selesai' : 'Menunggu') : 'Belum Input' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Dokumen Laporan</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $uploadCount }} dari 3 file diupload</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Aksi Cepat</h2>
            </div>
            <div class="p-4 space-y-2">
                <a href="{{ route('mahasiswa.proposal.index') }}" class="flex items-center gap-3 p-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/10 hover:border-blue-300 dark:hover:border-blue-700 group transition-colors">
                    <div class="w-9 h-9 rounded-md bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">Data PKL</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Lihat detail pengajuan</p>
                    </div>
                </a>
                <a href="{{ $proposal ? route('mahasiswa.laporan.index') : route('mahasiswa.proposal.index') }}" class="flex items-center gap-3 p-3 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/10 hover:border-blue-300 dark:hover:border-blue-700 group transition-colors">
                    <div class="w-9 h-9 rounded-md bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">Upload Laporan</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $proposal ? 'Kelola dokumen laporan' : 'Input Data PKL lebih dulu' }}</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Deadlines -->
    @if($deadline)
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
            <i class="fa-solid fa-calendar-days text-blue-500"></i>
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Batas Waktu Sistem</h2>
        </div>

        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @php $now = now(); @endphp

            @if($deadline->open_time && $deadline->close_time)
                @php $close = \Carbon\Carbon::parse($deadline->close_time); $open = \Carbon\Carbon::parse($deadline->open_time); @endphp

                <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Input Data PKL</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $open->format('d M Y') }} — {{ $close->format('d M Y') }}</p>
                    </div>
                    <div>
                        @if($now->gt($close))
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">Ditutup</span>
                        @elseif($now->lt($open))
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800/30">Belum Buka</span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30">Buka · {{ (int)$now->diffInDays($close) }} hari lagi</span>
                        @endif
                    </div>
                </div>
            @endif

            @if($deadline->open_laporan && $deadline->close_laporan)
                @php $closeLp = \Carbon\Carbon::parse($deadline->close_laporan); $openLp = \Carbon\Carbon::parse($deadline->open_laporan); @endphp

                <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Upload Laporan</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $openLp->format('d M Y') }} — {{ $closeLp->format('d M Y') }}</p>
                    </div>
                    <div>
                        @if($now->gt($closeLp))
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">Ditutup</span>
                        @elseif($now->lt($openLp))
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800/30">Belum Buka</span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30">Buka · {{ (int)$now->diffInDays($closeLp) }} hari lagi</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif

</x-app-layout>
