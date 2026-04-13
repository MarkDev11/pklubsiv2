<x-app-layout>
    <x-slot name="title">Daftar Akun</x-slot>

    <div class="space-y-5" x-data="akunManager()" x-init="fetchData()">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <i class="fa-solid fa-users-gear text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Manajemen Akun</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total: <span class="font-semibold text-primary-600 dark:text-primary-400" x-text="totalRecords"></span> akun terdaftar</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.akun.create') }}" class="btn-success">
                    <i class="fa-solid fa-plus text-sm"></i>
                    Tambah Manual
                </a>
                <a href="{{ route('admin.import.index') }}" class="btn-primary">
                    <i class="fa-solid fa-file-import text-sm"></i>
                    Import Data
                </a>
            </div>
        </div>

        {{-- Search & Filter --}}
        <div class="rounded-2xl border border-gray-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4 animate-fade-in shadow-sm" style="animation-delay:.05s">
            <div class="flex flex-col sm:flex-row gap-3">
                {{-- Search --}}
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                    </div>
                    <input type="text" x-model.debounce.300ms="search" placeholder="Cari nama, NIM, NIP, atau email..."
                           class="form-input !pl-11 !py-2.5 !rounded-xl bg-gray-50 dark:bg-surface-900 border-gray-200 dark:border-surface-600">
                </div>

                {{-- Role Filter Chips --}}
                <div class="flex gap-1.5 flex-wrap">
                    <template x-for="r in ['semua', 'mahasiswa', 'dosen', 'mentor', 'admin']" :key="r">
                        <button @click="roleFilter = r === 'semua' ? '' : r; fetchData()"
                                :class="(roleFilter === '' && r === 'semua') || roleFilter === r
                                    ? 'bg-primary-600 text-white shadow-md shadow-primary-500/20'
                                    : 'bg-gray-100 dark:bg-surface-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-surface-600'"
                                class="px-3.5 py-2 rounded-lg text-xs font-bold uppercase tracking-wide transition-all duration-200 whitespace-nowrap">
                            <span x-text="r.charAt(0).toUpperCase() + r.slice(1)"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl border border-gray-200 dark:border-surface-700 overflow-hidden shadow-sm animate-fade-in" style="animation-delay:.1s">

            {{-- Loading --}}
            <div x-show="loading" class="flex items-center justify-center py-20 bg-white dark:bg-surface-800">
                <div class="flex flex-col items-center gap-3">
                    <svg class="w-8 h-8 text-primary-500 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Memuat data...</p>
                </div>
            </div>

            {{-- Data --}}
            <div x-show="!loading" x-cloak>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gradient-to-r from-slate-50 to-gray-50 dark:from-surface-800 dark:to-surface-900 border-b-2 border-gray-200 dark:border-surface-600">
                                <th class="px-5 py-3.5 text-left font-bold text-gray-500 dark:text-gray-400 uppercase text-[11px] tracking-wider w-14">#</th>
                                <th @click="toggleSort('username')" class="px-5 py-3.5 text-left font-bold text-gray-500 dark:text-gray-400 uppercase text-[11px] tracking-wider cursor-pointer hover:text-primary-600 dark:hover:text-primary-400 transition-colors select-none">
                                    <span class="flex items-center gap-1.5">Username/NIM <i class="fa-solid fa-sort text-[10px] text-gray-300" x-show="sortCol !== 'username'"></i><span x-show="sortCol === 'username'" x-text="sortDir === 'asc' ? '↑' : '↓'" class="text-primary-500 font-mono"></span></span>
                                </th>
                                <th @click="toggleSort('name')" class="px-5 py-3.5 text-left font-bold text-gray-500 dark:text-gray-400 uppercase text-[11px] tracking-wider cursor-pointer hover:text-primary-600 dark:hover:text-primary-400 transition-colors select-none">
                                    <span class="flex items-center gap-1.5">Nama <i class="fa-solid fa-sort text-[10px] text-gray-300" x-show="sortCol !== 'name'"></i><span x-show="sortCol === 'name'" x-text="sortDir === 'asc' ? '↑' : '↓'" class="text-primary-500 font-mono"></span></span>
                                </th>
                                <th class="px-5 py-3.5 text-left font-bold text-gray-500 dark:text-gray-400 uppercase text-[11px] tracking-wider">Role</th>
                                <th class="px-5 py-3.5 text-left font-bold text-gray-500 dark:text-gray-400 uppercase text-[11px] tracking-wider">Info</th>
                                <th class="px-5 py-3.5 text-center font-bold text-gray-500 dark:text-gray-400 uppercase text-[11px] tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-if="rows.length === 0">
                                <tr>
                                    <td colspan="6" class="text-center py-16 bg-white dark:bg-surface-800">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-16 h-16 bg-gray-100 dark:bg-surface-700 rounded-2xl flex items-center justify-center mx-auto">
                                                <i class="fa-solid fa-magnifying-glass text-xl text-gray-400"></i>
                                            </div>
                                            <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada akun ditemukan</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500" x-show="search">Coba kata kunci lain</p>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-for="(row, index) in rows" :key="row.username">
                                <tr class="border-b border-gray-100 dark:border-surface-700/50 transition-colors duration-150 hover:bg-blue-50/50 dark:hover:bg-primary-900/10"
                                    :class="index % 2 === 0 ? 'bg-white dark:bg-surface-800' : 'bg-gray-50/70 dark:bg-surface-800/50'">
                                    <td class="px-5 py-3 text-gray-400 font-medium text-xs" x-text="currentStart + index + 1"></td>
                                    <td class="px-5 py-3">
                                        <span class="font-semibold text-gray-900 dark:text-white text-sm" x-text="row.username"></span>
                                    </td>
                                    <td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-200" x-text="row.name"></td>
                                    <td class="px-5 py-3">
                                        <span :class="{
                                            'bg-sky-100 dark:bg-sky-900/20 text-sky-700 dark:text-sky-400 border-sky-200 dark:border-sky-700/30': row.role === 'admin',
                                            'bg-emerald-100 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-700/30': row.role === 'dosen',
                                            'bg-amber-100 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-700/30': row.role === 'mahasiswa',
                                            'bg-violet-100 dark:bg-violet-900/20 text-violet-700 dark:text-violet-400 border-violet-200 dark:border-violet-700/30': row.role === 'mentor'
                                        }" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wide uppercase border">
                                            <i :class="{
                                                'fa-solid fa-shield-halved': row.role === 'admin',
                                                'fa-solid fa-chalkboard-user': row.role === 'dosen',
                                                'fa-solid fa-graduation-cap': row.role === 'mahasiswa',
                                                'fa-solid fa-building': row.role === 'mentor'
                                            }" class="text-[10px]"></i>
                                            <span x-text="row.role"></span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <template x-if="row.jenis">
                                            <span class="text-xs text-gray-500 dark:text-gray-400" x-text="row.jenis"></span>
                                        </template>
                                        <template x-if="row.dosen_pa">
                                            <span class="text-[11px] text-gray-400 block mt-0.5"><i class="fa-solid fa-user-tie text-[10px] mr-1"></i><span x-text="row.dosen_pa"></span></span>
                                        </template>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a :href="'/admin/akun/' + row.encrypted_id + '/edit'"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 hover:bg-primary-100 dark:hover:bg-primary-900/30 transition-colors border border-primary-200/50 dark:border-primary-700/30">
                                                <i class="fa-solid fa-pen-to-square text-[11px]"></i> Edit
                                            </a>
                                            <template x-if="!row.is_admin">
                                                <button @click="confirmDelete(row)"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors border border-red-200/50 dark:border-red-700/30">
                                                    <i class="fa-solid fa-trash text-[11px]"></i> Hapus
                                                </button>
                                            </template>
                                            <a :href="'/admin/akun/' + row.encrypted_id + '/log'"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-50 dark:bg-surface-700 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-600 transition-colors border border-gray-200/50 dark:border-surface-600">
                                                <i class="fa-solid fa-clock-rotate-left text-[11px]"></i> Log
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                <div class="flex items-center justify-between px-5 py-3.5 border-t-2 border-gray-200 dark:border-surface-600 bg-gray-50 dark:bg-surface-900">
                    <div class="flex items-center gap-3">
                        <select x-model="perPage" @change="fetchData()" class="form-select !w-auto !py-1.5 !pl-3 !pr-8 !rounded-lg text-xs !border-gray-200 dark:!border-surface-600 bg-white dark:bg-surface-800">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            Menampilkan <strong x-text="currentStart + 1"></strong>–<strong x-text="Math.min(currentStart + parseInt(perPage), filteredRecords)"></strong> dari <strong x-text="filteredRecords"></strong>
                        </span>
                    </div>
                    <div class="flex gap-1">
                        <button @click="prevPage()" :disabled="currentPage <= 1"
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-xs font-semibold transition-all disabled:opacity-30 disabled:cursor-not-allowed bg-white dark:bg-surface-700 border border-gray-200 dark:border-surface-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-600">
                            <i class="fa-solid fa-chevron-left text-[10px]"></i>
                        </button>
                        <template x-for="p in pageNumbers" :key="p">
                            <button @click="goToPage(p)"
                                    :class="p === currentPage ? 'bg-primary-600 text-white border-primary-600 shadow-md shadow-primary-500/20' : 'bg-white dark:bg-surface-700 border-gray-200 dark:border-surface-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-600'"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg text-xs font-semibold transition-all border"
                                    x-text="p"></button>
                        </template>
                        <button @click="nextPage()" :disabled="currentPage >= totalPages"
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-xs font-semibold transition-all disabled:opacity-30 disabled:cursor-not-allowed bg-white dark:bg-surface-700 border border-gray-200 dark:border-surface-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-600">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Delete Modal --}}
        <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="showDeleteModal = false" class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>
            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative bg-white dark:bg-surface-800 rounded-2xl shadow-2xl max-w-sm w-full p-6 z-10 border border-gray-100 dark:border-surface-700 text-center">
                <div class="w-16 h-16 bg-red-50 dark:bg-red-900/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-triangle-exclamation text-2xl text-red-500"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Hapus Akun?</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Akun <strong x-text="deleteTarget?.name"></strong> (<span x-text="deleteTarget?.username" class="font-mono"></span>) akan dihapus permanen.</p>
                <div class="flex gap-3 mt-6 justify-center">
                    <button @click="showDeleteModal = false" class="btn-secondary">Batal</button>
                    <form :action="'/admin/akun/' + deleteTarget?.encrypted_id" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-danger">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    function akunManager() {
        return {
            rows: [],
            search: '',
            roleFilter: '',
            loading: true,
            totalRecords: 0,
            filteredRecords: 0,
            perPage: 25,
            currentPage: 1,
            sortCol: 'username',
            sortDir: 'asc',
            showDeleteModal: false,
            deleteTarget: null,

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

                const colIndex = { username: 1, name: 2, role: 3 };
                const body = new URLSearchParams({
                    draw: 1,
                    start: this.currentStart,
                    length: this.perPage,
                    'search[value]': this.roleFilter ? (this.search ? this.search + ' ' + this.roleFilter : this.roleFilter) : this.search,
                    'order[0][column]': colIndex[this.sortCol] || 1,
                    'order[0][dir]': this.sortDir,
                });

                fetch('{{ route("admin.akun.datatable") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: body
                })
                .then(r => r.json())
                .then(data => {
                    this.rows = data.data.map(d => ({
                        ...d,
                        jenis: d.jenis || '',
                        dosen_pa: d.dosen_pa || ''
                    }));
                    this.totalRecords = data.recordsTotal;
                    this.filteredRecords = data.recordsFiltered;
                    this.loading = false;
                });
            },

            toggleSort(col) {
                if (this.sortCol === col) {
                    this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortCol = col;
                    this.sortDir = 'asc';
                }
                this.fetchData();
            },

            prevPage() { if (this.currentPage > 1) { this.currentPage--; this.fetchData(); } },
            nextPage() { if (this.currentPage < this.totalPages) { this.currentPage++; this.fetchData(); } },
            goToPage(p) { this.currentPage = p; this.fetchData(); },

            confirmDelete(row) {
                this.deleteTarget = row;
                this.showDeleteModal = true;
            }
        };
    }
    </script>
</x-app-layout>
