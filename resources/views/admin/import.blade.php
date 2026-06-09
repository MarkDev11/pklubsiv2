<x-app-layout>
    <x-slot name="title">Import Data Akun</x-slot>
    
    <div class="space-y-6">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.akun.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                <i class="fa-solid fa-users-gear mr-1"></i> Manajemen Akun
            </a>
            <i class="fa-solid fa-chevron-right text-xs text-gray-300 dark:text-gray-600"></i>
            <span class="text-gray-900 dark:text-white font-medium">Import Data</span>
        </div>

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Import Data Akun</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Tambahkan banyak akun mahasiswa, dosen, atau mentor sekaligus melalui file Excel (.xlsx / .csv)
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            {{-- Left Column: Upload Area --}}
            <div class="space-y-6">
                
                {{-- Info Alert --}}
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/30 rounded-lg p-4 flex gap-3">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800/30 rounded-md flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-circle-info text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-blue-900 dark:text-blue-300 text-sm">Sebelum Mengupload</h4>
                        <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">
                            Pastikan data sesuai dengan format yang ditentukan. Username / NIM yang <strong>sudah ada</strong> (duplikat) di sistem akan di-skip otomatis.
                        </p>
                    </div>
                </div>

                {{-- Upload Card --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        <h4 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-cloud-arrow-up text-gray-500 dark:text-gray-400"></i>
                            Pilih File
                        </h4>
                    </div>
                    
                    <div class="p-6">
                        <form method="POST" action="{{ route('admin.import.store') }}" enctype="multipart/form-data">
                            @csrf
                            
                            {{-- File Input --}}
                            <div class="relative">
                                <input type="file" name="upload_excel" id="upload_excel" accept=".xlsx,.xls,.csv" required
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                    onchange="document.getElementById('file-name').textContent = this.files[0] ? this.files[0].name : 'Pilih atau drop file Excel...'">
                                
                                <div class="p-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg hover:border-emerald-500 dark:hover:border-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-emerald-900/10 transition-all">
                                    <div class="flex flex-col items-center text-center">
                                        <div class="w-16 h-16 mb-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl">
                                            <i class="fa-solid fa-file-arrow-up"></i>
                                        </div>
                                        <p id="file-name" class="font-semibold text-gray-700 dark:text-gray-300">Pilih atau drop file di sini...</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Maksimal 5MB. Format: .xlsx, .csv</p>
                                    </div>
                                </div>
                            </div>
                            
                            @error('upload_excel')
                                <p class="text-red-600 dark:text-red-400 text-sm mt-3 flex items-center gap-1.5">
                                    <i class="fa-solid fa-triangle-exclamation text-xs"></i> {{ $message }}
                                </p>
                            @enderror

                            <div class="mt-6">
                                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                                    <i class="fa-solid fa-upload"></i>
                                    <span>Proses Import Data</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Right Column: Template & Guide --}}
            <div class="space-y-6">
                
                {{-- Download Template --}}
                <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/30 rounded-lg p-6">
                    <div class="flex flex-col sm:flex-row items-center gap-4 sm:justify-between text-center sm:text-left">
                        <div>
                            <h4 class="font-semibold text-emerald-900 dark:text-emerald-100 flex items-center gap-2 justify-center sm:justify-start">
                                <i class="fa-regular fa-file-excel"></i>
                                Unduh File Template
                            </h4>
                            <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-1">
                                Gunakan format file ini agar data bisa terbaca dengan sempurna oleh sistem.
                            </p>
                        </div>
                        <a href="{{ asset('templates/Format_Import_Akun.xlsx') }}" download 
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-md transition-colors whitespace-nowrap">
                            <i class="fa-solid fa-download"></i>
                            <span>Format.xlsx</span>
                        </a>
                    </div>
                </div>

                {{-- Column Guide --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        <h4 class="font-semibold text-gray-900 dark:text-white text-sm flex items-center gap-2">
                            <i class="fa-solid fa-table-columns text-gray-500 dark:text-gray-400"></i>
                            Panduan Kolom Excel
                        </h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider w-16">Kolom</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Header</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Contoh</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 font-mono font-semibold text-xs border border-emerald-200 dark:border-emerald-800/30">A</span>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">Nama Lengkap</td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs">Arfandi Santoso</td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 font-mono font-semibold text-xs border border-emerald-200 dark:border-emerald-800/30">B</span>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">NIM / NIP / Email</td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs">19210080</td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 font-mono font-semibold text-xs border border-emerald-200 dark:border-emerald-800/30">C</span>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">Password</td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">katasandi123</td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 font-mono font-semibold text-xs border border-emerald-200 dark:border-emerald-800/30">D</span>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">Role</td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">
                                        <span class="px-2 py-0.5 rounded border border-gray-300 dark:border-gray-600">mahasiswa / dosen</span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 font-mono font-semibold text-xs border border-emerald-200 dark:border-emerald-800/30">E</span>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">NIP Dosen PA</td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">Username dosen, wajib jika mahasiswa</td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-mono font-semibold text-xs border border-gray-200 dark:border-gray-600">F</span>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">
                                        Jenis Magang <span class="ml-1 text-xs text-gray-400">(opsional)</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">
                                        <span class="px-2 py-0.5 rounded border border-gray-300 dark:border-gray-600">Magang / PMK</span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-mono font-semibold text-xs border border-gray-200 dark:border-gray-600">G</span>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">
                                        Kelas <span class="ml-1 text-xs text-gray-400">(opsional)</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs">12.7A.01</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
