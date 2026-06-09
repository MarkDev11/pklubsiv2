<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\MathCaptchaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function __construct(protected MathCaptchaService $mathCaptcha) {}

    /**
     * Tampilkan halaman login.
     */
    public function create(): View
    {
        return view('auth.login', $this->loginViewData());
    }

    /**
     * Handle login request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        if (! $user instanceof User) {
            return redirect()->route('login')->with('status', 'Silakan login kembali.');
        }

        // Log activity
        ActivityLog::log($user->id, 'Login '.$user->username);

        // Redirect berdasarkan role
        return $this->redirectByRole($user->role->value);
    }

    /**
     * Tampilkan halaman login admin.
     */
    public function createAdmin(): View
    {
        return view('auth.login-admin', $this->loginViewData());
    }

    /**
     * Logout.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user instanceof User) {
            ActivityLog::log($user->id, 'Logout '.$user->username);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', 'Anda telah berhasil keluar.');
    }

    /**
     * Redirect berdasarkan role user.
     */
    protected function redirectByRole(string $role): RedirectResponse
    {
        $enumRole = UserRole::tryFrom($role);

        if ($enumRole) {
            return redirect()->route($enumRole->dashboardRoute());
        }

        return redirect()->route('login');
    }

    /**
     * @return array<string, mixed>
     */
    protected function loginViewData(): array
    {
        $bypassLocal = app()->environment(['local', 'testing']) && (bool) config('services.turnstile.bypass_local', false);

        return [
            'turnstileSiteKey' => config('services.turnstile.site_key'),
            'turnstileBypassLocal' => $bypassLocal,
            'mathCaptcha' => $bypassLocal ? null : $this->mathCaptcha->generate(),
        ];
    }
}
