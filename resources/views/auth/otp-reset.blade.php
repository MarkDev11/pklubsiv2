<x-guest-layout>
    <x-slot name="title">Verifikasi Reset Password</x-slot>

    <div class="split-container flex bg-white/5 backdrop-blur-sm rounded-3xl overflow-hidden shadow-2xl"
         x-data="{ 
            isLoading: false,
            isResending: false,
            resendCountdown: 0,
            resendAttempts: {{ session('otp_resend_attempts', 0) }},
            
            initCountdown() {
                const lastResend = @js(session('otp_resend_last_time'));
                if (lastResend) {
                    const lastResendTime = new Date(lastResend);
                    const now = new Date();
                    const diffInSeconds = Math.floor((now - lastResendTime) / 1000);
                    const remainingCooldown = 60 - diffInSeconds;
                    
                    if (remainingCooldown > 0) {
                        this.resendCountdown = remainingCooldown;
                        this.startCountdown();
                    }
                }

                this.$nextTick(() => {
                    if (this.$refs.otpInput) {
                        this.$refs.otpInput.focus();
                        this.$refs.otpInput.select();
                    }
                });
            },
            
            startCountdown() {
                const interval = setInterval(() => {
                    this.resendCountdown--;
                    if (this.resendCountdown <= 0) {
                        clearInterval(interval);
                        this.resendCountdown = 0;
                    }
                }, 1000);
            },
            
            resendOtp() {
                if (this.resendCountdown > 0 || this.isResending) return;
                this.isResending = true;
                this.$refs.resendForm.submit();
            },
            
            handleOtpInput(event) {
                const value = event.target.value.replace(/\D/g, '');
                event.target.value = value;
                if (value.length === 6) {
                    this.isLoading = true;
                    event.target.form.submit();
                }
            },
            
            handlePaste(event) {
                event.preventDefault();
                const paste = (event.clipboardData || window.clipboardData).getData('text');
                const cleaned = paste.replace(/\D/g, '').slice(0, 6);
                event.target.value = cleaned;
                if (cleaned.length === 6) {
                    this.isLoading = true;
                    event.target.form.submit();
                }
            }
         }"
         x-init="initCountdown()">
        
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
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

                {{-- Icon Header --}}
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </div>
                    
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        Verifikasi OTP
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Masukkan 6 digit kode yang dikirim ke
                    </p>
                    <p class="text-sm text-blue-600 dark:text-blue-400 font-semibold mt-1">
                        {{ $email }}
                    </p>
                </div>

                {{-- Session Status --}}
                @if(session('status'))
                    <div class="mb-6 px-4 py-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('status') }}
                    </div>
                @endif

                {{-- Error Message --}}
                @if(session('error'))
                    <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                {{-- OTP Form --}}
                <form method="POST" action="{{ route('password.confirm-otp') }}">
                    @csrf

                    {{-- OTP Input --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 text-center">
                            Kode OTP
                        </label>
                        <input type="text" name="otp_code" maxlength="6" required autofocus
                               class="login-input w-full bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-xl px-4 py-3 text-gray-900 dark:text-white text-center text-2xl tracking-[0.5em] font-mono placeholder-gray-400 focus:outline-none transition-colors"
                               placeholder="000000"
                               x-ref="otpInput"
                               @input="handleOtpInput($event)"
                               @paste="handlePaste($event)">
                        @error('otp_code') 
                            <p class="text-red-600 text-xs mt-2 text-center">{{ $message }}</p> 
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                            class="btn-login w-full py-3.5 rounded-xl text-base font-semibold text-white shadow-lg hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2"
                            :disabled="isLoading">
                        <svg x-show="!isLoading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <svg x-show="isLoading" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isLoading ? 'Memverifikasi...' : 'Verifikasi Kode'"></span>
                    </button>
                </form>

                {{-- Resend OTP Section --}}
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-slate-600">
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                            Tidak menerima kode?
                        </p>
                        
                        {{-- Resend Form --}}
                        <form method="POST" action="{{ route('password.resend-otp') }}" x-ref="resendForm">
                            @csrf
                        </form>
                        
                        {{-- Resend Button --}}
                        <button type="button"
                                @click="resendOtp()"
                                :disabled="resendCountdown > 0"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200"
                                :class="resendCountdown > 0 
                                    ? 'bg-gray-100 dark:bg-slate-700 text-gray-400 dark:text-gray-500 cursor-not-allowed' 
                                    : 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50'">
                            <svg x-show="!isResending && resendCountdown === 0" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <svg x-show="isResending" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            
                            <span x-text="resendCountdown > 0 ? 'Kirim ulang (' + resendCountdown + 's)' : 'Kirim ulang OTP'"></span>
                        </button>
                        
                        {{-- Attempts Warning --}}
                        <p class="text-xs text-amber-600 dark:text-amber-400 mt-3" x-show="resendAttempts >= 2">
                            <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Sisa percobaan: <span x-text="3 - resendAttempts"></span>
                        </p>
                    </div>
                </div>

                {{-- Back Link --}}
                <div class="mt-4 text-center">
                    <a href="{{ route('password.request') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Halaman Lupa Password
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
