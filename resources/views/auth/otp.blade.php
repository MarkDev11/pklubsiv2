<x-guest-layout>
    <x-slot name="title">Verifikasi OTP</x-slot>

    <div class="bg-white/10 dark:bg-surface-800/90 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/10 dark:border-surface-700 p-8 text-center">

        <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-primary-500/25">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>

        <h2 class="text-xl font-bold text-white mb-2">Verifikasi OTP</h2>
        <p class="text-sm text-white/60 mb-6">Kode verifikasi telah dikirim ke<br><span class="text-primary-400 font-medium">{{ $email ?? 'email Anda' }}</span></p>

        @if(session('error'))
            <div class="alert-error mb-4 animate-fade-in text-left">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert-success mb-4 animate-fade-in text-left">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('otp.verify') }}" class="space-y-4">
            @csrf
            <div>
                <input type="text" name="otp_code" maxlength="6" required autofocus
                       class="w-full bg-white/10 border border-white/10 rounded-lg px-4 py-3 text-white text-center text-2xl tracking-[0.5em] font-mono placeholder-white/30 focus:border-primary-500 focus:ring-primary-500"
                       placeholder="000000">
                @error('otp_code') <p class="text-red-400 text-xs mt-2">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full btn-primary py-3 rounded-xl">Verifikasi</button>
        </form>

        <form method="POST" action="{{ route('otp.resend') }}" class="mt-4">
            @csrf
            <button type="submit" class="text-sm text-primary-400 hover:text-primary-300 transition-colors">
                Kirim ulang kode OTP
            </button>
        </form>
    </div>
</x-guest-layout>
