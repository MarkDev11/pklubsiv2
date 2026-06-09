<x-guest-layout>
    <x-slot name="title">Login</x-slot>

    <div class="split-container flex bg-white/5 backdrop-blur-sm rounded-3xl overflow-hidden shadow-2xl" x-data="{ selectedRole: '{{ old('role', 'mahasiswa') }}' }">
        
        {{-- Left Panel - Branding --}}
        <div class="brand-panel hidden lg:flex lg:w-1/2 p-8 lg:p-12 flex-col justify-center items-center text-center animate-fade-in bg-white/10">
            
            {{-- Logos - Bigger Size --}}
            <div class="flex flex-col sm:flex-row items-center gap-6 mb-8">
                <img src="{{ asset('images/logo1.png') }}" alt="Logo UBSI" class="h-24 lg:h-28 w-auto drop-shadow-lg">
                <img src="{{ asset('images/logo2.png') }}" alt="Logo PKL" class="h-24 lg:h-28 w-auto drop-shadow-lg">
            </div>
            
            {{-- Title --}}
            <h1 class="text-3xl lg:text-4xl font-bold text-white tracking-tight mb-3">
                Sistem Informasi PKL
            </h1>
            <p class="text-lg text-white/80 font-medium mb-6">
                Universitas Bina Sarana Informatika
            </p>
            
            {{-- Tagline / Description --}}
            <div class="max-w-md text-white/60 text-sm leading-relaxed">
                <p>Platform terintegrasi untuk pengelolaan</p>
                <p>Program Kerja Lapangan dan MSIB</p>
            </div>
            
            {{-- Decorative Elements --}}
            <div class="mt-8 flex items-center gap-4 text-white/40">
                <div class="h-px w-12 bg-white/20"></div>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
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
                @if(session('status'))
                    <div class="mb-6 px-4 py-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('status') }}
                    </div>
                @endif

                {{-- Mobile: Show logos small --}}
                <div class="lg:hidden text-center mb-6">
                    <div class="flex items-center justify-center gap-4 mb-3">
                        <img src="{{ asset('images/logo1.png') }}" alt="Logo UBSI" class="h-12 w-auto">
                        <img src="{{ asset('images/logo2.png') }}" alt="Logo PKL" class="h-12 w-auto">
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Sistem Informasi PKL</h2>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Role Selection --}}
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">
                            Masuk Sebagai
                        </label>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach(['mahasiswa' => ['fa-solid fa-graduation-cap', 'Mahasiswa'], 'dosen' => ['fa-solid fa-chalkboard-user', 'Dosen'], 'mentor' => ['fa-solid fa-building', 'Mentor']] as $value => [$icon, $label])
                                <label class="relative cursor-pointer" @click="selectedRole = '{{ $value }}'">
                                    <input type="radio" name="role" value="{{ $value }}" class="peer sr-only" :checked="selectedRole === '{{ $value }}'">
                                    <div class="role-card flex flex-col items-center gap-2 px-2 py-3 rounded-xl border-2 border-gray-200 bg-white text-center"
                                         :class="selectedRole === '{{ $value }}' ? 'active' : ''">
                                        <i class="{{ $icon }} text-lg" :class="selectedRole === '{{ $value }}' ? 'text-white' : 'text-gray-600 dark:text-gray-400'"></i>
                                        <span class="text-xs font-semibold" :class="selectedRole === '{{ $value }}' ? 'text-white' : 'text-gray-700 dark:text-gray-300'">{{ $label }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('role') <p class="text-red-600 text-xs mt-2">{{ $message }}</p> @enderror
                    </div>

                    {{-- Username --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            NIM / NIP / Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input type="text" name="username" value="{{ old('username') }}" required autofocus
                                   class="login-input w-full bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-xl pl-12 pr-4 py-3 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none text-sm font-medium transition-colors"
                                   placeholder="Masukkan NIM, NIP, atau email">
                        </div>
                        @error('username') <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Password
                            </label>
                            <a href="{{ route('password.request') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium hover:underline">
                                Lupa password?
                            </a>
                        </div>
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
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        Masuk
                    </button>
                </form>

                {{-- Google OAuth --}}
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200 dark:border-slate-600"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white dark:bg-slate-800 text-gray-500 dark:text-gray-400 font-medium">
                                atau
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('auth.google') }}"
                       class="mt-4 w-full flex items-center justify-center gap-3 px-4 py-3 bg-white dark:bg-slate-700 border-2 border-gray-200 dark:border-slate-600 rounded-xl text-gray-700 dark:text-gray-200 text-sm font-semibold hover:border-gray-300 dark:hover:border-slate-500 hover:bg-gray-50 dark:hover:bg-slate-600 transition-all duration-200">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Masuk dengan Google BSI
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
