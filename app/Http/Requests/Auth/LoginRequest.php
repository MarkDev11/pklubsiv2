<?php

namespace App\Http\Requests\Auth;

use App\Enums\UserRole;
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
        $turnstileRule = app()->environment(['local', 'testing']) && (bool) config('services.turnstile.bypass_local', false)
            ? ['nullable', 'string']
            : ['required', 'string'];

        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'role' => ['required', 'string', Rule::in(UserRole::values())],
            'cf-turnstile-response' => $turnstileRule,
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'NIM/NIP/Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'role.required' => 'Pilih peran terlebih dahulu.',
            'role.in' => 'Peran tidak valid.',
            'cf-turnstile-response.required' => 'CAPTCHA wajib diselesaikan.',
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

        if (! $this->verifyTurnstile()) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'cf-turnstile-response' => 'Verifikasi CAPTCHA gagal. Silakan coba lagi.',
            ]);
        }

        // Attempt login with username + password + role
        $credentials = [
            'username' => $this->username,
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

    protected function verifyTurnstile(): bool
    {
        if (app()->environment(['local', 'testing']) && (bool) config('services.turnstile.bypass_local', false)) {
            return true;
        }

        $secretKey = (string) config('services.turnstile.secret_key');
        $token = (string) $this->input('cf-turnstile-response');

        if ($secretKey === '' || $token === '') {
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
