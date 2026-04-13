<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthenticationRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_is_rate_limited(): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true], 200),
        ]);

        $response = $this->post('/login', [
            'username' => 'testuser',
            'password' => 'wrongpassword',
            'role' => 'mahasiswa',
            'cf-turnstile-response' => 'token-valid',
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'username' => 'testuser',
                'password' => 'wrongpassword',
                'role' => 'mahasiswa',
                'cf-turnstile-response' => 'token-valid',
            ]);
        }

        $response = $this->post('/login', [
            'username' => 'testuser',
            'password' => 'wrongpassword',
            'role' => 'mahasiswa',
            'cf-turnstile-response' => 'token-valid',
        ]);

        $response->assertStatus(429);
    }

    public function test_admin_login_is_rate_limited(): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true], 200),
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/login', [
                'username' => 'admin',
                'password' => 'wrongpassword',
                'role' => 'admin',
                'cf-turnstile-response' => 'token-valid',
            ]);
        }

        $response = $this->post('/admin/login', [
            'username' => 'admin',
            'password' => 'wrongpassword',
            'role' => 'admin',
            'cf-turnstile-response' => 'token-valid',
        ]);

        $response->assertStatus(429);
    }

    public function test_successful_login_resets_rate_limit(): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true], 200),
        ]);

        $user = User::factory()->mahasiswa()->create([
            'password' => bcrypt('password123'),
        ]);

        for ($i = 0; $i < 3; $i++) {
            $this->post('/login', [
                'username' => 'wronguser',
                'password' => 'wrongpassword',
                'role' => 'mahasiswa',
                'cf-turnstile-response' => 'token-valid',
            ]);
        }

        $response = $this->post('/login', [
            'username' => $user->username,
            'password' => 'password123',
            'role' => 'mahasiswa',
            'cf-turnstile-response' => 'token-valid',
        ]);

        $response->assertRedirect();
    }
}
