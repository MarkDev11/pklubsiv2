<x-guest-layout>
    <x-slot name="title">Lupa Password</x-slot>

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
                        Lupa Password?
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        Tidak masalah. Masukkan alamat email Anda dan kami akan mengirimkan link reset password untuk membuat password baru.
                    </p>
                </div>

                {{-- Session Status --}}
                @if (session('status'))
                    <div class="mb-6 px-4 py-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Alamat Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="login-input w-full bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-xl pl-12 pr-4 py-3 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none text-sm font-medium transition-colors"
                                   placeholder="Masukkan email Anda">
                        </div>
                        @error('email') 
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p> 
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                            class="btn-login w-full py-3.5 rounded-xl text-base font-semibold text-white shadow-lg hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Kirim Link Reset Password
                    </button>
                </form>

                {{-- Back to Login --}}
                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium hover:underline transition-colors">
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
