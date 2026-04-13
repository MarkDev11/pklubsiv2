<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class OtpController extends Controller
{
    public function show(): View|RedirectResponse
    {
        $user = $this->authenticatedUser();

        if ($user->otp_verified) {
            return redirect()->route($user->role->dashboardRoute());
        }

        if (! $user->otp_code || ($user->otp_expires_at && $user->otp_expires_at->isPast())) {
            $user->generateOtp();
        }

        return view('auth.otp', [
            'email' => $user->email,
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'otp_code' => ['required', 'string', 'size:6'],
        ], [
            'otp_code.required' => 'Kode OTP wajib diisi.',
            'otp_code.size' => 'Kode OTP harus 6 digit.',
        ]);

        $user = $this->authenticatedUser();

        $this->ensureIsNotRateLimited($request);

        if ($user->verifyOtp($validated['otp_code'])) {
            RateLimiter::clear($this->throttleKey($request));

            return redirect()->route($user->role->dashboardRoute())
                ->with('success', 'Verifikasi OTP berhasil!');
        }

        RateLimiter::hit($this->throttleKey($request));

        return back()->with('error', 'Kode OTP salah atau sudah kedaluwarsa.');
    }

    public function resend(): RedirectResponse
    {
        $user = $this->authenticatedUser();

        $this->ensureIsNotRateLimited(request());

        RateLimiter::hit($this->throttleKey(request()), 60);

        $user->generateOtp();

        return back()->with('success', 'Kode OTP baru telah dikirim ke email Anda.');
    }

    protected function throttleKey(Request $request): string
    {
        return 'otp:'.$request->ip();
    }

    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        abort(429, "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.");
    }
}
