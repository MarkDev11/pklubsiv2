<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AkunControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var User $admin */
        $admin = User::factory()->admin()->create();
        $this->admin = $admin;
    }

    public function test_admin_can_view_akun_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.akun.index'));
        $response->assertOk();
    }

    public function test_non_admin_cannot_access_akun(): void
    {
        /** @var User $mahasiswa */
        $mahasiswa = User::factory()->mahasiswa()->create();
        $response = $this->actingAs($mahasiswa)->get(route('admin.akun.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_create_akun(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.akun.store'), [
            'name' => 'Test User',
            'username' => '12345678',
            'role' => 'mahasiswa',
            'password' => 'SecureP@ss1',
        ]);

        $response->assertRedirect(route('admin.akun.index'));
        $this->assertDatabaseHas('users', ['username' => '12345678']);
    }

    public function test_admin_can_delete_non_admin_akun(): void
    {
        /** @var User $target */
        $target = User::factory()->mahasiswa()->create();
        $encrypted = encryptUrl($target->username);

        $response = $this->actingAs($this->admin)->delete(route('admin.akun.destroy', $encrypted));
        $response->assertRedirect(route('admin.akun.index'));
        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_admin_cannot_delete_admin_akun(): void
    {
        /** @var User $otherAdmin */
        $otherAdmin = User::factory()->admin()->create();
        $encrypted = encryptUrl($otherAdmin->username);

        $response = $this->actingAs($this->admin)->delete(route('admin.akun.destroy', $encrypted));
        $response->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $otherAdmin->id]);
    }

    public function test_guest_cannot_access_admin_routes(): void
    {
        $response = $this->get(route('admin.akun.index'));
        $response->assertRedirect(route('login'));
    }
}
