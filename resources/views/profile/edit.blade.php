<x-app-layout>
    <x-slot name="title">Pengaturan Profil</x-slot>
    
    <div class="space-y-6 pb-10 max-w-5xl mx-auto">
        
        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Pengaturan Profil</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Kelola informasi akun dan keamanan password Anda
            </p>
        </div>

        {{-- Success Messages --}}
        @if(session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg p-4 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>
                <p class="text-sm text-emerald-700 dark:text-emerald-300 font-medium">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('status') === 'password-updated')
            <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg p-4 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>
                <p class="text-sm text-emerald-700 dark:text-emerald-300 font-medium">Password berhasil diperbarui</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Profile Info Card --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <div class="flex flex-col items-center text-center mb-6">
                        <div class="w-20 h-20 rounded-lg bg-blue-600 flex items-center justify-center text-2xl font-bold text-white mb-3">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $user->name }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mt-1">{{ $user->role->value }}</p>
                    </div>
                    
                    <div class="space-y-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Username</p>
                            <p class="text-sm font-mono text-gray-900 dark:text-white">{{ $user->username }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Email</p>
                            <p class="text-sm font-mono text-gray-900 dark:text-white">{{ $user->email ?? '-' }}</p>
                        </div>
                        @if($user->jenis)
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Jenis Mahasiswa</p>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $user->jenis }}</p>
                        </div>
                        @endif
                        @if($user->nama_dosen_pa)
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Dosen PA</p>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $user->dosenPaLabel() }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Password Change Form --}}
            <div class="lg:col-span-2 space-y-6">
                
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ubah Password</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pastikan menggunakan password yang kuat untuk keamanan akun</p>
                    </div>

                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password Saat Ini</label>
                            <input type="password" name="password_lama" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" 
                                   required placeholder="Masukkan password lama">
                            @error('password_lama')
                                <p class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password Baru</label>
                            <input type="password" name="password_baru" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" 
                                   required placeholder="Masukkan password baru">
                            @error('password_baru')
                                <p class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Konfirmasi Password Baru</label>
                            <input type="password" name="password_baru_confirmation" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" 
                                   required placeholder="Ketik ulang password baru">
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                                <i class="fa-solid fa-key"></i>
                                <span>Update Password</span>
                            </button>
                        </div>
                    </form>
                </div>
                
                {{-- Logout Section --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Keluar dari Akun</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Logout dari sistem untuk mengakhiri sesi</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition-colors">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
                
            </div>
            
        </div>
    </div>
</x-app-layout>
