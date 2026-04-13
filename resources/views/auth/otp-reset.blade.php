<x-guest-layout>
    <x-slot name="title">Verifikasi Reset Password</x-slot>

    <div class="bg-white/10 dark:bg-surface-800/90 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/10 dark:border-surface-700 p-8 text-center" style="max-width: 400px; margin: 0 auto;">

        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-blue-500/25">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
        </div>

        <h2 class="text-xl font-bold text-white mb-2">Verifikasi OTP</h2>
        <p class="text-sm text-white/60 mb-6">Masukkan 6 digit kode yang dikirim ke<br><span class="text-blue-400 font-medium">{{ $email }}</span></p>

        @if(session('status'))
            <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs text-left">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.confirm-otp') }}" class="space-y-4">
            @csrf
            <div>
                <input type="text" name="otp_code" maxlength="6" required autofocus
                       class="w-full bg-white/10 border border-white/10 rounded-lg px-4 py-3 text-white text-center text-2xl tracking-[0.5em] font-mono placeholder-white/30 focus:border-blue-500 focus:ring-blue-500"
                       placeholder="000000">
                @error('otp_code') <p class="text-red-400 text-xs mt-2 text-left">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-blue-600/20">
                Verifikasi Kode
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-white/5">
            <p class="text-xs text-white/40">
                Kembali ke <a href="{{ route('password.request') }}" class="text-blue-400 hover:underline">Halaman Lupa Password</a>
            </p>
        </div>
    </div>
</x-guest-layout>
