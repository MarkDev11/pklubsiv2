<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordOtp;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'Akun dengan email tersebut tidak ditemukan.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Generate OTP
        $user->generateOtp();

        // Send Email
        Mail::to($user->email)->send(new ResetPasswordOtp($user->otp_code));

        // Simpan email di session untuk tahap verifikasi
        session(['reset_email' => $user->email]);

        return redirect()->route('password.verify-otp')
            ->with('status', 'Kode OTP telah dikirim ke email Anda.');
    }

    /**
     * Tampilkan form verifikasi OTP.
     */
    public function showVerifyForm(): View|RedirectResponse
    {
        if (! session('reset_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.otp-reset', [
            'email' => session('reset_email'),
        ]);
    }

    /**
     * Verifikasi kode OTP.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp_code' => ['required', 'string', 'size:6'],
        ]);

        $email = session('reset_email');
        $user = User::where('email', $email)->first();

        if (! $user || ! $user->verifyOtp($request->otp_code)) {
            return back()->withErrors(['otp_code' => 'Kode OTP salah atau sudah kedaluwarsa.']);
        }

        // Generate temporary token for password reset
        $token = bin2hex(random_bytes(32));
        session(['reset_token' => $token]);

        // Clear resend attempts on successful verification
        session()->forget('otp_resend_attempts');
        session()->forget('otp_resend_last_time');

        return redirect()->route('password.reset', ['token' => $token, 'email' => $email]);
    }

    /**
     * Kirim ulang kode OTP.
     */
    public function resendOtp(Request $request): RedirectResponse
    {
        $email = session('reset_email');

        if (! $email) {
            return redirect()->route('password.request')
                ->with('error', 'Sesi telah berakhir. Silakan mulai ulang proses reset password.');
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            return redirect()->route('password.request')
                ->with('error', 'Akun tidak ditemukan.');
        }

        // Check cooldown (60 seconds)
        $lastResend = session('otp_resend_last_time');
        if ($lastResend) {
            $secondsSinceLastResend = now()->diffInSeconds($lastResend);
            $cooldownRemaining = 60 - $secondsSinceLastResend;

            if ($cooldownRemaining > 0) {
                return back()->with('error', "Mohon tunggu {$cooldownRemaining} detik sebelum mengirim ulang.");
            }
        }

        // Check max attempts (3 times)
        $resendAttempts = session('otp_resend_attempts', 0);
        if ($resendAttempts >= 3) {
            return redirect()->route('password.request')
                ->with('error', 'Anda telah melebihi batas pengiriman ulang OTP. Silakan mulai proses dari awal.');
        }

        // Generate new OTP
        $user->generateOtp();

        // Send Email
        Mail::to($user->email)->send(new ResetPasswordOtp($user->otp_code));

        // Update session
        session(['otp_resend_attempts' => $resendAttempts + 1]);
        session(['otp_resend_last_time' => now()]);

        return back()->with('status', 'Kode OTP baru telah dikirim ke email Anda.');
    }
}
