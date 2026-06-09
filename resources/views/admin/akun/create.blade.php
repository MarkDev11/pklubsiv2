<x-app-layout>
    <x-slot name="title">Tambah Akun</x-slot>

    <div class="space-y-6 max-w-4xl mx-auto animate-fade-in" x-data="{ role: '{{ old('role', 'mahasiswa') }}' }">
        
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.akun.index') }}" class="text-gray-400 dark:text-gray-500 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                <i class="fa-solid fa-users-gear mr-1"></i> Manajemen Akun
            </a>
            <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 dark:text-gray-600"></i>
            <span class="text-gray-700 dark:text-gray-300 font-medium">Tambah Manual</span>
        </div>

        {{-- Header Card --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden shadow-sm">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-6 relative overflow-hidden">
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center text-white text-xl shadow-lg border border-white/10">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Tambah Akun Baru</h2>
                        <p class="text-blue-100 text-sm mt-0.5">Daftarkan akun satuan tanpa menggunakan file import Excel</p>
                    </div>
                </div>
                {{-- Decorative --}}
                <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute right-20 -bottom-8 w-28 h-28 bg-blue-400/20 rounded-full blur-xl"></div>
            </div>

            <div class="p-6 sm:p-8">
                <form method="POST" action="{{ route('admin.akun.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nama Lengkap --}}
                        <div>
                            <label class="form-label text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 block">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="form-input w-full bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-700 rounded-xl"
                                   placeholder="Contoh: Budi Santoso">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Username / NIM --}}
                        <div>
                            <label class="form-label text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 block">
                                Username / NIM / NIP <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="username" value="{{ old('username') }}" required
                                   class="form-input w-full font-mono bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-700 rounded-xl"
                                   placeholder="Contoh: 12345678">
                            @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Role --}}
                        <div>
                            <label class="form-label text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 block">
                                Role Akses <span class="text-red-500">*</span>
                            </label>
                            <select name="role" x-model="role" required class="form-input w-full bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-700 rounded-xl">
                                <option value="mahasiswa">Mahasiswa</option>
                                <option value="dosen">Dosen</option>
                                <option value="mentor">Mentor</option>
                                <option value="admin">Admin</option>
                            </select>
                            @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label class="form-label text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 block">
                                Password <span class="text-gray-400 font-normal text-xs ml-1">(opsional)</span>
                            </label>
                            <input type="password" name="password"
                                   class="form-input w-full bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-700 rounded-xl"
                                   placeholder="Biarkan kosong untuk password default">
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Kosongi bidang ini untuk menggunakan password bawaan: <strong class="font-mono text-gray-700 dark:text-gray-300">bs10k3PKL</strong></p>
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Form Tambahan Khusus Mahasiswa --}}
                    <div x-show="role === 'mahasiswa'" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-2"
                         class="p-5 rounded-xl border border-indigo-100 dark:border-indigo-900/30 bg-indigo-50/50 dark:bg-indigo-900/10 grid grid-cols-1 md:grid-cols-3 gap-5" style="display: none;">
                        
                        <div class="md:col-span-3 pb-2 border-b border-indigo-100 dark:border-indigo-900/30">
                            <h4 class="text-sm font-bold text-indigo-800 dark:text-indigo-300"><i class="fa-solid fa-graduation-cap mr-1"></i> Data Spesifik Mahasiswa</h4>
                        </div>

                        {{-- Kelas --}}
                        <div>
                            <label class="form-label text-xs font-medium text-gray-700 dark:text-gray-300 mb-1 block">Kelas</label>
                            <input type="text" name="kd_lokal" value="{{ old('kd_lokal') }}"
                                   class="form-input w-full bg-white dark:bg-gray-800 border-indigo-200 dark:border-gray-600 rounded-lg text-sm"
                                   placeholder="Contoh: 12.7A.01">
                        </div>

                        {{-- Jenis Magang --}}
                        <div>
                            <label class="form-label text-xs font-medium text-gray-700 dark:text-gray-300 mb-1 block">Jenis Magang</label>
                            <select name="jenis" class="form-input w-full bg-white dark:bg-gray-800 border-indigo-200 dark:border-gray-600 rounded-lg text-sm">
                                <option value="">-- Pilih Jenis --</option>
                                <option value="Magang" {{ old('jenis') == 'Magang' ? 'selected' : '' }}>Magang</option>
                                <option value="PMK" {{ old('jenis') == 'PMK' ? 'selected' : '' }}>PMK</option>
                            </select>
                        </div>

                        {{-- Dosen PA --}}
                        <div>
                            <label class="form-label text-xs font-medium text-gray-700 dark:text-gray-300 mb-1 block">Nama Dosen PA</label>
                            <input type="text" name="nama_dosen_pa" value="{{ old('nama_dosen_pa') }}"
                                   class="form-input w-full bg-white dark:bg-gray-800 border-indigo-200 dark:border-gray-600 rounded-lg text-sm"
                                   placeholder="Nama Dosen">
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-700 mt-6">
                        <a href="{{ route('admin.akun.index') }}" class="btn text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl px-5 py-2.5 font-medium text-sm transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="btn-primary rounded-xl px-6 py-2.5 shadow-lg shadow-blue-500/20 flex items-center gap-2 text-sm font-bold">
                            <i class="fa-solid fa-save"></i>
                            Simpan Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
