<x-guest-layout>
    <x-slot name="title">Login Admin</x-slot>

    <div class="login-card rounded-3xl p-8">

        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="flex items-center justify-center gap-4 mb-5">
                <img src="{{ asset('images/logo1.png') }}" alt="Logo UBSI" class="h-16 w-auto drop-shadow-lg">
                <img src="{{ asset('images/logo2.png') }}" alt="Logo PKL" class="h-16 w-auto drop-shadow-lg">
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Admin Panel</h1>
            <p class="text-sm text-white/60 mt-1.5 font-medium">Sistem Informasi PKL — UBSI</p>
        </div>

        {{-- Flash Messages --}}
        @if(session('error'))
            <div class="mb-4 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-200 text-sm flex items-center gap-2 animate-fade-in">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.admin.store') }}">
            @csrf
            <input type="hidden" name="role" value="admin">

            {{-- Admin Badge --}}
            <div class="flex items-center justify-center mb-6">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/95 shadow-lg">
                    <i class="fa-solid fa-shield-halved text-lg text-blue-600"></i>
                    <span class="text-xs font-bold uppercase tracking-widest text-blue-700">Administrator</span>
                </div>
            </div>

            {{-- Username --}}
            <div class="mb-4">
                <label class="block text-xs font-bold text-white/80 uppercase tracking-widest mb-2">Username Admin</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg class="w-4 h-4 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <input type="text" name="username" value="{{ old('username') }}" required autofocus
                           class="login-input w-full bg-white/[0.04] border border-white/[0.08] rounded-xl pl-11 pr-4 py-3 text-white placeholder-white/40 focus:border-blue-400/50 focus:outline-none text-sm font-medium transition-all duration-300"
                           placeholder="Masukkan username admin">
                </div>
                @error('username') <p class="text-red-300 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <label class="block text-xs font-bold text-white/80 uppercase tracking-widest mb-2">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg class="w-4 h-4 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <input type="password" name="password" required
                           class="login-input w-full bg-white/[0.04] border border-white/[0.08] rounded-xl pl-11 pr-4 py-3 text-white placeholder-white/40 focus:border-blue-400/50 focus:outline-none text-sm font-medium transition-all duration-300"
                           placeholder="Masukkan password">
                </div>
                @error('password') <p class="text-red-300 text-xs mt-1.5">{{ $message }}</p> @enderror
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
                @error('cf-turnstile-response') <p class="text-red-300 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full relative overflow-hidden py-3.5 rounded-xl text-base font-bold text-white shadow-2xl shadow-blue-600/25 transition-all duration-300 hover:shadow-blue-500/40 hover:-translate-y-0.5 active:translate-y-0 group"
                    style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%);">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Masuk sebagai Admin
                </span>
                <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
            </button>
        </form>

        <div class="mt-5 text-center">
            <a href="{{ route('login') }}" class="text-sm text-white/50 hover:text-white/90 transition-colors font-medium">← Kembali ke login umum</a>
        </div>

        <p class="text-center text-[11px] text-white/40 mt-4 font-medium">&copy; {{ date('Y') }} Sistem PKL v2.0 — UBSI</p>
    </div>
</x-guest-layout>
