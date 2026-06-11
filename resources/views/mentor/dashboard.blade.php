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

        {{-- Timeline Sistem --}}
        @if($deadline)
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-blue-500"></i>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Timeline Sistem</h2>
                </div>

                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @php
                        $now = now();
                        $timelineItems = [
                            ['label' => 'Input Data PKL', 'open' => $deadline->open_time, 'close' => $deadline->close_time, 'icon' => 'fa-file-lines'],
                            ['label' => 'Upload Laporan', 'open' => $deadline->open_laporan, 'close' => $deadline->close_laporan, 'icon' => 'fa-cloud-arrow-up'],
                            ['label' => 'Input Nilai', 'open' => $deadline->open_nilai, 'close' => $deadline->close_nilai, 'icon' => 'fa-star'],
                        ];
                    @endphp

                    @foreach($timelineItems as $item)
                        @if($item['open'] && $item['close'])
                            @php
                                $open = \Carbon\Carbon::parse($item['open']);
                                $close = \Carbon\Carbon::parse($item['close']);
                                $isClosed = $now->gt($close);
                                $isPending = $now->lt($open);
                            @endphp

                            <div class="p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-md flex items-center justify-center {{ $isClosed ? 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300' : ($isPending ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400') }}">
                                        <i class="fa-solid {{ $item['icon'] }}"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item['label'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $open->format('d M Y') }} — {{ $close->format('d M Y') }}</p>
                                    </div>
                                </div>

                                <div>
                                    @if($isClosed)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">Ditutup</span>
                                    @elseif($isPending)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800/30">Belum Buka</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30">Buka · {{ (int) $now->diffInDays($close) }} hari lagi</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Quick Actions --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('mentor.mahasiswa.index') }}"
               class="group bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 hover:border-blue-300 dark:hover:border-blue-700 hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-md bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Data Mahasiswa</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola seluruh mahasiswa bimbingan dan pantau kelengkapan dokumen.</p>
                        <span class="inline-flex items-center gap-1 mt-3 text-sm font-medium text-blue-600 dark:text-blue-400">
                            Buka <i class="fa-solid fa-arrow-right text-xs"></i>
                        </span>
                    </div>
                </div>
            </a>

            <a href="{{ route('mentor.nilai.index') }}"
               class="group bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 hover:border-blue-300 dark:hover:border-blue-700 hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-md bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-rocket"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Penilaian</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Input nilai seluruh peserta PKL dan MSIB/PMK.</p>
                        <span class="inline-flex items-center gap-1 mt-3 text-sm font-medium text-blue-600 dark:text-blue-400">
                            Buka <i class="fa-solid fa-arrow-right text-xs"></i>
                        </span>
                    </div>
                </div>
            </a>
        </div>

    </div>
</x-app-layout>
