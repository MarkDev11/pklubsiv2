<x-app-layout>
    <x-slot name="title">Pengaturan Profil</x-slot>
    
    <div class="space-y-6 sm:space-y-8 animate-fade-in pb-10 max-w-5xl mx-auto">
        
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-surface-700 text-gray-600 dark:text-gray-300 flex items-center justify-center shrink-0 border border-gray-200 dark:border-surface-600">
                        <i class="fa-solid fa-user-astronaut text-xl"></i>
                    </div>
                    Pengaturan Profil
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola data rahasia otentikasi login serta identitas pribadi Anda di satu panel kontrol.</p>
            </div>
            
    <div class="px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-surface-700 font-bold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-widest border border-gray-200 dark:border-surface-600 flex items-center gap-2 shadow-sm">
        <span class="w-2 h-2 rounded-full {{ auth()->user()->role->value === 'admin' ? 'bg-indigo-500' : (auth()->user()->role->value === 'dosen' ? 'bg-fuchsia-500' : 'bg-emerald-500') }} animate-pulse"></span>
        Sesi {{ auth()->user()->role->value }} Mengudara
    </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-900/20 border-l-4 border-emerald-500 rounded-r-xl p-4 shadow-sm flex items-center gap-3 mb-6">
                <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-check"></i>
                </div>
                <p class="text-emerald-700 dark:text-emerald-300 font-bold text-sm">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('status') === 'password-updated')
            <div class="bg-emerald-50 dark:bg-emerald-900/20 border-l-4 border-emerald-500 rounded-r-xl p-4 shadow-sm flex items-center gap-3 mb-6">
                <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-key"></i>
                </div>
                <p class="text-emerald-700 dark:text-emerald-300 font-bold text-sm">Kata sandi berhasil diperbarui permanen.</p>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
            
            {{-- Bagian Kiri: Kartu Identitas --}}
            <div class="md:col-span-1">
                <div class="bg-white dark:bg-surface-800 rounded-[2rem] p-6 shadow-sm border border-gray-100 dark:border-surface-700 relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 w-32 h-32 bg-gray-50 dark:bg-surface-700/50 rounded-full group-hover:scale-150 transition-transform duration-500 z-0"></div>
                    
                    <div class="relative z-10 flex flex-col items-center text-center">
                        <div class="w-24 h-24 rounded-3xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-surface-700 dark:to-surface-600 flex items-center justify-center text-3xl font-black text-gray-500 dark:text-gray-300 shadow-inner mb-4 border-2 border-white dark:border-surface-800 rotate-3 group-hover:rotate-0 transition-transform">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white">{{ $user->name }}</h3>
                        <p class="text-xs uppercase tracking-widest font-bold text-gray-400 mt-1 mb-5">{{ $user->role->value }} Sistem</p>
                        
                        <div class="w-full space-y-3 text-left">
                            <div class="bg-gray-50 dark:bg-surface-900 rounded-xl p-3 border border-gray-100 dark:border-surface-700 focus-within:ring-2 ring-indigo-500/20">
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold mb-1">Username / Induk</p>
                                <p class="font-mono text-sm text-gray-900 dark:text-white break-all">{{ $user->username }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-surface-900 rounded-xl p-3 border border-gray-100 dark:border-surface-700 focus-within:ring-2 ring-indigo-500/20">
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold mb-1">Email Aktif</p>
                                <p class="font-mono text-sm text-gray-900 dark:text-white break-all">{{ $user->email ?? '-' }}</p>
                            </div>
                            @if($user->jenis)
                            <div class="bg-gray-50 dark:bg-surface-900 rounded-xl p-3 border border-gray-100 dark:border-surface-700">
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold mb-1">Klasifikasi Mahasiswa</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $user->jenis }}</p>
                            </div>
                            @endif
                            @if($user->nama_dosen_pa)
                            <div class="bg-gray-50 dark:bg-surface-900 rounded-xl p-3 border border-gray-100 dark:border-surface-700">
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold mb-1">Dosen Penasihat</p>
                                <p class="text-sm font-bold text-indigo-600 dark:text-indigo-400"><i class="fa-solid fa-chalkboard-user mr-1"></i> {{ $user->nama_dosen_pa }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian Kanan: Ganti Password --}}
            <div class="md:col-span-2 space-y-6">
                
                {{-- Form Keamanan Sandi --}}
                <div class="bg-white dark:bg-surface-800 rounded-[2rem] p-6 lg:p-8 shadow-sm border border-gray-100 dark:border-surface-700">
                    <div class="flex items-center gap-3 mb-8 pb-5 border-b border-gray-100 dark:border-surface-700">
                        <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/20 rounded-2xl flex items-center justify-center text-amber-500 shadow-sm border border-amber-100 dark:border-amber-800/30">
                            <i class="fa-solid fa-shield-halved text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white">Otentikasi Sandi</h3>
                            <p class="text-sm text-gray-500">Pastikan akun menggunakan kata sandi acak yang kuat agar tetap aman.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Password Saat Ini</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-unlock-keyhole text-gray-400"></i>
                                    </div>
                                    <input type="password" name="password_lama" class="w-full pl-11 bg-gray-50 dark:bg-surface-900 border appearance-none border-gray-200 dark:border-surface-600 focus:border-amber-500 focus:ring-amber-500 text-gray-900 dark:text-white rounded-xl px-4 py-3 transition-colors" required placeholder="Ketik kata sandi lama untuk validasi">
                                </div>
                                @error('password_lama')
                                    <p class="text-red-500 text-xs font-bold mt-1.5 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Password Pengganti</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-key text-amber-500"></i>
                                    </div>
                                    <input type="password" name="password_baru" class="w-full pl-11 bg-gray-50 dark:bg-surface-900 border appearance-none border-gray-200 dark:border-surface-600 focus:border-amber-500 focus:ring-amber-500 text-gray-900 dark:text-white rounded-xl px-4 py-3 transition-colors" required placeholder="Sandi rahasia yang baru">
                                </div>
                                @error('password_baru')
                                    <p class="text-red-500 text-xs font-bold mt-1.5 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Konfirmasi Pengganti</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-check-double text-gray-400"></i>
                                    </div>
                                    <input type="password" name="password_baru_confirmation" class="w-full pl-11 bg-gray-50 dark:bg-surface-900 border appearance-none border-gray-200 dark:border-surface-600 focus:border-amber-500 focus:ring-amber-500 text-gray-900 dark:text-white rounded-xl px-4 py-3 transition-colors" required placeholder="Ketik ulang persis sama">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="btn-primary bg-amber-500 hover:bg-amber-400 text-white shadow-[0_0_15px_rgba(245,158,11,0.3)] hover:shadow-[0_0_25px_rgba(245,158,11,0.5)] border-none">
                                <i class="fa-solid fa-shield-cat mr-2"></i> Konfirmasi Perubahan Sandi
                            </button>
                        </div>
                    </form>
                </div>
                
                {{-- Log Out Banner - Extra --}}
                <div class="bg-rose-50 dark:bg-surface-800 rounded-3xl p-6 border border-rose-100 dark:border-rose-900/30 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">Tinggalkan Sesi</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Harap keluar dari sistem apabila Anda sedang meminjam gawai umum / warnet.</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="shrink-0 w-full sm:w-auto">
                        @csrf
                        <button type="submit" class="w-full btn-danger inline-flex justify-center shadow-sm">
                            <i class="fa-solid fa-power-off"></i> Logout
                        </button>
                    </form>
                </div>
                
            </div>
            
        </div>
    </div>
</x-app-layout>
