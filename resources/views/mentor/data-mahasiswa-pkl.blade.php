<x-app-layout>
    <x-slot name="title">Mahasiswa</x-slot>

    @php
        $sections = [
            'berjalan' => [
                'label' => 'Berjalan',
                'description' => 'Dokumen belum lengkap dan nilai belum final.',
                'icon' => 'fa-spinner',
                'tone' => 'blue',
                'items' => $sedangBerjalan,
                'empty' => 'Tidak ada mahasiswa yang sedang berjalan.',
            ],
            'perlu_nilai' => [
                'label' => 'Perlu Nilai',
                'description' => 'Dokumen lengkap dan menunggu penilaian.',
                'icon' => 'fa-clipboard-list',
                'tone' => 'amber',
                'items' => $perluNilai,
                'empty' => 'Tidak ada mahasiswa yang memerlukan penilaian.',
            ],
            'selesai' => [
                'label' => 'Selesai',
                'description' => 'Nilai sudah tersimpan.',
                'icon' => 'fa-check-double',
                'tone' => 'emerald',
                'items' => $telahDinilai,
                'empty' => 'Belum ada mahasiswa selesai dinilai.',
            ],
        ];

        $totalMahasiswa = $sedangBerjalan->total() + $perluNilai->total() + $telahDinilai->total();

        $toneClasses = [
            'blue' => [
                'card' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-800/30',
                'button' => 'bg-blue-600 text-white shadow-sm',
                'icon' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
            ],
            'amber' => [
                'card' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-300 dark:border-amber-800/30',
                'button' => 'bg-amber-500 text-white shadow-sm',
                'icon' => 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400',
            ],
            'emerald' => [
                'card' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-800/30',
                'button' => 'bg-emerald-600 text-white shadow-sm',
                'icon' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400',
            ],
        ];
    @endphp

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
        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Mahasiswa Bimbingan</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-2xl">
                    Pantau seluruh peserta PKL dan MSIB/PMK yang memakai email mentor Anda. Detail mahasiswa bisa dibuka dari tombol aksi.
                </p>
            </div>

            <div class="lg:w-72 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg px-4 py-3 flex items-center gap-3">
                <div class="w-11 h-11 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Bimbingan</p>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <span class="text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $totalMahasiswa }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">mahasiswa</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                @foreach($sections as $key => $section)
                    <button type="button" @click="tab = '{{ $key }}'"
                            :class="tab === '{{ $key }}' ? '{{ $toneClasses[$section['tone']]['card'] }} ring-2 ring-offset-1 ring-offset-gray-50 dark:ring-offset-gray-900 ring-current/20' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:border-blue-300 dark:hover:border-blue-700'"
                            class="text-left border rounded-lg px-4 py-3 transition-all min-h-[92px]">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider">{{ $section['label'] }}</p>
                                <p class="mt-1 text-2xl font-semibold tabular-nums">{{ $section['items']->total() }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $toneClasses[$section['tone']]['icon'] }}">
                                <i class="fa-solid {{ $section['icon'] }}"></i>
                            </div>
                        </div>
                        <p class="text-xs mt-1 opacity-80 truncate">{{ $section['description'] }}</p>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-2">
            <div class="relative flex-1">
                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" x-model.debounce.150ms="search" placeholder="Cari nama, NIM, atau instansi..."
                       class="w-full pl-10 pr-10 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                <button x-show="search" x-cloak @click="search = ''" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>

            <div class="flex gap-2 sm:w-auto">
                <a href="{{ route('mentor.pdf.pkl') }}" target="_blank"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition-colors shadow-sm shadow-blue-500/20 flex-1 sm:flex-none">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span>PKL</span>
                </a>
                <a href="{{ route('mentor.pdf.msib') }}" target="_blank"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors flex-1 sm:flex-none">
                    <i class="fa-solid fa-file-pdf text-rose-500"></i>
                    <span>MSIB</span>
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <template x-if="tab === 'berjalan'">
                        <div>
                            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Mahasiswa Berjalan</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Dokumen belum lengkap dan belum dinilai.</p>
                        </div>
                    </template>
                    <template x-if="tab === 'perlu_nilai'">
                        <div>
                            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Mahasiswa Perlu Nilai</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Dokumen sudah lengkap dan siap dinilai.</p>
                        </div>
                    </template>
                    <template x-if="tab === 'selesai'">
                        <div>
                            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Mahasiswa Selesai</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Nilai akhir sudah tersimpan.</p>
                        </div>
                    </template>
                </div>
                <a href="{{ route('mentor.nilai.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-medium transition-colors">
                    <i class="fa-solid fa-pen-to-square"></i>
                    Buka Penilaian
                </a>
            </div>

            @foreach($sections as $key => $section)
                <div x-show="tab === '{{ $key }}'" x-transition.opacity>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider w-14">No</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Mahasiswa</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Jenis</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Instansi</th>
                                    <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Dokumen</th>
                                    <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Nilai</th>
                                    <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider w-20">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($section['items'] as $i => $p)
                                    <tr x-show="search === '' || $el.dataset.search.includes(search.toLowerCase())"
                                        data-search="{{ strtolower(e($p->nama . ' ' . $p->nim . ' ' . $p->tempat_riset . ' ' . $p->jns_pkl)) }}"
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                        <td class="px-4 py-4 text-center text-gray-500 dark:text-gray-400 tabular-nums">{{ $i + 1 }}</td>
                                        <td class="px-4 py-4">
                                            <div class="font-semibold text-gray-900 dark:text-white">{{ $p->nama }}</div>
                                            <div class="font-mono text-xs text-blue-600 dark:text-blue-400 mt-0.5">{{ $p->nim }}</div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium border {{ $p->jns_pkl === 'Magang' ? 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800/30' : 'bg-cyan-50 text-cyan-700 border-cyan-200 dark:bg-cyan-900/20 dark:text-cyan-400 dark:border-cyan-800/30' }}">
                                                {{ $p->jns_pkl === 'Magang' ? 'PKL' : 'MSIB/PMK' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="font-medium text-gray-800 dark:text-gray-200 truncate max-w-[280px]">
                                                <i class="fa-solid fa-building text-gray-400 text-xs mr-1"></i> {{ $p->tempat_riset }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate max-w-[280px]">{{ $p->judul_pkl }}</div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex justify-center gap-1.5">
                                                @foreach(['lp' => 'LP', 'lpp' => 'LPP', 'skp' => 'SKP'] as $field => $label)
                                                    <span @class([
                                                        'px-2 py-1 rounded text-xs font-medium border',
                                                        'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30' => $p->{$field},
                                                        'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-900/20 dark:text-rose-400 dark:border-rose-800/30' => ! $p->{$field},
                                                    ])>{{ $label }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            @if($p->nilai > 0)
                                                <span class="inline-flex items-center justify-center min-w-12 px-2.5 py-1 rounded-md bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 text-sm font-semibold border border-emerald-200 dark:border-emerald-800/30 tabular-nums">
                                                    {{ $p->nilai }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-medium">
                                                    <i class="fa-solid fa-minus text-[10px]"></i> Belum
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <button @click="showDetail('{{ encryptUrl($p->id) }}')"
                                                    class="inline-flex items-center justify-center w-9 h-9 rounded-md bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors"
                                                    title="Lihat Detail">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="p-12 text-center">
                                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto mb-4">
                                                    <i class="fa-solid fa-inbox text-2xl text-gray-400"></i>
                                                </div>
                                                <p class="text-gray-500 dark:text-gray-400 font-medium">{{ $section['empty'] }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($section['items']->hasPages())
                        <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                            {{ $section['items']->links() }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        @include('mentor.partials.mahasiswa-detail-modal')
    </div>
</x-app-layout>
