<x-app-layout>
    <x-slot name="title">Mahasiswa PKL</x-slot>

    <div class="space-y-6" x-data="{
        tab: 'berjalan',
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
                const response = await fetch(`/mentor/mahasiswa/${encryptedId}/detail`);
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
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Mahasiswa Bimbingan PKL — Magang Reguler</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Daftar peserta magang yang berada di bawah bimbingan teknis Anda.</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-2">
                <div class="relative">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" x-model="search" placeholder="Cari nama atau NIM..."
                           class="pl-10 pr-10 py-2 w-full sm:w-64 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    <button x-show="search" @click="search = ''" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
                <a href="{{ route('mentor.pdf.pkl') }}" target="_blank"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition-colors">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span>Cetak Rekap</span>
                </a>
            </div>
        </div>

        {{-- Tab Switcher --}}
        <div class="flex flex-wrap gap-1 p-1 bg-gray-100 dark:bg-gray-800 rounded-md">
            <button @click="tab = 'berjalan'"
                    :class="tab === 'berjalan' ? 'bg-white dark:bg-gray-900 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                    class="flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                <i class="fa-solid fa-spinner"></i> Berjalan
                <span class="text-xs px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-700 tabular-nums">{{ $sedangBerjalan->count() }}</span>
            </button>
            <button @click="tab = 'perlu_nilai'"
                    :class="tab === 'perlu_nilai' ? 'bg-white dark:bg-gray-900 text-amber-600 dark:text-amber-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                    class="flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                <i class="fa-solid fa-clipboard-list"></i> Perlu Nilai
                <span class="text-xs px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-700 tabular-nums">{{ $perluNilai->count() }}</span>
            </button>
            <button @click="tab = 'selesai'"
                    :class="tab === 'selesai' ? 'bg-white dark:bg-gray-900 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                    class="flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                <i class="fa-solid fa-check-double"></i> Selesai
                <span class="text-xs px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-700 tabular-nums">{{ $telahDinilai->count() }}</span>
            </button>
        </div>

        {{-- Reusable empty-state component as anonymous markup repeated --}}

        {{-- Tab: Berjalan --}}
        <div x-show="tab === 'berjalan'" x-transition.opacity class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider w-16">No</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Mahasiswa</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Tempat Riset</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Kelengkapan</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($sedangBerjalan as $i => $p)
                        <tr x-show="(tab === 'berjalan') && (search === '' || $el.dataset.search.includes(search.toLowerCase()))"
                            data-search="{{ strtolower(e($p->nama . ' ' . $p->nim . ' ' . $p->tempat_riset)) }}"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400 tabular-nums">{{ $i + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $p->nama }}</div>
                                <div class="font-mono text-xs text-blue-600 dark:text-blue-400 mt-0.5">{{ $p->nim }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800 dark:text-gray-200 truncate max-w-[240px]">
                                    <i class="fa-solid fa-building text-gray-400 text-xs mr-1"></i> {{ $p->tempat_riset }}
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
                                <button @click="showDetail('{{ encryptUrl($p->user_id) }}')" 
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                                        title="Lihat Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="p-12 text-center">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto mb-4">
                                        <i class="fa-solid fa-inbox text-2xl text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada mahasiswa magang yang sedang berjalan.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tab: Perlu Nilai --}}
        <div x-cloak x-show="tab === 'perlu_nilai'" x-transition.opacity class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider w-16">No</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Mahasiswa</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Tempat Riset</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Status</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($perluNilai as $i => $p)
                        <tr x-show="(tab === 'perlu_nilai') && (search === '' || $el.dataset.search.includes(search.toLowerCase()))"
                            data-search="{{ strtolower(e($p->nama . ' ' . $p->nim . ' ' . $p->tempat_riset)) }}"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400 tabular-nums">{{ $i + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $p->nama }}</div>
                                <div class="font-mono text-xs text-blue-600 dark:text-blue-400 mt-0.5">{{ $p->nim }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800 dark:text-gray-200 truncate max-w-[240px]">
                                    <i class="fa-solid fa-building text-gray-400 text-xs mr-1"></i> {{ $p->tempat_riset }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium border bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800/30">
                                    <i class="fa-solid fa-hourglass-half text-[10px]"></i> Menunggu Penilaian
                                </span>
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
                            <td colspan="5">
                                <div class="p-12 text-center">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto mb-4">
                                        <i class="fa-solid fa-clipboard-check text-2xl text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada mahasiswa yang memerlukan penilaian.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tab: Selesai --}}
        <div x-cloak x-show="tab === 'selesai'" x-transition.opacity class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider w-16">No</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Mahasiswa</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Tempat Riset</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Nilai</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Penilai</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($telahDinilai as $i => $p)
                        <tr x-show="(tab === 'selesai') && (search === '' || $el.dataset.search.includes(search.toLowerCase()))"
                            data-search="{{ strtolower(e($p->nama . ' ' . $p->nim . ' ' . $p->tempat_riset)) }}"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400 tabular-nums">{{ $i + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $p->nama }}</div>
                                <div class="font-mono text-xs text-blue-600 dark:text-blue-400 mt-0.5">{{ $p->nim }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800 dark:text-gray-200 truncate max-w-[240px]">
                                    <i class="fa-solid fa-building text-gray-400 text-xs mr-1"></i> {{ $p->tempat_riset }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $p->nilai }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400">{{ $p->penilai ?? '-' }}</td>
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
                                    <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada mahasiswa selesai dinilai.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @include('mentor.partials.mahasiswa-detail-modal')

    </div>
</x-app-layout>
