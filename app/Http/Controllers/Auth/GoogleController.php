<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;

class GoogleController extends Controller
{
    /**
     * Redirect ke Google OAuth.
     */
    public function redirect(): RedirectResponse
    {
        /** @var GoogleProvider $provider */
        $provider = Socialite::driver('google');

        return $provider
            ->with(['hd' => 'bsi.ac.id']) // hint domain BSI
            ->redirect();
    }

    /**
     * Handle callback dari Google.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Gagal autentikasi dengan Google. Silakan coba lagi.');
        }

        $email = $googleUser->getEmail();

        if (! is_string($email) || ! str_contains($email, '@')) {
            return redirect()->route('login')
                ->with('error', 'Email Google tidak valid.');
        }

        $domain = substr(strrchr($email, '@') ?: '', 1);

        // Validasi domain BSI
        if ($domain !== config('services.google.allowed_domain', 'bsi.ac.id')) {
            return redirect()->route('login')
                ->with('error', 'Hanya email @bsi.ac.id yang diperbolehkan.');
        }

        // Cari user berdasarkan email_bsi atau google_id
        $user = User::where('email_bsi', $email)
            ->orWhere('google_id', $googleUser->getId())
            ->first();

        if (! $user) {
            return redirect()->route('login')
                ->with('error', 'Akun dengan email BSI ini belum terdaftar di sistem. Hubungi administrator.');
        }

        // Update Google ID jika belum ada
        if (! $user->google_id) {
            $user->update([
                'google_id' => $googleUser->getId(),
                'email_bsi' => $email,
            ]);
        }

        // Login user
        Auth::login($user, true);

        // Log activity
        ActivityLog::log($user->id, 'Login via Google OAuth '.$user->username);

        // Redirect ke dashboard

        return redirect()->route($user->role->dashboardRoute());
    }
}
