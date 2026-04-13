<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SsoController extends Controller
{
    /**
     * Menangani passthrough login dari Web Student / Staff pusat.
     */
    public function handle(Request $request): RedirectResponse
    {
        $payload = $request->query('payload');
        $signature = $request->query('signature');
        $secret = (string) config('app.sso_shared_secret', '');

        if ($secret === '') {
            return redirect()->route('login')
                ->with('error', 'Konfigurasi SSO belum aktif.');
        }

        if (! $payload || ! $signature) {
            return redirect()->route('login')
                ->with('error', 'Token Kredensial SSO tidak lengkap.');
        }

        // 1. Validasi Keaslian Tanda Tangan (Signature)
        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        if (! hash_equals($expectedSignature, $signature)) {
            return redirect()->route('login')
                ->with('error', 'Tanda tangan otentikasi SSO tidak valid (Pemalsuan Akses).');
        }

        // 2. Decode payload & validasi isi
        $decoded = json_decode(base64_decode($payload), true);
        if (! $decoded || ! isset($decoded['username']) || ! isset($decoded['timestamp'])) {
            return redirect()->route('login')
                ->with('error', 'Struktur Payload SSO rusak.');
        }

        // 3. Validasi Batas Waktu untuk Anti-Replay Attack (Maksimal 60 Detik Toleransi)
        $timeDiff = time() - $decoded['timestamp'];
        if ($timeDiff > 60 || $timeDiff < -60) {
            return redirect()->route('login')
                ->with('error', 'Token SSO kedaluwarsa. Silakan muat ulang akses dari web asal Anda.');
        }

        // 4. Proses Otentikasi
        $user = User::where('username', $decoded['username'])->first();
        if (! $user) {
            return redirect()->route('login')
                ->with('error', 'Akun SSO Anda belum diselaraskan / belum terdaftar di database PKLv2.');
        }

        // Paksa Login User
        Auth::login($user, true);

        ActivityLog::log($user->id, 'Login via Internal SSO Central: '.$user->username);

        return redirect()->route($user->role->dashboardRoute())
            ->with('success', 'Berhasil login melalui integrasi Single Sign-On UBSI.');
    }
}
