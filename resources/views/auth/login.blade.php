<x-guest-layout>
    <x-slot name="title">Login</x-slot>

    <div class="login-card rounded-3xl p-8" x-data="{ selectedRole: '{{ old('role', 'mahasiswa') }}' }">

        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="flex items-center justify-center gap-4 mb-5">
                <img src="{{ asset('images/logo1.png') }}" alt="Logo UBSI" class="h-16 w-auto drop-shadow-lg">
                <img src="{{ asset('images/logo2.png') }}" alt="Logo PKL" class="h-16 w-auto drop-shadow-lg">
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Sistem Informasi PKL</h1>
            <p class="text-sm text-white/60 mt-1.5 font-medium">Universitas Bina Sarana Informatika</p>
        </div>

        {{-- Flash Messages --}}
        @if(session('error'))
            <div class="mb-4 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-300 text-sm flex items-center gap-2 animate-fade-in">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif
        @if(session('status'))
            <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm flex items-center gap-2 animate-fade-in">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Role Selection --}}
            <div class="mb-6">
                <label class="block text-xs font-bold text-white/80 uppercase tracking-widest mb-3">Masuk Sebagai</label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach(['mahasiswa' => ['fa-solid fa-graduation-cap', 'Mahasiswa'], 'dosen' => ['fa-solid fa-chalkboard-user', 'Dosen'], 'mentor' => ['fa-solid fa-building', 'Mentor']] as $value => [$icon, $label])
                        <label class="relative cursor-pointer" @click="selectedRole = '{{ $value }}'">
                            <input type="radio" name="role" value="{{ $value }}" class="peer sr-only" :checked="selectedRole === '{{ $value }}'">
                            <div class="role-card flex flex-col items-center gap-1.5 px-2 py-3 rounded-xl border border-white/10 bg-white/[0.03] text-center"
                                 :class="selectedRole === '{{ $value }}' ? 'active' : ''">
                                <i class="{{ $icon }} text-xl leading-none transition-colors duration-300"
                                   :class="selectedRole === '{{ $value }}' ? 'text-blue-700' : 'text-white'"></i>
                                <span class="text-[11px] font-bold uppercase tracking-wide text-white/80"
                                      :class="selectedRole === '{{ $value }}' ? '!text-blue-700' : ''">{{ $label }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('role') <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Username --}}
            <div class="mb-4">
                <label class="block text-xs font-bold text-white/80 uppercase tracking-widest mb-2">NIM / NIP / Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg class="w-4 h-4 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <input type="text" name="username" value="{{ old('username') }}" required autofocus
                           class="login-input w-full bg-white/[0.04] border border-white/[0.08] rounded-xl pl-11 pr-4 py-3 text-white placeholder-white/40 focus:border-blue-500/50 focus:outline-none text-sm font-medium transition-all duration-300"
                           placeholder="Masukkan NIM, NIP, atau email">
                </div>
                @error('username') <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <label class="block text-xs font-bold text-white/80 uppercase tracking-widest mb-2">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg class="w-4 h-4 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <input type="password" name="password" required
                           class="login-input w-full bg-white/[0.04] border border-white/[0.08] rounded-xl pl-11 pr-4 py-3 text-white placeholder-white/40 focus:border-blue-500/50 focus:outline-none text-sm font-medium transition-all duration-300"
                           placeholder="Masukkan password">
                </div>
                @error('password') <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Cloudflare Turnstile --}}
            <div class="mb-6">
                <label class="block text-xs font-bold text-white/80 uppercase tracking-widest mb-2">Verifikasi Keamanan</label>
                @if(! $turnstileBypassLocal)
                    <div class="cf-turnstile" data-sitekey="{{ $turnstileSiteKey }}" data-theme="dark"></div>
                @else
                    <input type="hidden" name="cf-turnstile-response" value="local-bypass-token">
                    <div class="rounded-xl border border-emerald-300/30 bg-emerald-500/10 px-3 py-2 text-xs font-medium text-emerald-200">
                        CAPTCHA bypass aktif untuk environment lokal.
                    </div>
                @endif
                @error('cf-turnstile-response') <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full relative overflow-hidden py-3.5 rounded-xl text-base font-bold text-white shadow-2xl shadow-blue-600/25 transition-all duration-300 hover:shadow-blue-500/40 hover:-translate-y-0.5 active:translate-y-0 group"
                    style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%);">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Masuk
                </span>
                <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
            </button>
        </form>

        {{-- Google OAuth --}}
        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-white/[0.06]"></div></div>
                <div class="relative flex justify-center text-xs"><span class="px-4 text-white/70 font-semibold uppercase tracking-widest" style="background: rgba(255, 255, 255, 0.12); border-radius: 20px;">atau</span></div>
            </div>
            <a href="{{ route('auth.google') }}"
               class="mt-4 w-full flex items-center justify-center gap-3 px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/80 text-sm font-semibold hover:bg-white/[0.08] hover:border-white/[0.12] transition-all duration-300 group">
                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                Masuk dengan Google BSI
            </a>
        </div>

        {{-- Footer --}}
        <p class="text-center text-[11px] text-white/40 mt-6 font-medium">&copy; {{ date('Y') }} Sistem PKL v2.0 — UBSI</p>

    </div>
</x-guest-layout>
