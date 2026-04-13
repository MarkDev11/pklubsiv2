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
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Daftar anak bimbing yang berpartisipasi dalam program nasional Kampus Merdeka MSIB.</p>
            </div>
            
            {{-- Modern Alpine Tabs Nav & Search --}}
            <div class="flex flex-col lg:flex-row gap-3 w-full xl:w-auto">
                <div class="bg-gray-100/80 dark:bg-surface-800 p-1.5 rounded-2xl flex flex-nowrap overflow-x-auto custom-scrollbar gap-1 shadow-sm border border-gray-200/50 dark:border-surface-700 max-w-full">
                    <button @click="tab = 'berjalan'" :class="tab === 'berjalan' ? 'bg-white dark:bg-surface-600 text-cyan-600 dark:text-cyan-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="px-4 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2 whitespace-nowrap">
                        <i class="fa-solid fa-spinner" :class="tab === 'berjalan' ? 'animate-spin-slow' : ''"></i> Sedang Berjalan
                        <span class="bg-gray-100 dark:bg-surface-900 px-2 py-0.5 rounded text-xs ml-1">{{ $sedangBerjalan->count() }}</span>
                    </button>
                    <button @click="tab = 'tuntas'" :class="tab === 'tuntas' ? 'bg-white dark:bg-surface-600 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="px-4 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2 whitespace-nowrap">
                        <i class="fa-solid fa-check-double"></i> Telah Tuntas
                        <span class="bg-gray-100 dark:bg-surface-900 px-2 py-0.5 rounded text-xs ml-1">{{ $laporanTuntas->count() }}</span>
                    </button>
                    <button @click="tab = 'belum'" :class="tab === 'belum' ? 'bg-white dark:bg-surface-600 text-rose-600 dark:text-rose-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="px-4 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2 whitespace-nowrap">
                        <i class="fa-solid fa-ghost"></i> Belum Mendaftar
                        <span class="bg-gray-100 dark:bg-surface-900 px-2 py-0.5 rounded text-xs ml-1">{{ $belumInput->count() }}</span>
                    </button>
                </div>
                
                {{-- Search & Export --}}
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative flex-1 sm:flex-initial">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-search text-gray-400"></i>
                        </div>
                        <input type="text" x-model="search" placeholder="Cari nama atau NIM..." class="pl-10 pr-4 py-2 w-full sm:w-64 bg-white dark:bg-surface-800 border border-gray-200 dark:border-surface-700 rounded-xl text-sm focus:ring-cyan-500 focus:border-cyan-500 dark:text-white shadow-sm transition-all h-full outline-none">
                        <button x-show="search" @click="search = ''" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                    
                    <a href="{{ route('dosen.pdf.msib') }}" target="_blank" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-bold shadow-md shadow-cyan-500/20 transition-all active:scale-95 whitespace-nowrap">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>Cetak Rekap</span>
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
                            <th class="px-6 py-4">Informasi Mahasiswa</th>
                            <th class="px-6 py-4">Mitra Kampus Merdeka</th>
                            <th class="px-6 py-4 text-center">Status Berkas Laporan</th>
                            <th class="px-6 py-4 text-center rounded-tr-xl">Lencana</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-surface-700">
                        @forelse($sedangBerjalan as $i => $p)
                        <tr x-show="search === '' || $el.dataset.search.includes(search.toLowerCase())" data-search="{{ strtolower(e($p->nama . ' ' . $p->nim . ' ' . $p->tempat_riset)) }}" class="hover:bg-gray-50/50 dark:hover:bg-surface-800/50 transition-colors group">
                            <td class="px-6 py-4"><span class="w-8 h-8 rounded-full bg-gray-100 dark:bg-surface-700 text-gray-600 dark:text-gray-300 flex items-center justify-center text-xs font-bold">{{ $i+1 }}</span></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-cyan-50 dark:bg-cyan-900/20 text-cyan-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-graduation-cap"></i></div>
                                    <div><p class="font-bold text-gray-900 dark:text-white">{{ $p->nama }}</p><p class="text-xs font-mono text-gray-500 mt-0.5"><i class="fa-regular fa-id-card mr-1"></i> {{ $p->nim }}</p></div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200 line-clamp-1 truncate max-w-[200px]" title="{{ $p->tempat_riset }}">{{ $p->tempat_riset }}</p>
                                <p class="text-xs text-indigo-500 font-medium mt-0.5"><i class="fa-solid fa-user-tie mr-1"></i> {{ $p->nama_mentor }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap items-center justify-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border {{ $p->lp ? 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30' : 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-900/20 dark:text-rose-400 dark:border-rose-800/30' }}">
                                        @if($p->lp) <i class="fa-solid fa-check"></i> LP @else <i class="fa-solid fa-xmark"></i> LP @endif
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border {{ $p->lpp ? 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30' : 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-900/20 dark:text-rose-400 dark:border-rose-800/30' }}">
                                        @if($p->lpp) <i class="fa-solid fa-check"></i> LPP @else <i class="fa-solid fa-xmark"></i> LPP @endif
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border {{ $p->skp ? 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30' : 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-900/20 dark:text-rose-400 dark:border-rose-800/30' }}">
                                        @if($p->skp) <i class="fa-solid fa-check"></i> SKP @else <i class="fa-solid fa-xmark"></i> SKP @endif
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if(!$p->lp && !$p->lpp && !$p->skp)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 text-[10px] font-bold uppercase tracking-widest border border-amber-100 dark:border-amber-800/30">Baru Daftar</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-cyan-50 dark:bg-cyan-900/20 text-cyan-600 dark:text-cyan-400 text-[10px] font-bold uppercase tracking-widest border border-cyan-100 dark:border-cyan-800/30">Penyusunan</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="w-16 h-16 bg-gray-50 dark:bg-surface-700 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100 dark:border-surface-600"><i class="fa-solid fa-box-open text-2xl text-gray-400"></i></div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Tak Ada Mahasiswa Berjalan</h3>
                                <p class="text-sm text-gray-500">Seluruh anak bimbing Anda mungkin sudah menuntaskan laporan, atau malah belum mendaftar sama sekali.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- TAB 2: TELAH TUNTAS --}}
            <div x-cloak x-show="tab === 'tuntas'" x-transition.opacity.duration.300ms class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-surface-900 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest font-bold">
                        <tr>
                            <th class="px-6 py-4 rounded-tl-xl w-16">No</th>
                            <th class="px-6 py-4">Informasi Mahasiswa</th>
                            <th class="px-6 py-4">Mitra Kampus Merdeka</th>
                            <th class="px-6 py-4 text-center">Berkas Valid</th>
                            <th class="px-6 py-4 text-center rounded-tr-xl">Mutu Penilaian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-surface-700">
                        @forelse($laporanTuntas as $i => $p)
                        <tr x-show="search === '' || $el.dataset.search.includes(search.toLowerCase())" data-search="{{ strtolower(e($p->nama . ' ' . $p->nim . ' ' . $p->tempat_riset)) }}" class="hover:bg-emerald-50/30 dark:hover:bg-emerald-900/10 transition-colors group">
                            <td class="px-6 py-4"><span class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center text-xs font-bold">{{ $i+1 }}</span></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100 dark:border-emerald-800/30"><i class="fa-solid fa-graduation-cap"></i></div>
                                    <div><p class="font-bold text-gray-900 dark:text-white">{{ $p->nama }}</p><p class="text-xs font-mono text-gray-500 mt-0.5">{{ $p->nim }}</p></div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200 line-clamp-1 truncate max-w-[200px]">{{ $p->tempat_riset }}</p>
                                <p class="text-xs text-indigo-500 font-medium mt-0.5">{{ $p->nama_mentor }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500 text-white text-[10px] font-bold uppercase tracking-widest shadow-sm">
                                    <i class="fa-solid fa-lock"></i> Komplit (3/3)
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($p->nilai > 0)
                                    <div class="inline-flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center"><i class="fa-solid fa-medal text-xs"></i></div>
                                        <span class="text-xl font-black text-gray-900 dark:text-white">{{ $p->nilai }}</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 text-[10px] font-bold uppercase tracking-widest border border-amber-100 dark:border-amber-800/30"><i class="fa-solid fa-hourglass-half animate-pulse"></i> Menunggu Nilai</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="w-16 h-16 bg-gray-50 dark:bg-surface-700 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100 dark:border-surface-600"><i class="fa-solid fa-folder-open text-2xl text-gray-400"></i></div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Belum Ada Pengumpulan Akhir</h3>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- TAB 3: BELUM INPUT SAMA SEKALI --}}
            <div x-cloak x-show="tab === 'belum'" x-transition.opacity.duration.300ms class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-surface-900 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest font-bold">
                        <tr>
                            <th class="px-6 py-4 rounded-tl-xl w-16">No</th>
                            <th class="px-6 py-4">Identitas Akademik</th>
                            <th class="px-6 py-4">Kontak / Email</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right rounded-tr-xl">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-surface-700">
                        @forelse($belumInput as $i => $u)
                        <tr x-show="search === '' || $el.dataset.search.includes(search.toLowerCase())" data-search="{{ strtolower(e($u->name . ' ' . $u->username)) }}" class="hover:bg-rose-50/30 dark:hover:bg-rose-900/10 transition-colors group">
                            <td class="px-6 py-4"><span class="w-8 h-8 rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-600 flex items-center justify-center text-xs font-bold">{{ $i+1 }}</span></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-surface-700 text-gray-400 flex items-center justify-center shrink-0"><i class="fa-regular fa-user"></i></div>
                                    <div><p class="font-bold text-gray-900 dark:text-white">{{ $u->name }}</p><p class="text-xs font-mono text-gray-500 mt-0.5"><i class="fa-regular fa-id-card mr-1"></i> {{ $u->username }}</p></div>
                                </div>
                            </td>
                            <td class="px-6 py-4"><p class="text-sm text-gray-600 dark:text-gray-300">{{ $u->email }}</p></td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 text-[10px] font-bold uppercase tracking-wider border border-rose-100 dark:border-rose-800/30">Pasif (Tanpa Form)</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="mailto:{{ $u->email }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-surface-700 dark:hover:bg-surface-600 text-gray-700 dark:text-gray-200 text-xs font-bold transition-colors">
                                    <i class="fa-solid fa-envelope"></i> Hubungi
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-900/20 rounded-full flex items-center justify-center mx-auto mb-4 border border-emerald-100 dark:border-emerald-800/30"><i class="fa-solid fa-check-double text-2xl text-emerald-500"></i></div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Aman & Terkendali</h3>
                                <p class="text-sm text-gray-500">Seluruh anak bimbing Anda telah mengisi data pendaftaran awal.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
