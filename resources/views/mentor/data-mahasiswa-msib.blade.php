<x-app-layout>
    <x-slot name="title">Mahasiswa MSIB</x-slot>
    
    <div class="space-y-6 animate-fade-in pb-10" x-data="{ tab: 'berjalan', search: '' }">

        {{-- Page Header --}}
        <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-cyan-100 dark:bg-cyan-500/10 text-cyan-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-rocket text-xl"></i>
                    </div>
                    Data Mahasiswa MSIB
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Daftar peserta MSIB (Magang & Studi Independen Bersertifikat) yang Anda bimbing.</p>
            </div>
            
            {{-- Modern Alpine Tabs Nav & Search --}}
            <div class="flex flex-col lg:flex-row gap-3 w-full xl:w-auto">
                <div class="bg-gray-100/80 dark:bg-surface-800 p-1.5 rounded-2xl flex flex-nowrap overflow-x-auto custom-scrollbar gap-1 shadow-sm border border-gray-200/50 dark:border-surface-700 max-w-full">
                    <button @click="tab = 'berjalan'" :class="tab === 'berjalan' ? 'bg-white dark:bg-surface-600 text-cyan-600 dark:text-cyan-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="px-4 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2 whitespace-nowrap">
                        <i class="fa-solid fa-spinner" :class="tab === 'berjalan' ? 'animate-spin-slow' : ''"></i> Berjalan
                        <span class="bg-gray-100 dark:bg-surface-900 px-2 py-0.5 rounded text-xs ml-1">{{ $sedangBerjalan->count() }}</span>
                    </button>
                    <button @click="tab = 'perlu_nilai'" :class="tab === 'perlu_nilai' ? 'bg-white dark:bg-surface-600 text-amber-600 dark:text-amber-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="px-4 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2 whitespace-nowrap">
                        <i class="fa-solid fa-file-signature"></i> Perlu Nilai
                        <span class="bg-gray-100 dark:bg-surface-900 px-2 py-0.5 rounded text-xs ml-1">{{ $perluNilai->count() }}</span>
                    </button>
                    <button @click="tab = 'selesai'" :class="tab === 'selesai' ? 'bg-white dark:bg-surface-600 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="px-4 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2 whitespace-nowrap">
                        <i class="fa-solid fa-check-double"></i> Selesai
                        <span class="bg-gray-100 dark:bg-surface-900 px-2 py-0.5 rounded text-xs ml-1">{{ $telahDinilai->count() }}</span>
                    </button>
                </div>
                
                {{-- Search & Export --}}
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative flex-1 sm:flex-initial">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-search text-gray-400"></i>
                        </div>
                        <input type="text" x-model="search" placeholder="Cari nama atau NIM..." class="pl-10 pr-4 py-2 w-full sm:w-64 bg-white dark:bg-surface-800 border border-gray-200 dark:border-surface-700 rounded-xl text-sm focus:ring-cyan-500 focus:border-cyan-500 dark:text-white shadow-sm transition-all h-full outline-none">
                    </div>
                    
                    <a href="{{ route('mentor.pdf.msib') }}" target="_blank" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-bold shadow-md shadow-cyan-500/20 transition-all active:scale-95 whitespace-nowrap">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>Rekap</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Main Container --}}
        <div class="bg-white dark:bg-surface-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-surface-700 overflow-hidden relative">
            
            {{-- TAB 1: SEDANG BERJALAN --}}
            <div x-show="tab === 'berjalan'" x-transition.opacity.duration.300ms class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-surface-900 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest font-bold">
                        <tr>
                            <th class="px-6 py-4 rounded-tl-xl w-16">No</th>
                            <th class="px-6 py-4">Peserta MSIB</th>
                            <th class="px-6 py-4">Mitra (Perusahaan)</th>
                            <th class="px-6 py-4 text-center">Progres Berkas</th>
                            <th class="px-6 py-4 text-center rounded-tr-xl">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-surface-700">
                        @forelse($sedangBerjalan as $i => $p)
                        <tr x-show="search === '' || $el.dataset.search.includes(search.toLowerCase())" data-search="{{ strtolower(e($p->nama . ' ' . $p->nim . ' ' . $p->tempat_riset)) }}" class="hover:bg-gray-50/50 dark:hover:bg-surface-800/50 transition-colors group">
                            <td class="px-6 py-4 text-center font-bold text-gray-400">{{ $i+1 }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-cyan-50 dark:bg-cyan-900/20 text-cyan-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-user-tag"></i></div>
                                    <div><p class="font-bold text-gray-900 dark:text-white">{{ $p->nama }}</p><p class="text-xs font-mono text-gray-500 mt-0.5">{{ $p->nim }}</p></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-700 dark:text-gray-300">{{ $p->tempat_riset }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap items-center justify-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $p->lp ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-400 border-rose-100' }}">LP</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $p->lpp ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-400 border-rose-100' }}">LPP</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $p->skp ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-400 border-rose-100' }}">SKP</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-lg bg-cyan-50 dark:bg-cyan-900/10 text-cyan-600 dark:text-cyan-400 text-[10px] font-black uppercase tracking-widest border border-cyan-100 dark:border-cyan-800/30">Ongoing</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-16 text-center text-gray-500">Tidak ada mahasiswa MSIB yang sedang berjalan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- TAB 2: PERLU DINILAI --}}
            <div x-cloak x-show="tab === 'perlu_nilai'" x-transition.opacity.duration.300ms class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-surface-900 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest font-bold">
                        <tr>
                            <th class="px-6 py-4 rounded-tl-xl w-16">No</th>
                            <th class="px-6 py-4">Mahasiswa MSIB</th>
                            <th class="px-6 py-4">Program / Mitra</th>
                            <th class="px-6 py-4 text-center">Review Sertifikat</th>
                            <th class="px-6 py-4 text-right rounded-tr-xl">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-surface-700">
                        @forelse($perluNilai as $i => $p)
                        <tr x-show="search === '' || $el.dataset.search.includes(search.toLowerCase())" data-search="{{ strtolower(e($p->nama . ' ' . $p->nim . ' ' . $p->tempat_riset)) }}" class="hover:bg-amber-50/30 dark:hover:bg-amber-900/10 transition-colors group">
                            <td class="px-6 py-4 text-center font-bold text-gray-400">{{ $i+1 }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-amber-50 dark:bg-amber-900/20 text-amber-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-graduation-cap"></i></div>
                                    <div><p class="font-bold text-gray-900 dark:text-white">{{ $p->nama }}</p><p class="text-xs font-mono text-gray-500 mt-0.5">{{ $p->nim }}</p></div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $p->tempat_riset }}</p>
                                <p class="text-[10px] text-indigo-500 font-bold mt-0.5">MSIB / PMK PROGRAM</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-widest border border-emerald-200">Lengkap</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('mentor.nilai.msib') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-bold transition-all shadow-md shadow-cyan-500/20 active:scale-95">
                                    <i class="fa-solid fa-clipboard-check"></i> Input Nilai
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-16 text-center text-gray-500">Semua laporan MSIB sudah diproses penilaian.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- TAB 3: TELAH DINILAI --}}
            <div x-cloak x-show="tab === 'selesai'" x-transition.opacity.duration.300ms class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-surface-900 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest font-bold">
                        <tr>
                            <th class="px-6 py-4 rounded-tl-xl w-16">No</th>
                            <th class="px-6 py-4">Identitas Peserta</th>
                            <th class="px-6 py-4">Mitra MSIB</th>
                            <th class="px-6 py-4 text-center">Skor</th>
                            <th class="px-6 py-4 text-center rounded-tr-xl">Status Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-surface-700">
                        @forelse($telahDinilai as $i => $p)
                        <tr x-show="search === '' || $el.dataset.search.includes(search.toLowerCase())" data-search="{{ strtolower(e($p->nama . ' ' . $p->nim)) }}" class="hover:bg-emerald-50/30 dark:hover:bg-emerald-900/10 transition-colors group">
                            <td class="px-6 py-4 text-center font-bold text-gray-400">{{ $i+1 }}</td>
                            <td class="px-6 py-4 text-gray-900 dark:text-white font-bold">{{ $p->nama }}<br><span class="text-[10px] font-mono text-gray-500 font-normal">{{ $p->nim }}</span></td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400 text-xs">{{ $p->tempat_riset }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center font-black text-xs mx-auto shadow-sm">{{ $p->nilai }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded bg-emerald-100 text-emerald-700 text-[10px] font-bold">TERVALIDASI</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-16 text-center text-gray-500">Belum ada data nilai MSIB yang tersimpan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
