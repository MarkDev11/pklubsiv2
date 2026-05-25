<x-app-layout>
    <x-slot name="title">Edit Akun</x-slot>
    <div class="space-y-6 animate-fade-in">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.akun.index') }}" class="text-gray-400 dark:text-gray-500 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                <i class="fa-solid fa-users-gear mr-1"></i> Manajemen Akun
            </a>
            <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 dark:text-gray-600"></i>
            <span class="text-gray-700 dark:text-gray-300 font-medium">Edit Akun</span>
        </div>

        {{-- Profile Header Card --}}
        <div class="rounded-2xl border border-gray-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden shadow-sm">
            <div class="bg-gradient-to-r from-primary-500 to-primary-700 px-6 py-8 relative">
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center text-white text-xl font-bold shadow-lg border border-white/10">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">{{ $user->name }}</h3>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-blue-100/80 text-sm">{{ $user->username }}</span>
                            <span class="w-1 h-1 rounded-full bg-white/30"></span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white/15 text-white text-xs font-semibold uppercase tracking-wide">
                        <i class="{{ $user->role->value === 'admin' ? 'fa-solid fa-shield-halved' : ($user->role->value === 'dosen' ? 'fa-solid fa-chalkboard-user' : ($user->role->value === 'mentor' ? 'fa-solid fa-building' : 'fa-solid fa-graduation-cap')) }} text-[10px]"></i>
                        {{ $user->role->value }}
                    </span>
                        </div>
                    </div>
                </div>
                {{-- Decorative --}}
                <div class="absolute -right-6 -top-6 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>
                <div class="absolute -left-4 -bottom-4 w-28 h-28 bg-white/5 rounded-full blur-xl"></div>
            </div>
        </div>

        {{-- Edit Form --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Main Form --}}
            <div class="lg:col-span-2">
                <div class="rounded-2xl border border-gray-200 dark:border-surface-700 bg-white dark:bg-surface-800 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-surface-700">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-user-pen text-primary-600 dark:text-primary-400 text-sm"></i>
                            </div>
                            <h4 class="font-bold text-gray-900 dark:text-white">Informasi Akun</h4>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.akun.update', encryptUrl($user->username)) }}">
                        @csrf @method('PUT')
                        <div class="p-6 space-y-5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="form-label"><i class="fa-solid fa-user text-[11px] mr-1.5 text-gray-400"></i>Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
                                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="form-label"><i class="fa-solid fa-id-card text-[11px] mr-1.5 text-gray-400"></i>Username (NIM/NIP)</label>
                                    <input type="text" name="username" value="{{ old('username', $user->username) }}" class="form-input" required>
                                    @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="form-label"><i class="fa-solid fa-user-tag text-[11px] mr-1.5 text-gray-400"></i>Role</label>
                                    <select name="role" class="form-select" required>
                                        @foreach(['mahasiswa','dosen','mentor','admin'] as $r)
                                            <option value="{{ $r }}" {{ $user->role->value === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label"><i class="fa-solid fa-briefcase text-[11px] mr-1.5 text-gray-400"></i>Jenis PKL</label>
                                    <select name="jenis" class="form-select">
                                        <option value="">— Pilih —</option>
                                        <option value="Magang" {{ $user->jenis === 'Magang' ? 'selected' : '' }}>Magang</option>
                                        <option value="Program Magang khusus (PMK/GNIK/MBKM/MSIB/PMMB)" {{ $user->jenis !== 'Magang' && $user->jenis ? 'selected' : '' }}>PMK/GNIK/MBKM/MSIB/PMMB</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="form-label"><i class="fa-solid fa-chalkboard-user text-[11px] mr-1.5 text-gray-400"></i>Nama Dosen PA</label>
                                <input type="text" name="nama_dosen_pa" value="{{ old('nama_dosen_pa', $user->nama_dosen_pa) }}" class="form-input" placeholder="Kosongkan jika bukan mahasiswa">
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-100 dark:border-surface-700 bg-gray-50/50 dark:bg-surface-900/50 flex items-center justify-between rounded-b-2xl">
                            <a href="{{ route('admin.akun.index') }}" class="btn-secondary">
                                <i class="fa-solid fa-arrow-left text-sm"></i> Kembali
                            </a>
                            <button type="submit" class="btn-primary">
                                <i class="fa-solid fa-check text-sm"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Reset Password Card --}}
                <div class="rounded-2xl border border-gray-200 dark:border-surface-700 bg-white dark:bg-surface-800 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-surface-700">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-key text-red-500 dark:text-red-400 text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">Reset Password</h4>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500">Ganti password akun ini</p>
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.akun.reset-password', encryptUrl($user->username)) }}" class="p-6">
                        @csrf
                        <div class="space-y-3">
                            <input type="password" name="password" placeholder="Password baru (min 8 karakter)" class="form-input" required minlength="8">
                            <input type="password" name="password_confirmation" placeholder="Konfirmasi password" class="form-input" required minlength="8">
                            <button type="submit" class="btn-danger w-full justify-center">
                                <i class="fa-solid fa-rotate text-sm"></i> Reset Password
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Quick Info Card --}}
                <div class="rounded-2xl border border-gray-200 dark:border-surface-700 bg-white dark:bg-surface-800 shadow-sm p-6">
                    <h4 class="font-bold text-gray-900 dark:text-white text-sm mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-primary-500"></i> Informasi Akun
                    </h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-surface-700">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Username</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->username }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-surface-700">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Role</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white capitalize">{{ $user->role->value }}</span>
                        </div>
                        @if($user->jenis)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-surface-700">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Jenis</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->jenis }}</span>
                        </div>
                        @endif
                        <div class="flex items-center justify-between py-2">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Terdaftar</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->created_at?->format('d M Y') ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
