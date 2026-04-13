<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->actingAs($user)->get('/verify-email')->assertNotFound();
    }

    public function test_email_can_be_verified(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->actingAs($user)->get('/verify-email')->assertNotFound();
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->actingAs($user)->get('/verify-email')->assertNotFound();

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
