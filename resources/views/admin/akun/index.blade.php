<x-app-layout>
    <x-slot name="title">Daftar Akun</x-slot>

    <div class="space-y-6" x-data="akunManager()" x-init="fetchData()">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Manajemen Akun</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Total: <span class="font-semibold text-blue-600 dark:text-blue-400 tabular-nums" x-text="totalRecords"></span> akun terdaftar
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.akun.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-md transition-colors">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Tambah Manual</span>
                </a>
                <a href="{{ route('admin.import.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                    <i class="fa-solid fa-file-import text-xs"></i>
                    <span>Import Data</span>
                </a>
            </div>
        </div>

        {{-- Search & Filter --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <div class="flex flex-col sm:flex-row gap-3">
                {{-- Search --}}
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                    </div>
                    <input type="text" x-model.debounce.300ms="search" placeholder="Cari nama, NIM, NIP, atau email..."
                           class="w-full pl-10 pr-4 py-2 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                {{-- Role Filter Chips --}}
                <div class="flex gap-2 flex-wrap">
                    <template x-for="r in ['semua', 'mahasiswa', 'dosen', 'mentor', 'admin']" :key="r">
                        <button @click="roleFilter = r === 'semua' ? '' : r; fetchData()"
                                :class="(roleFilter === '' && r === 'semua') || roleFilter === r
                                    ? 'bg-blue-600 text-white border-blue-600'
                                    : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600'"
                                class="px-3 py-1.5 border rounded-md text-xs font-medium uppercase tracking-wide transition-colors whitespace-nowrap">
                            <span x-text="r.charAt(0).toUpperCase() + r.slice(1)"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">

            {{-- Loading --}}
            <div x-show="loading" class="flex items-center justify-center py-20">
                <div class="flex flex-col items-center gap-3">
                    <svg class="w-8 h-8 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
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
                            <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider w-16">#</th>
                                <th @click="toggleSort('username')" class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors select-none">
                                    <span class="flex items-center gap-2">
                                        Username/NIM 
                                        <i class="fa-solid fa-sort text-xs text-gray-300" x-show="sortCol !== 'username'"></i>
                                        <span x-show="sortCol === 'username'" x-text="sortDir === 'asc' ? '↑' : '↓'" class="text-blue-600 dark:text-blue-400"></span>
                                    </span>
                                </th>
                                <th @click="toggleSort('name')" class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors select-none">
                                    <span class="flex items-center gap-2">
                                        Nama 
                                        <i class="fa-solid fa-sort text-xs text-gray-300" x-show="sortCol !== 'name'"></i>
                                        <span x-show="sortCol === 'name'" x-text="sortDir === 'asc' ? '↑' : '↓'" class="text-blue-600 dark:text-blue-400"></span>
                                    </span>
                                </th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Role</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Info</th>
                                <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <template x-if="rows.length === 0">
                                <tr>
                                    <td colspan="6" class="text-center py-12">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                                <i class="fa-solid fa-magnifying-glass text-lg text-gray-400"></i>
                                            </div>
                                            <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada akun ditemukan</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500" x-show="search">Coba kata kunci lain</p>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-for="(row, index) in rows" :key="row.username">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-4 py-3 text-gray-400 font-medium text-xs tabular-nums" x-text="currentStart + index + 1"></td>
                                    <td class="px-4 py-3">
                                        <span class="font-semibold text-gray-900 dark:text-white" x-text="row.username"></span>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200" x-text="row.name"></td>
                                    <td class="px-4 py-3">
                                        <span :class="{
                                            'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-800/30': row.role === 'admin',
                                            'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800/30': row.role === 'dosen',
                                            'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800/30': row.role === 'mahasiswa',
                                            'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 border-purple-200 dark:border-purple-800/30': row.role === 'mentor'
                                        }" class="inline-flex items-center gap-1.5 px-2 py-1 rounded border text-xs font-medium uppercase">
                                            <i :class="{
                                                'fa-solid fa-shield-halved': row.role === 'admin',
                                                'fa-solid fa-chalkboard-user': row.role === 'dosen',
                                                'fa-solid fa-graduation-cap': row.role === 'mahasiswa',
                                                'fa-solid fa-building': row.role === 'mentor'
                                            }" class="text-xs"></i>
                                            <span x-text="row.role"></span>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <template x-if="row.jenis">
                                            <span class="text-xs text-gray-500 dark:text-gray-400" x-text="row.jenis"></span>
                                        </template>
                                        <template x-if="row.dosen_pa">
                                            <span class="text-xs text-gray-400 block mt-0.5">
                                                <i class="fa-solid fa-user-tie text-xs mr-1"></i>
                                                <span x-text="row.dosen_pa"></span>
                                            </span>
                                        </template>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-2">
                                            <a :href="'/admin/akun/' + row.encrypted_id + '/edit'"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-medium transition-colors">
                                                <i class="fa-solid fa-pen text-xs"></i>
                                                <span>Edit</span>
                                            </a>
                                            <template x-if="!row.is_admin">
                                                <button @click="confirmDelete(row)"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-medium transition-colors">
                                                    <i class="fa-solid fa-trash text-xs"></i>
                                                    <span>Hapus</span>
                                                </button>
                                            </template>
                                            <a :href="'/admin/akun/' + row.encrypted_id + '/log'"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white rounded text-xs font-medium transition-colors">
                                                <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                                                <span>Log</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                <div class="flex items-center justify-between px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    <div class="flex items-center gap-3">
                        <select x-model="perPage" @change="fetchData()" class="px-3 py-1.5 text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            Menampilkan <strong class="text-gray-900 dark:text-white tabular-nums" x-text="currentStart + 1"></strong>–<strong class="text-gray-900 dark:text-white tabular-nums" x-text="Math.min(currentStart + parseInt(perPage), filteredRecords)"></strong> dari <strong class="text-gray-900 dark:text-white tabular-nums" x-text="filteredRecords"></strong>
                        </span>
                    </div>
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

        {{-- Delete Modal --}}
        <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div x-show="showDeleteModal" 
                 x-transition:enter="ease-out duration-200" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0"
                 @click="showDeleteModal = false" 
                 class="fixed inset-0 bg-black/50"></div>
            <div x-show="showDeleteModal" 
                 x-transition:enter="ease-out duration-200" 
                 x-transition:enter-start="opacity-0 scale-95" 
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-150" 
                 x-transition:leave-start="opacity-100 scale-100" 
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-sm w-full p-6 z-10 border border-gray-200 dark:border-gray-700">
                <div class="flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-red-50 dark:bg-red-900/20 rounded-lg flex items-center justify-center mb-4">
                        <i class="fa-solid fa-triangle-exclamation text-xl text-red-600 dark:text-red-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Hapus Akun?</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                        Akun <strong x-text="deleteTarget?.name"></strong> (<span x-text="deleteTarget?.username" class="font-mono"></span>) akan dihapus permanen.
                    </p>
                    <div class="flex gap-3 mt-6 w-full">
                        <button @click="showDeleteModal = false" 
                                class="flex-1 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            Batal
                        </button>
                        <form :action="'/admin/akun/' + deleteTarget?.encrypted_id" method="POST" class="flex-1">
                            @csrf @method('DELETE')
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm font-medium transition-colors">
                                Ya, Hapus
                            </button>
                        </form>
                    </div>
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
