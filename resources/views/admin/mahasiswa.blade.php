<x-app-layout>
    <x-slot name="title">Status Mahasiswa</x-slot>

    <div class="space-y-6" x-data="mahasiswaManager()" x-init="fetchData()">
        
        {{-- Header & Stats Widgets --}}
        <div class="animate-fade-in">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Board Pemantauan Status PKL/MSIB</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Belum Input Widget --}}
                <div @click="statusFilter = (statusFilter === 'belum' ? '' : 'belum'); fetchData()"
                     :class="statusFilter === 'belum' ? 'ring-2 ring-red-500 scale-[1.02]' : 'hover:scale-[1.02]'"
                     class="card !p-5 relative overflow-hidden transition-all duration-300 cursor-pointer group">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-red-100 dark:bg-red-900/30 rounded-full blur-xl transition-all group-hover:bg-red-200 dark:group-hover:bg-red-900/50"></div>
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                            <i class="fa-solid fa-user-xmark"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Belum Input Proposal</p>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalBelum }} <span class="text-xs font-normal text-gray-400">Mhs</span></h3>
                        </div>
                    </div>
                </div>

                {{-- Sedang Proses Widget --}}
                <div @click="statusFilter = (statusFilter === 'proses' ? '' : 'proses'); fetchData()"
                     :class="statusFilter === 'proses' ? 'ring-2 ring-yellow-400 scale-[1.02]' : 'hover:scale-[1.02]'"
                     class="card !p-5 relative overflow-hidden transition-all duration-300 cursor-pointer group">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-yellow-100 dark:bg-yellow-900/30 rounded-full blur-xl transition-all group-hover:bg-yellow-200 dark:group-hover:bg-yellow-900/50"></div>
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/40 text-yellow-600 dark:text-yellow-400 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                            <i class="fa-solid fa-hourglass-half"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Sedang Proses</p>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalProses }} <span class="text-xs font-normal text-gray-400">Mhs</span></h3>
                        </div>
                    </div>
                </div>

                {{-- Komplit Widget --}}
                <div @click="statusFilter = (statusFilter === 'selesai' ? '' : 'selesai'); fetchData()"
                     :class="statusFilter === 'selesai' ? 'ring-2 ring-emerald-500 scale-[1.02]' : 'hover:scale-[1.02]'"
                     class="card !p-5 relative overflow-hidden transition-all duration-300 cursor-pointer group">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-100 dark:bg-emerald-900/30 rounded-full blur-xl transition-all group-hover:bg-emerald-200 dark:group-hover:bg-emerald-900/50"></div>
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Dokumen Komplit</p>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalSelesai }} <span class="text-xs font-normal text-gray-400">Mhs</span></h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 ml-1"><i class="fa-solid fa-circle-info mr-1"></i> Klik pada kartu di atas untuk memfilter tabel ke status tertentu.</p>
        </div>

        {{-- Table Section --}}
        <div class="card animate-fade-in" style="animation-delay:.1s">
            
            <div class="flex flex-col sm:flex-row justify-between gap-4 mb-4">
                {{-- Search --}}
                <div class="relative w-full sm:w-72 border border-gray-200 dark:border-surface-600 rounded-xl overflow-hidden focus-within:border-primary-500 transition-colors">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                    </div>
                    <input type="text" x-model.debounce.300ms="search" placeholder="Cari nama, NIM, tempat riset..."
                           class="w-full pl-9 pr-3 py-2 border-0 bg-transparent focus:ring-0 text-sm dark:text-white">
                </div>
                
                {{-- Length Dropdown --}}
                <div class="flex items-center gap-2 self-end sm:self-auto">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Show:</span>
                    <select x-model="perPage" @change="currentPage = 1; fetchData()" class="form-input !py-2 !text-sm border-gray-200 dark:border-surface-600 rounded-xl">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-container">
                <table class="w-full relative">
                    {{-- Loader Overlay --}}
                    <div x-show="loading" class="absolute inset-0 z-10 bg-white/60 dark:bg-surface-800/60 backdrop-blur-sm flex items-center justify-center rounded-xl">
                        <i class="fa-solid fa-circle-notch fa-spin text-3xl text-primary-600"></i>
                    </div>

                    <thead>
                        <tr>
                            <th class="w-12 text-center">No</th>
                            <th>NIM / Username</th>
                            <th>Nama Mahasiswa</th>
                            <th class="text-center">Jenis Magang</th>
                            <th>Tempat Riset</th>
                            <th>Nama Mentor</th>
                            <th>Status Dokumen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="r in rows" :key="r.nim">
                            <tr class="hover:bg-gray-50 dark:hover:bg-surface-800/80 transition-colors">
                                <td class="text-center font-medium text-gray-500" x-text="r.no"></td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-md bg-gray-100 dark:bg-surface-700 flex items-center justify-center text-xs border border-gray-200 dark:border-surface-600">
                                            <i class="fa-solid fa-id-card text-gray-400"></i>
                                        </div>
                                        <span class="font-mono font-semibold text-gray-900 dark:text-white" x-text="r.nim"></span>
                                    </div>
                                </td>
                                <td class="font-bold text-gray-800 dark:text-gray-200" x-text="r.nama"></td>
                                <td class="text-center" x-html="r.jenis"></td>
                                <td class="text-sm font-medium" x-text="r.tempat"></td>
                                <td class="text-sm text-gray-500" x-text="r.mentor"></td>
                                <td x-html="r.status"></td>
                            </tr>
                        </template>
                        <tr x-show="!loading && rows.length === 0">
                            <td colspan="7" class="py-12 text-center">
                                <div class="w-16 h-16 bg-gray-50 dark:bg-surface-700 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fa-solid fa-magnifying-glass text-xl text-gray-400"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada data ditemukan</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination Info --}}
            <div class="flex flex-col sm:flex-row justify-between items-center mt-5 gap-4">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    Menampilkan <strong x-text="rows.length > 0 ? currentStart + 1 : 0"></strong> hingga <strong x-text="currentStart + rows.length"></strong> dari <strong x-text="filteredRecords"></strong> entri
                </span>
                
                <div class="flex gap-1">
                    <button @click="prevPage()" :disabled="currentPage <= 1"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-xs font-semibold transition-all disabled:opacity-30 disabled:cursor-not-allowed bg-white dark:bg-surface-700 border border-gray-200 dark:border-surface-600 hover:bg-gray-100 dark:hover:bg-surface-600">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    </button>
                    <template x-for="p in pageNumbers" :key="p">
                        <button @click="goToPage(p)"
                                :class="p === currentPage ? 'bg-primary-600 text-white border-primary-600 shadow-sm' : 'bg-white dark:bg-surface-700 border-gray-200 dark:border-surface-600 hover:bg-gray-100 dark:hover:bg-surface-600'"
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-xs font-semibold transition-all border"
                                x-text="p"></button>
                    </template>
                    <button @click="nextPage()" :disabled="currentPage >= totalPages"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-xs font-semibold transition-all disabled:opacity-30 disabled:cursor-not-allowed bg-white dark:bg-surface-700 border border-gray-200 dark:border-surface-600 hover:bg-gray-100 dark:hover:bg-surface-600">
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
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
