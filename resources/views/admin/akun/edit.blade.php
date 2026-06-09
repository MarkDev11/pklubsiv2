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

        {{-- Profile Header Card - COLORFUL --}}
        <div class="card overflow-hidden">
            <div class="px-6 py-8 relative" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%);">
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-16 h-16 rounded-xl flex items-center justify-center text-white text-2xl font-bold shadow-lg" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); border: 2px solid rgba(255, 255, 255, 0.3);">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white drop-shadow-sm">{{ $user->name }}</h3>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="text-blue-100 text-sm font-medium font-mono">{{ $user->username }}</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-white/40"></span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold uppercase" style="background: rgba(255, 255, 255, 0.25); color: white; backdrop-filter: blur(10px);">
                                <i class="{{ $user->role->value === 'admin' ? 'fa-solid fa-shield-halved' : ($user->role->value === 'dosen' ? 'fa-solid fa-chalkboard-user' : ($user->role->value === 'mentor' ? 'fa-solid fa-building' : 'fa-solid fa-graduation-cap')) }} text-[10px]"></i>
                                {{ $user->role->value }}
                            </span>
                        </div>
                    </div>
                </div>
                {{-- Decorative Elements --}}
                <div class="absolute -right-8 -top-8 w-32 h-32 rounded-full opacity-20" style="background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);"></div>
                <div class="absolute -left-6 -bottom-6 w-24 h-24 rounded-full opacity-20" style="background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);"></div>
            </div>
        </div>

        {{-- Edit Form --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Main Form --}}
            <div class="lg:col-span-2">
                <div class="card border-l-4 border-blue-500">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-transparent dark:from-blue-900/10 dark:to-transparent">
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shadow-sm" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                                <i class="fa-solid fa-user-pen text-white text-sm"></i>
                            </div>
                            <h4 class="font-bold text-gray-900 dark:text-white">Informasi Akun</h4>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.akun.update', encryptUrl($user->username)) }}">
                        @csrf @method('PUT')
                        <div class="p-6 space-y-5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-5 items-start">
                                <div class="flex flex-col">
                                    <label class="form-label mb-2"><i class="fa-solid fa-user text-[11px] mr-1.5 text-gray-400"></i>Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
                                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="flex flex-col">
                                    <label class="form-label mb-2"><i class="fa-solid fa-id-card text-[11px] mr-1.5 text-gray-400"></i>Username (NIM/NIP)</label>
                                    <input type="text" name="username" value="{{ old('username', $user->username) }}" class="form-input" required>
                                    @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-5 items-start">
                                <div class="flex flex-col">
                                    <label class="form-label mb-2"><i class="fa-solid fa-user-tag text-[11px] mr-1.5 text-gray-400"></i>Role</label>
                                    <select name="role" class="form-select" required>
                                        @foreach(['mahasiswa','dosen','mentor','admin'] as $r)
                                            <option value="{{ $r }}" {{ $user->role->value === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex flex-col">
                                    <label class="form-label mb-2"><i class="fa-solid fa-briefcase text-[11px] mr-1.5 text-gray-400"></i>Jenis PKL</label>
                                    <select name="jenis" class="form-select">
                                        <option value="">— Pilih —</option>
                                        <option value="Magang" {{ $user->jenis === 'Magang' ? 'selected' : '' }}>Magang</option>
                                        <option value="Program Magang khusus (PMK/GNIK/MBKM/MSIB/PMMB)" {{ $user->jenis !== 'Magang' && $user->jenis ? 'selected' : '' }}>PMK/GNIK/MBKM/MSIB/PMMB</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex flex-col">
                                <label class="form-label mb-2"><i class="fa-solid fa-chalkboard-user text-[11px] mr-1.5 text-gray-400"></i>NIP Dosen PA</label>
                                <select name="nama_dosen_pa" class="form-select">
                                    <option value="">-- Kosongkan jika bukan mahasiswa --</option>
                                    @foreach($dosenList as $dosen)
                                        <option value="{{ $dosen->username }}" {{ old('nama_dosen_pa', $user->nama_dosen_pa) === $dosen->username ? 'selected' : '' }}>
                                            {{ $dosen->name }} (NIP: {{ $dosen->username }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('nama_dosen_pa') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex items-center justify-between">
                            <a href="{{ route('admin.akun.index') }}" 
                               style="background-color: #6b7280; color: white;"
                               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-md hover:opacity-90 transition-opacity">
                                <i class="fa-solid fa-arrow-left text-sm"></i> Kembali
                            </a>
                            <button type="submit" 
                                    style="background-color: #2563eb; color: white;"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-md hover:opacity-90 transition-opacity">
                                <i class="fa-solid fa-check text-sm"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Reset Password Card - COLORFUL --}}
                <div class="card border-l-4 border-red-500">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-red-50 to-transparent dark:from-red-900/10 dark:to-transparent">
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shadow-sm" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                                <i class="fa-solid fa-key text-white text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">Reset Password</h4>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400">Ganti password akun ini</p>
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.akun.reset-password', encryptUrl($user->username)) }}" class="p-6">
                        @csrf
                        <div class="space-y-3">
                            <div>
                                <input type="password" name="password" placeholder="Password baru (min 8 karakter)"
                                       class="form-input w-full @error('password') border-red-500 @enderror"
                                       required minlength="8">
                                @error('password')
                                    <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                        <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <div>
                                <input type="password" name="password_confirmation" placeholder="Konfirmasi password baru"
                                       class="form-input w-full @error('password_confirmation') border-red-500 @enderror"
                                       required minlength="8">
                                @error('password_confirmation')
                                    <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                        <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <button type="submit"
                                    style="background-color: #dc2626; color: white;"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold rounded-md hover:opacity-90 transition-opacity">
                                <i class="fa-solid fa-rotate text-sm"></i> Reset Password
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Quick Info Card - COLORFUL --}}
                <div class="card border-l-4 border-green-500">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-transparent dark:from-green-900/10 dark:to-transparent">
                        <h4 class="font-bold text-gray-900 dark:text-white text-sm mb-4 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                <i class="fa-solid fa-circle-info text-white text-xs"></i>
                            </div>
                            Informasi Akun
                        </h4>
                    </div>
                    <div class="px-6 pb-6 space-y-3">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Username</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->username }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Role</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white capitalize">{{ $user->role->value }}</span>
                        </div>
                        @if($user->jenis)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
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
