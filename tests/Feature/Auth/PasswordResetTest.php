<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertOk();
        $response->assertViewIs('auth.forgot-password');
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->post('/forgot-password', ['email' => $user->email]);

        $response->assertRedirect('/forgot-password/verify');
        $response->assertSessionHas('reset_email', $user->email);
    }

    public function test_otp_verify_screen_can_be_rendered(): void
    {
        $response = $this->withSession(['reset_email' => 'test@example.com'])
            ->get('/forgot-password/verify');

        $response->assertOk();
        $response->assertViewIs('auth.otp-reset');
    }

    public function test_otp_verify_screen_redirects_without_session(): void
    {
        $response = $this->get('/forgot-password/verify');

        $response->assertRedirect('/forgot-password');
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        $response = $this->withSession([
            'reset_token' => 'test-token',
            'reset_email' => 'test@example.com',
        ])->get('/reset-password');

        $response->assertOk();
        $response->assertViewIs('auth.reset-password');
    }

    public function test_reset_password_screen_redirects_without_session(): void
    {
        $response = $this->get('/reset-password');

        $response->assertRedirect('/forgot-password');
    }

    public function test_password_can_be_reset_with_valid_session(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $token = 'test-token-123';

        $response = $this->withSession([
            'reset_token' => $token,
            'reset_email' => $user->email,
        ])->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect('/login');
        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_password_reset_fails_with_invalid_token(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->withSession([
            'reset_token' => 'correct-token',
            'reset_email' => $user->email,
        ])->post('/reset-password', [
            'token' => 'wrong-token',
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect('/forgot-password');
    }
}
