<?php

namespace App\Http\Requests\Auth;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\MathCaptchaService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'role' => ['required', 'string', Rule::in(UserRole::values())],
            'cf-turnstile-response' => ['nullable', 'string'],
            'math_captcha_answer' => ['nullable', 'string', 'max:5'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'NIM/NIP/Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'role.required' => 'Pilih peran terlebih dahulu.',
            'role.in' => 'Peran tidak valid.',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! $this->verifyCaptcha()) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'cf-turnstile-response' => 'Verifikasi CAPTCHA gagal. Silakan coba lagi.',
            ]);
        }

        // Determine if input is email or username
        $input = $this->username;
        $username = $input;

        // If input is email format, find the username from database
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $input)
                ->orWhere('email_bsi', $input)
                ->first();

            if (! $user) {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'username' => 'Email tidak terdaftar dalam sistem.',
                ]);
            }

            // Check role match
            if ($user->role->value !== $this->role) {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'username' => 'Akun dengan email ini memiliki peran yang berbeda. Silakan pilih peran yang sesuai.',
                ]);
            }

            $username = $user->username;
        }

        // Attempt login with username + password + role
        $credentials = [
            'username' => $username,
            'password' => $this->password,
            'role' => $this->role,
        ];

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'username' => 'Username, password, atau peran salah.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('username')).'|'.$this->ip());
    }

    /**
     * Verify either the Turnstile token (preferred) or the math captcha
     * fallback. Local/testing bypass still applies.
     */
    protected function verifyCaptcha(): bool
    {
        if (app()->environment(['local', 'testing']) && (bool) config('services.turnstile.bypass_local', false)) {
            return true;
        }

        $turnstileToken = (string) $this->input('cf-turnstile-response');
        if ($turnstileToken !== '') {
            return $this->verifyTurnstile($turnstileToken);
        }

        $mathAnswer = $this->input('math_captcha_answer');
        if ($mathAnswer !== null && $mathAnswer !== '') {
            return app(MathCaptchaService::class)->verify((string) $mathAnswer);
        }

        return false;
    }

    protected function verifyTurnstile(string $token): bool
    {
        $secretKey = (string) config('services.turnstile.secret_key');

        if ($secretKey === '') {
            return false;
        }

        $response = Http::asForm()->timeout(10)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => $secretKey,
            'response' => $token,
            'remoteip' => $this->ip(),
        ]);

        if (! $response->ok()) {
            return false;
        }

        return (bool) $response->json('success', false);
    }
}
