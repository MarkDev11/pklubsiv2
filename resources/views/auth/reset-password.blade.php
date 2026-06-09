<x-guest-layout>
    <x-slot name="title">Reset Password</x-slot>

    <div class="split-container flex bg-white/5 backdrop-blur-sm rounded-3xl overflow-hidden shadow-2xl"
         x-data="{ showPassword: false, showConfirmPassword: false }">
        
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2a3 3 0 11-6 0v-2a3 3 0 116 0zm6 4a2 2 0 100-4 2 2 0 000 4zM7 6a3 3 0 116 0 3 3 0 01-6 0zm6 4a2 2 0 100-4 2 2 0 000 4zm-7 4a3 3 0 100-6 3 3 0 000 6z"/>
                </svg>
                <div class="h-px w-12 bg-white/20"></div>
            </div>
        </div>

        {{-- Right Panel - Form --}}
        <div class="w-full lg:w-1/2 p-8 lg:p-12">
            <div class="login-card rounded-2xl p-6 lg:p-8 h-full">
                
                {{-- Mobile: Show logos small --}}
                <div class="lg:hidden text-center mb-6">
                    <div class="flex items-center justify-center gap-4 mb-3">
                        <img src="{{ asset('images/logo1.png') }}" alt="Logo UBSI" class="h-12 w-auto">
                        <img src="{{ asset('images/logo2.png') }}" alt="Logo PKL" class="h-12 w-auto">
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Sistem Informasi PKL</h2>
                </div>

                {{-- Header --}}
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        Buat Password Baru
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        Masukkan password baru untuk akun Anda. Pastikan password kuat dan mudah diingat.
                    </p>
                </div>

                {{-- Error Message --}}
                @if(session('error'))
                    <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/50 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                {{-- Validation Errors --}}
                @if($errors->any())
                    <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/50 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="font-semibold">Password tidak valid</span>
                        </div>
                        <ul class="ml-11 text-sm space-y-1">
                            @foreach($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    {{-- Hidden Token & Email --}}
                    <input type="hidden" name="token" value="{{ $token ?? '' }}">
                    <input type="hidden" name="email" value="{{ $email ?? '' }}">

                    {{-- Email Display --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input type="text" 
                                   value="{{ $email ?? '' }}" 
                                   disabled
                                   class="w-full bg-gray-100 dark:bg-slate-600 border border-gray-300 dark:border-slate-500 rounded-xl pl-12 pr-4 py-3 text-gray-500 dark:text-gray-300 text-sm font-medium cursor-not-allowed">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Password Baru
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input type="password"
                                   name="password"
                                   required
                                   autofocus
                                   class="login-input w-full bg-white dark:bg-slate-700 border rounded-xl pl-12 pr-12 py-3 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none text-sm font-medium transition-colors @error('password') border-red-500 @else border-gray-300 dark:border-slate-600 @enderror"
                                   placeholder="Minimal 8 karakter"
                                   :type="showPassword ? 'text' : 'password'">
                            <button type="button"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                    @click="showPassword = !showPassword">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A11.955 11.955 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-600 dark:text-red-400 text-xs mt-1.5">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Minimal 8 karakter dengan kombinasi huruf dan angka.</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Konfirmasi Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <input type="password"
                                   name="password_confirmation"
                                   required
                                   class="login-input w-full bg-white dark:bg-slate-700 border rounded-xl pl-12 pr-12 py-3 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none text-sm font-medium transition-colors @error('password_confirmation') border-red-500 @else border-gray-300 dark:border-slate-600 @enderror"
                                   placeholder="Masukkan ulang password"
                                   :type="showConfirmPassword ? 'text' : 'password'">
                            <button type="button"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                    @click="showConfirmPassword = !showConfirmPassword">
                                <svg x-show="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A11.955 11.955 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="text-red-600 dark:text-red-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                            class="btn-login w-full py-3.5 rounded-xl text-base font-semibold text-white shadow-lg hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Simpan Password Baru
                    </button>
                </form>

                {{-- Back Link --}}
                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Login
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