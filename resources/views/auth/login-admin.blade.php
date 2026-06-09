<x-guest-layout>
    <x-slot name="title">Login Admin</x-slot>

    <div class="split-container flex bg-white/5 backdrop-blur-sm rounded-3xl overflow-hidden shadow-2xl">
        
        {{-- Left Panel - Branding --}}
        <div class="brand-panel hidden lg:flex lg:w-1/2 p-8 lg:p-12 flex-col justify-center items-center text-center animate-fade-in bg-white/10">
            
            {{-- Logos - Bigger Size --}}
            <div class="flex flex-col sm:flex-row items-center gap-6 mb-8">
                <img src="{{ asset('images/logo1.png') }}" alt="Logo UBSI" class="h-24 lg:h-28 w-auto drop-shadow-lg">
                <img src="{{ asset('images/logo2.png') }}" alt="Logo PKL" class="h-24 lg:h-28 w-auto drop-shadow-lg">
            </div>
            
            {{-- Title --}}
            <h1 class="text-3xl lg:text-4xl font-bold text-white tracking-tight mb-3">
                Admin Panel
            </h1>
            <p class="text-lg text-white/80 font-medium mb-6">
                Sistem Informasi PKL — UBSI
            </p>
            
            {{-- Tagline / Description --}}
            <div class="max-w-md text-white/60 text-sm leading-relaxed">
                <p>Portal khusus administrator untuk</p>
                <p>pengelolaan sistem PKL dan MSIB</p>
            </div>
            
            {{-- Decorative Elements --}}
            <div class="mt-8 flex items-center gap-4 text-white/40">
                <div class="h-px w-12 bg-white/20"></div>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <div class="h-px w-12 bg-white/20"></div>
            </div>
        </div>

        {{-- Right Panel - Form --}}
        <div class="w-full lg:w-1/2 p-8 lg:p-12">
            <div class="login-card rounded-2xl p-6 lg:p-8 h-full">
                
                {{-- Flash Messages --}}
                @if(session('error'))
                    <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Mobile: Show logos small --}}
                <div class="lg:hidden text-center mb-6">
                    <div class="flex items-center justify-center gap-4 mb-3">
                        <img src="{{ asset('images/logo1.png') }}" alt="Logo UBSI" class="h-12 w-auto">
                        <img src="{{ asset('images/logo2.png') }}" alt="Logo PKL" class="h-12 w-auto">
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Admin Panel</h2>
                </div>

                <form method="POST" action="{{ route('login.admin.store') }}">
                    @csrf
                    <input type="hidden" name="role" value="admin">

                    {{-- Admin Badge --}}
                    <div class="flex items-center justify-center mb-6">
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 shadow-sm">
                            <i class="fa-solid fa-shield-halved text-lg text-blue-600 dark:text-blue-400"></i>
                            <span class="text-xs font-bold uppercase tracking-widest text-blue-700 dark:text-blue-300">Administrator</span>
                        </div>
                    </div>

                    {{-- Username --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Username Admin
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input type="text" name="username" value="{{ old('username') }}" required autofocus
                                   class="login-input w-full bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-xl pl-12 pr-4 py-3 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none text-sm font-medium transition-colors"
                                   placeholder="Masukkan username admin">
                        </div>
                        @error('username') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input type="password" name="password" required
                                   class="login-input w-full bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-xl pl-12 pr-4 py-3 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none text-sm font-medium transition-colors"
                                   placeholder="Masukkan password">
                        </div>
                        @error('password') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    {{-- Cloudflare Turnstile + Math CAPTCHA fallback --}}
                    <div class="mb-6" x-data="{ turnstileFailed: false }"
                         x-init="window.addEventListener('turnstile:failed', () => turnstileFailed = true);
                                 setTimeout(() => { if (typeof window.turnstile === 'undefined') turnstileFailed = true }, 3500);">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Verifikasi Keamanan
                        </label>
                        @if(! $turnstileBypassLocal)
                            <div x-show="!turnstileFailed" class="cf-turnstile rounded-lg overflow-hidden" data-sitekey="{{ $turnstileSiteKey }}" data-theme="light"></div>

                            @if($mathCaptcha)
                                <div x-show="turnstileFailed" x-cloak class="space-y-2">
                                    <div class="rounded-xl border border-amber-200 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-800 px-4 py-3 text-xs text-amber-700 dark:text-amber-400">
                                        Verifikasi Cloudflare tidak tersedia. Silakan jawab soal berikut.
                                    </div>
                                    <label class="flex items-center gap-3 px-4 py-3 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-xl">
                                        <span class="text-base font-semibold text-gray-700 dark:text-gray-200 select-none">{{ $mathCaptcha['question'] }} =</span>
                                        <input type="text" name="math_captcha_answer" inputmode="numeric" pattern="-?\d{1,3}" maxlength="5"
                                               class="flex-1 bg-transparent border-0 focus:outline-none text-sm font-medium text-gray-900 dark:text-white"
                                               placeholder="Jawaban">
                                    </label>
                                </div>
                            @endif
                        @else
                            <input type="hidden" name="cf-turnstile-response" value="local-bypass-token">
                            <div class="rounded-xl border border-emerald-200 bg-emerald-50 dark:bg-emerald-900/20 dark:border-emerald-800 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
                                CAPTCHA bypass aktif untuk environment lokal.
                            </div>
                        @endif
                        @error('cf-turnstile-response') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                        @error('math_captcha_answer') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                            class="btn-login w-full py-3.5 rounded-xl text-base font-semibold text-white shadow-lg hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Masuk sebagai Admin
                    </button>
                </form>

                {{-- Back Link --}}
                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors font-medium">
                        ← Kembali ke login umum
                    </a>
                </div>

                {{-- Footer --}}
                <p class="text-center text-xs text-gray-500 dark:text-gray-400 mt-6 font-medium">
                    &copy; {{ date('Y') }} Sistem PKL v2.0 — UBSI
                </p>

            </div>
        </div>

    </div>
</x-guest-layout>
