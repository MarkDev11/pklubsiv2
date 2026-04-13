<x-app-layout>
    <x-slot name="title">Import Data Akun</x-slot>
    <div class="space-y-6 animate-fade-in">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm mb-2">
            <a href="{{ route('admin.akun.index') }}" class="text-gray-400 dark:text-gray-500 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                <i class="fa-solid fa-users-gear mr-1"></i> Manajemen Akun
            </a>
            <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 dark:text-gray-600"></i>
            <span class="text-gray-700 dark:text-gray-300 font-medium">Import Data</span>
        </div>

        {{-- Header Banner --}}
        <div class="rounded-2xl border border-gray-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden shadow-sm">
            <div class="bg-gradient-to-r from-emerald-500 to-emerald-700 px-6 py-8 relative">
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center text-white text-2xl font-bold shadow-lg border border-white/10">
                        <i class="fa-solid fa-file-excel"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Import Data Akun </h3>
                        <p class="text-emerald-50 mt-1 text-sm">Tambahkan banyak akun mahasiswa, dosen, atau mentor sekaligus melalui file <strong class="text-white">Excel</strong> (.xlsx / .csv)</p>
                    </div>
                </div>
                {{-- Decorative --}}
                <div class="absolute -right-6 -top-6 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>
                <div class="absolute -left-4 -bottom-4 w-28 h-28 bg-white/5 rounded-full blur-xl"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            {{-- Left Column: Upload Area --}}
            <div class="space-y-6">
                {{-- Warning/Info Alert --}}
                <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-900/10 border border-blue-200/50 dark:border-blue-700/20 flex gap-4">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800/30 rounded-lg flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-circle-info text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-blue-800 dark:text-blue-300 text-sm">Sebelum Mengupload</h4>
                        <p class="text-sm text-blue-700 dark:text-blue-400/80 mt-1">
                            Pastikan data sesuai dengan format yang ditentukan. Username / NIM yang <strong>sudah ada</strong> (duplikat) di sistem akan di-skip otomatis.
                        </p>
                    </div>
                </div>

                {{-- Upload Card --}}
                <div class="rounded-2xl border border-gray-200 dark:border-surface-700 bg-white dark:bg-surface-800 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-surface-700 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 flex items-center justify-center bg-gray-50 dark:bg-surface-700 rounded-lg">
                                <i class="fa-solid fa-cloud-arrow-up text-gray-500 dark:text-gray-400"></i>
                            </div>
                            <h4 class="font-bold text-gray-900 dark:text-white">Pilih File</h4>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <form method="POST" action="{{ route('admin.import.store') }}" enctype="multipart/form-data">
                            @csrf
                            
                            {{-- Input File Custom Design --}}
                            <div class="w-full relative">
                                <input type="file" name="upload_excel" id="upload_excel" accept=".xlsx,.xls,.csv" required
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                    onchange="document.getElementById('file-name').textContent = this.files[0] ? this.files[0].name : 'Pilih atau drop file Excel...'">
                                
                                <div class="w-full p-8 border-2 border-dashed border-gray-300 dark:border-surface-600 rounded-xl hover:border-emerald-500 dark:hover:border-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-emerald-900/10 transition-all flex flex-col items-center justify-center text-center">
                                    <div class="w-16 h-16 mb-4 rounded-full bg-emerald-100 dark:bg-emerald-900/20 text-emerald-600 flex items-center justify-center text-2xl shadow-inner">
                                        <i class="fa-solid fa-file-arrow-up"></i>
                                    </div>
                                    <p id="file-name" class="font-bold text-gray-700 dark:text-gray-300">Pilih atau letakkan file di sini...</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Maksimal 5MB. Format yang didukung: .xlsx, .csv</p>
                                </div>
                            </div>
                            @error('upload_excel')
                                <p class="text-red-500 text-sm mt-3 flex items-center gap-1.5"><i class="fa-solid fa-triangle-exclamation text-xs"></i> {{ $message }}</p>
                            @enderror

                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="btn-primary w-full sm:w-auto h-11 px-8 py-2 rounded-xl text-sm justify-center group flex items-center gap-2">
                                    <i class="fa-solid fa-upload group-hover:-translate-y-1 transition-transform"></i>
                                    Proses Import Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Right Column: Template & Format Guide --}}
            <div class="space-y-6">
                
                {{-- Download Template Card --}}
                <div class="rounded-2xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50/30 dark:bg-emerald-900/10 shadow-sm overflow-hidden">
                    <div class="p-5 sm:p-6 flex flex-col sm:flex-row items-center gap-5 sm:justify-between text-center sm:text-left relative">
                        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-emerald-100 dark:bg-emerald-800/40 rounded-full blur-xl"></div>
                        <div class="relative z-10 max-w-[200px] sm:max-w-none">
                            <h4 class="font-bold text-emerald-900 dark:text-emerald-100 mb-1"><i class="fa-regular fa-file-excel mr-1.5"></i> Unduh File Template</h4>
                            <p class="text-xs text-emerald-700 dark:text-emerald-300 leading-relaxed">Gunakan format file ini agar data bisa terbaca dengan sempurna oleh sistem.</p>
                        </div>
                        <a href="{{ asset('templates/Format_Import_Akun.xlsx') }}" download class="btn-success relative z-10 whitespace-nowrap shadow-lg shadow-emerald-500/20 px-5 py-2.5 rounded-lg text-sm flex items-center gap-2 shrink-0">
                            <i class="fa-solid fa-download"></i> Format.xlsx
                        </a>
                    </div>
                </div>

                {{-- Table Guide Card --}}
                <div class="rounded-2xl border border-gray-200 dark:border-surface-700 bg-white dark:bg-surface-800 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-surface-700 flex items-center gap-2.5 bg-gray-50 dark:bg-surface-900">
                        <i class="fa-solid fa-table-columns text-gray-500 dark:text-gray-400 text-sm"></i>
                        <h4 class="font-bold text-gray-900 dark:text-white text-sm">Panduan Kolom Excel</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-surface-800 text-gray-500 dark:text-gray-400">
                                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider w-16">Kolom</th>
                                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider">Isi (Header)</th>
                                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider">Contoh Value</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-surface-700">
                                <tr class="hover:bg-gray-50 dark:hover:bg-surface-800/50">
                                    <td class="px-5 py-3"><span class="inline-flex items-center justify-center w-7 h-7 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-mono font-bold text-xs ring-1 ring-emerald-200 dark:ring-emerald-800">A</span></td>
                                    <td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-200">Nama Lengkap</td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs">Arfandi Santoso</td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-surface-800/50">
                                    <td class="px-5 py-3"><span class="inline-flex items-center justify-center w-7 h-7 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-mono font-bold text-xs ring-1 ring-emerald-200 dark:ring-emerald-800">B</span></td>
                                    <td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-200">NIM / NIP / Email</td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs">19210080</td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-surface-800/50">
                                    <td class="px-5 py-3"><span class="inline-flex items-center justify-center w-7 h-7 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-mono font-bold text-xs ring-1 ring-emerald-200 dark:ring-emerald-800">C</span></td>
                                    <td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-200">Password</td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">katasandi123</td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-surface-800/50">
                                    <td class="px-5 py-3"><span class="inline-flex items-center justify-center w-7 h-7 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-mono font-bold text-xs ring-1 ring-emerald-200 dark:ring-emerald-800">D</span></td>
                                    <td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-200">Role</td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs"><span class="px-1.5 py-0.5 rounded border">mahasiswa / dosen</span></td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-surface-800/50">
                                    <td class="px-5 py-3"><span class="inline-flex items-center justify-center w-7 h-7 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-mono font-bold text-xs ring-1 ring-emerald-200 dark:ring-emerald-800">E</span></td>
                                    <td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-200">Nama Dosen PA</td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">(Jika mahasiswa)</td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-surface-800/50">
                                    <td class="px-5 py-3"><span class="inline-flex items-center justify-center w-7 h-7 rounded bg-gray-100 dark:bg-surface-700 text-gray-600 dark:text-gray-300 font-mono font-bold text-xs ring-1 ring-gray-200 dark:ring-surface-600">F</span></td>
                                    <td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-200">Jenis Magang <span class="ml-1 text-[10px] text-gray-400">opsional</span></td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs text-nowrap"><span class="px-1.5 py-0.5 rounded border">Magang / PMK</span></td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-surface-800/50">
                                    <td class="px-5 py-3"><span class="inline-flex items-center justify-center w-7 h-7 rounded bg-gray-100 dark:bg-surface-700 text-gray-600 dark:text-gray-300 font-mono font-bold text-xs ring-1 ring-gray-200 dark:ring-surface-600">G</span></td>
                                    <td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-200">Kelas <span class="ml-1 text-[10px] text-gray-400">opsional</span></td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs font-mono">12.7A.01</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
