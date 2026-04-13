<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOtpVerified
{
    /**
     * Pastikan user sudah verifikasi OTP sebelum akses halaman.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user instanceof User && ! $user->otp_verified) {
            return redirect()->route('otp.show');
        }

        return $next($request);
    }
}
