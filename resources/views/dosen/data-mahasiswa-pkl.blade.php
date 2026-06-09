<x-app-layout>
    <x-slot name="title">Mahasiswa PKL</x-slot>

    <div class="space-y-6" x-data="{
        statusFilter: 'berjalan',
        search: '',
        modalOpen: false,
        modalData: null,
        modalLoading: false,
        modalError: null,
        detailCache: {},
        async showDetail(encryptedId) {
            const cached = this.detailCache[encryptedId];
            if (cached && (Date.now() - cached.timestamp < 300000)) {
                this.modalData = cached.data;
                this.modalOpen = true;
                return;
            }
            this.modalOpen = true;
            this.modalLoading = true;
            this.modalError = null;
            this.modalData = null;
            try {
                const response = await fetch(`/dosen/mahasiswa/${encryptedId}/detail`);
                if (!response.ok) throw new Error('Gagal memuat data');
                const data = await response.json();
                this.modalData = data;
                this.detailCache[encryptedId] = { data, timestamp: Date.now() };
            } catch (error) {
                this.modalError = error.message;
            } finally {
                this.modalLoading = false;
            }
        }
    }">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Mahasiswa Bimbingan PKL — Magang Reguler</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Daftar mahasiswa bimbingan jalur Praktik Kerja Lapangan reguler.</p>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Sedang Berjalan --}}
            <button @click="statusFilter = (statusFilter === 'berjalan' ? '' : 'berjalan')"
                 :class="statusFilter === 'berjalan' ? 'ring-2 ring-blue-500 ring-offset-2 dark:ring-offset-gray-900' : ''"
                 class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-gray-300 dark:hover:border-gray-600 transition-all text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-md flex items-center justify-center">
                        <i class="fa-solid fa-spinner text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sedang Berjalan</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $sedangBerjalan->total() }}</p>
                    </div>
                </div>
            </button>

            {{-- Telah Tuntas --}}
            <button @click="statusFilter = (statusFilter === 'tuntas' ? '' : 'tuntas')"
                 :class="statusFilter === 'tuntas' ? 'ring-2 ring-emerald-500 ring-offset-2 dark:ring-offset-gray-900' : ''"
                 class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-gray-300 dark:hover:border-gray-600 transition-all text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/20 rounded-md flex items-center justify-center">
                        <i class="fa-solid fa-check-double text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Telah Tuntas</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $laporanTuntas->total() }}</p>
                    </div>
                </div>
            </button>

            {{-- Belum Mendaftar --}}
            <button @click="statusFilter = (statusFilter === 'belum' ? '' : 'belum')"
                 :class="statusFilter === 'belum' ? 'ring-2 ring-rose-500 ring-offset-2 dark:ring-offset-gray-900' : ''"
                 class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-gray-300 dark:hover:border-gray-600 transition-all text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-rose-50 dark:bg-rose-900/20 rounded-md flex items-center justify-center">
                        <i class="fa-solid fa-user-slash text-rose-600 dark:text-rose-400"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Belum Input Data</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $belumInput->total() }}</p>
                    </div>
                </div>
            </button>
        </div>

        <p class="text-xs text-gray-500 dark:text-gray-400">
            <i class="fa-solid fa-circle-info mr-1"></i> Klik kartu untuk filter tabel
        </p>

        {{-- Search & Export --}}
        <div class="flex flex-col sm:flex-row justify-between gap-3">
            <div class="relative flex-1 max-w-md">
                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" x-model="search" placeholder="Cari nama, NIM, atau tempat riset..."
                       class="pl-10 pr-10 py-2 w-full border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                <button x-show="search" @click="search = ''" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <a href="{{ route('dosen.pdf.pkl') }}" target="_blank"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition-colors">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Cetak Rekap</span>
            </a>
        </div>

        {{-- Sedang Berjalan --}}
        <div x-show="statusFilter === '' || statusFilter === 'berjalan'" x-transition.opacity class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider w-16">No</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Mahasiswa</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Tempat & Mentor</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Kelengkapan</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Status</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($sedangBerjalan as $i => $p)
                        <tr x-show="(statusFilter === '' || statusFilter === 'berjalan') && (search === '' || $el.dataset.search.includes(search.toLowerCase()))"
                            data-search="{{ strtolower(e($p->nama . ' ' . $p->nim . ' ' . $p->tempat_riset)) }}"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400 tabular-nums">{{ $i + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $p->nama }}</div>
                                <div class="font-mono text-xs text-blue-600 dark:text-blue-400 mt-0.5">{{ $p->nim }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800 dark:text-gray-200 truncate max-w-[200px]" title="{{ $p->tempat_riset }}">
                                    <i class="fa-solid fa-building text-gray-400 text-xs mr-1"></i> {{ $p->tempat_riset }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    <i class="fa-solid fa-user-tie text-xs mr-1"></i> {{ $p->nama_mentor ?? 'Belum ada mentor' }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-1.5">
                                    <span @class([
                                        'px-2 py-1 rounded text-xs font-medium border',
                                        'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30' => $p->lp,
                                        'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800/30' => !$p->lp,
                                    ])>LP</span>
                                    <span @class([
                                        'px-2 py-1 rounded text-xs font-medium border',
                                        'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30' => $p->lpp,
                                        'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800/30' => !$p->lpp,
                                    ])>LPP</span>
                                    <span @class([
                                        'px-2 py-1 rounded text-xs font-medium border',
                                        'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30' => $p->skp,
                                        'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800/30' => !$p->skp,
                                    ])>SKP</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if(!$p->lp && !$p->lpp && !$p->skp)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800/30">Baru Daftar</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800/30">Penyusunan</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button @click="showDetail('{{ encryptUrl($p->user_id) }}')" 
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                                        title="Lihat Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="p-12 text-center">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto mb-4">
                                        <i class="fa-solid fa-inbox text-2xl text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada mahasiswa berjalan saat ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination for Sedang Berjalan --}}
            @if($sedangBerjalan->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex flex-col sm:flex-row justify-between items-center gap-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Menampilkan {{ $sedangBerjalan->firstItem() ?? 0 }} - {{ $sedangBerjalan->lastItem() ?? 0 }} dari {{ $sedangBerjalan->total() }} mahasiswa
                    </p>
                    <div>
                        {{ $sedangBerjalan->appends(['tuntas' => request('tuntas'), 'belum' => request('belum')])->links() }}
                    </div>
                </div>
            @endif
        </div>

        {{-- Telah Tuntas --}}
        <div x-cloak x-show="statusFilter === '' || statusFilter === 'tuntas'" x-transition.opacity class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider w-16">No</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Mahasiswa</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Tempat & Mentor</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Berkas</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Nilai</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($laporanTuntas as $i => $p)
                        <tr x-show="(statusFilter === '' || statusFilter === 'tuntas') && (search === '' || $el.dataset.search.includes(search.toLowerCase()))"
                            data-search="{{ strtolower(e($p->nama . ' ' . $p->nim . ' ' . $p->tempat_riset)) }}"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400 tabular-nums">{{ $i + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $p->nama }}</div>
                                <div class="font-mono text-xs text-blue-600 dark:text-blue-400 mt-0.5">{{ $p->nim }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800 dark:text-gray-200 truncate max-w-[200px]">
                                    <i class="fa-solid fa-building text-gray-400 text-xs mr-1"></i> {{ $p->tempat_riset }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    <i class="fa-solid fa-user-tie text-xs mr-1"></i> {{ $p->nama_mentor }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium border bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30">
                                    <i class="fa-solid fa-check-double text-[10px]"></i> Komplit
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($p->nilai > 0)
                                    <span class="text-xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $p->nilai }}</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800/30">Menunggu</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button @click="showDetail('{{ encryptUrl($p->user_id) }}')" 
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                                        title="Lihat Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="p-12 text-center">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto mb-4">
                                        <i class="fa-solid fa-folder-open text-2xl text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada mahasiswa yang menyelesaikan laporan.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination for Laporan Tuntas --}}
            @if($laporanTuntas->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex flex-col sm:flex-row justify-between items-center gap-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Menampilkan {{ $laporanTuntas->firstItem() ?? 0 }} - {{ $laporanTuntas->lastItem() ?? 0 }} dari {{ $laporanTuntas->total() }} mahasiswa
                    </p>
                    <div>
                        {{ $laporanTuntas->appends(['berjalan' => request('berjalan'), 'belum' => request('belum')])->links() }}
                    </div>
                </div>
            @endif
        </div>

        {{-- Belum Mendaftar --}}
        <div x-cloak x-show="statusFilter === '' || statusFilter === 'belum'" x-transition.opacity class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider w-16">No</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Identitas</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Email</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($belumInput as $i => $u)
                        <tr x-show="(statusFilter === '' || statusFilter === 'belum') && (search === '' || $el.dataset.search.includes(search.toLowerCase()))"
                            data-search="{{ strtolower(e($u->name . ' ' . $u->username)) }}"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400 tabular-nums">{{ $i + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $u->name }}</div>
                                <div class="font-mono text-xs text-blue-600 dark:text-blue-400 mt-0.5">{{ $u->username }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $u->email }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="showDetail('{{ encryptUrl($u->id) }}')" 
                                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                                            title="Lihat Detail">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <a href="mailto:{{ $u->email }}"
                                       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs font-medium transition-colors">
                                        <i class="fa-solid fa-envelope"></i> Hubungi
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4">
                                <div class="p-12 text-center">
                                    <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg flex items-center justify-center mx-auto mb-4">
                                        <i class="fa-solid fa-check-double text-2xl text-emerald-500"></i>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 font-medium">Aman — semua mahasiswa bimbingan sudah mendaftar.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination for Belum Input --}}
            @if($belumInput->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex flex-col sm:flex-row justify-between items-center gap-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Menampilkan {{ $belumInput->firstItem() ?? 0 }} - {{ $belumInput->lastItem() ?? 0 }} dari {{ $belumInput->total() }} mahasiswa
                    </p>
                    <div>
                        {{ $belumInput->appends(['berjalan' => request('berjalan'), 'tuntas' => request('tuntas')])->links() }}
                    </div>
                </div>
            @endif
        </div>

        @include('dosen.partials.mahasiswa-detail-modal')

    </div>
</x-app-layout>
