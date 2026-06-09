<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View|RedirectResponse
    {
        // Pastikan ada token di session sebagai bukti verifikasi OTP
        if (! session('reset_token') || ! session('reset_email')) {
            return redirect()->route('password.request')
                ->with('error', 'Silakan verifikasi OTP terlebih dahulu.');
        }

        return view('auth.reset-password', [
            'email' => session('reset_email'),
            'token' => session('reset_token'),
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'token.required' => 'Token tidak valid.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal :min karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Verifikasi token session
        if ($request->token !== session('reset_token') || $request->email !== session('reset_email')) {
            return redirect()->route('password.request')
                ->with('error', 'Sesi reset password tidak valid atau sudah kedaluwarsa.');
        }

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'User tidak ditemukan.']);
        }

        // Update Password
        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
            'otp_code' => null, // Clear OTP
            'otp_expires_at' => null,
        ])->save();

        event(new PasswordReset($user));

        // Bersihkan session
        session()->forget(['reset_token', 'reset_email']);

        return redirect()->route('login')->with('status', 'Password berhasil diperbarui. Silakan login.');
    }
}
