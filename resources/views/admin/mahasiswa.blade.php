<x-app-layout>
    <x-slot name="title">Status Mahasiswa</x-slot>

    <div class="space-y-6" x-data="mahasiswaManager()" x-init="fetchData()">
        
        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Board Pemantauan Status PKL/MSIB</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pantau progress mahasiswa dalam melengkapi dokumen PKL</p>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Belum Input --}}
            <button @click="statusFilter = (statusFilter === 'belum' ? '' : 'belum'); fetchData()"
                 :class="statusFilter === 'belum' ? 'ring-2 ring-red-500 ring-offset-2 dark:ring-offset-gray-900' : ''"
                 class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-gray-300 dark:hover:border-gray-600 transition-all text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-50 dark:bg-red-900/20 rounded-md flex items-center justify-center">
                        <i class="fa-solid fa-user-xmark text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Belum Input</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $totalBelum }}</p>
                    </div>
                </div>
            </button>

            {{-- Sedang Proses --}}
            <button @click="statusFilter = (statusFilter === 'proses' ? '' : 'proses'); fetchData()"
                 :class="statusFilter === 'proses' ? 'ring-2 ring-amber-500 ring-offset-2 dark:ring-offset-gray-900' : ''"
                 class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-gray-300 dark:hover:border-gray-600 transition-all text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/20 rounded-md flex items-center justify-center">
                        <i class="fa-solid fa-hourglass-half text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sedang Proses</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $totalProses }}</p>
                    </div>
                </div>
            </button>

            {{-- Komplit --}}
            <button @click="statusFilter = (statusFilter === 'selesai' ? '' : 'selesai'); fetchData()"
                 :class="statusFilter === 'selesai' ? 'ring-2 ring-emerald-500 ring-offset-2 dark:ring-offset-gray-900' : ''"
                 class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-gray-300 dark:hover:border-gray-600 transition-all text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/20 rounded-md flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dokumen Komplit</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $totalSelesai }}</p>
                    </div>
                </div>
            </button>
        </div>
        
        <p class="text-xs text-gray-500 dark:text-gray-400">
            <i class="fa-solid fa-circle-info mr-1"></i> Klik kartu untuk filter tabel
        </p>

        {{-- Table Section --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            
            <div class="flex flex-col sm:flex-row justify-between gap-4 mb-4">
                {{-- Search --}}
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                    </div>
                    <input type="text" x-model.debounce.300ms="search" placeholder="Cari nama, NIM, tempat riset..."
                           class="w-full pl-10 pr-4 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                {{-- PDF Export & Per Page --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                    <a href="{{ route('admin.pdf.pkl') }}" target="_blank"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition-colors">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>Rekap PKL</span>
                    </a>
                    <a href="{{ route('admin.pdf.msib') }}" target="_blank"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium transition-colors">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>Rekap MSIB</span>
                    </a>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Show:</span>
                        <select x-model="perPage" @change="currentPage = 1; fetchData()"
                                class="px-3 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden relative">
                {{-- Loading Overlay --}}
                <div x-show="loading" class="absolute inset-0 z-50 bg-white/95 dark:bg-gray-800/95 flex items-center justify-center">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Memuat data...</span>
                    </div>
                </div>

                {{-- Table Content --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider w-16">No</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">NIM</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Nama Mahasiswa</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Jenis</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Tempat Riset</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Mentor</th>
                                <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <template x-for="(r, idx) in rows" :key="r.nim">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 tabular-nums" x-text="r.no"></td>
                                    <td class="px-4 py-3">
                                        <span class="font-mono font-semibold text-gray-900 dark:text-white" x-text="r.nim"></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="font-medium text-gray-900 dark:text-white" x-text="r.nama"></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div x-html="r.jenis"></div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-gray-700 dark:text-gray-300" x-text="r.tempat || '-'"></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-gray-600 dark:text-gray-400" x-text="r.mentor || '-'"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div x-html="r.status"></div>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="!loading && rows.length === 0">
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-magnifying-glass text-lg text-gray-400"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tidak ada data ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="flex flex-col sm:flex-row justify-between items-center mt-4 gap-4">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    Menampilkan <strong class="text-gray-900 dark:text-white tabular-nums" x-text="rows.length > 0 ? currentStart + 1 : 0"></strong> hingga <strong class="text-gray-900 dark:text-white tabular-nums" x-text="currentStart + rows.length"></strong> dari <strong class="text-gray-900 dark:text-white tabular-nums" x-text="filteredRecords"></strong> entri
                </span>
                
                <div class="flex gap-1">
                    <button @click="prevPage()" :disabled="currentPage <= 1"
                            class="w-8 h-8 flex items-center justify-center rounded-md text-sm font-medium transition-colors disabled:opacity-30 disabled:cursor-not-allowed bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    <template x-for="p in pageNumbers" :key="p">
                        <button @click="goToPage(p)"
                                :class="p === currentPage 
                                    ? 'bg-blue-600 text-white border-blue-600' 
                                    : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600'"
                                class="w-8 h-8 flex items-center justify-center rounded-md text-sm font-medium transition-colors border tabular-nums"
                                x-text="p"></button>
                    </template>
                    <button @click="nextPage()" :disabled="currentPage >= totalPages"
                            class="w-8 h-8 flex items-center justify-center rounded-md text-sm font-medium transition-colors disabled:opacity-30 disabled:cursor-not-allowed bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
    function mahasiswaManager() {
        return {
            rows: [],
            search: '',
            statusFilter: '',
            loading: true,
            totalRecords: 0,
            filteredRecords: 0,
            perPage: 10,
            currentPage: 1,

            get currentStart() { return (this.currentPage - 1) * parseInt(this.perPage); },
            get totalPages() { return Math.max(1, Math.ceil(this.filteredRecords / parseInt(this.perPage))); },
            get pageNumbers() {
                let pages = [];
                let start = Math.max(1, this.currentPage - 2);
                let end = Math.min(this.totalPages, this.currentPage + 2);
                for (let i = start; i <= end; i++) pages.push(i);
                return pages;
            },

            init() {
                this.$watch('search', () => { this.currentPage = 1; this.fetchData(); });
            },

            fetchData() {
                this.loading = true;

                const body = new URLSearchParams({
                    draw: 1,
                    start: this.currentStart,
                    length: this.perPage,
                    'search[value]': this.search,
                    status: this.statusFilter
                });

                fetch('{{ route("admin.mahasiswa.datatable") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: body
                })
                .then(r => r.json())
                .then(data => {
                    this.rows = data.data;
                    this.totalRecords = data.recordsTotal;
                    this.filteredRecords = data.recordsFiltered;
                    this.loading = false;
                });
            },

            prevPage() { if (this.currentPage > 1) { this.currentPage--; this.fetchData(); } },
            nextPage() { if (this.currentPage < this.totalPages) { this.currentPage++; this.fetchData(); } },
            goToPage(p) { this.currentPage = p; this.fetchData(); }
        }
    }
    </script>
</x-app-layout>
