<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\ProposalMahasiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
        // Create a dosen first for mahasiswa Dosen PA reference
        /** @var User $dosen */
        $dosen = User::factory()->dosen()->create();

        $response = $this->actingAs($this->admin)->post(route('admin.akun.store'), [
            'name' => 'Test User',
            'username' => '12345678',
            'role' => 'mahasiswa',
            'password' => 'SecureP@ss1',
            'nama_dosen_pa' => $dosen->username,
        ]);

        $response->assertRedirect(route('admin.akun.index'));
        $this->assertDatabaseHas('users', ['username' => '12345678']);
    }

    public function test_admin_can_create_akun_with_username_as_default_password_when_confirmed(): void
    {
        /** @var User $dosen */
        $dosen = User::factory()->dosen()->create();

        $response = $this->actingAs($this->admin)->post(route('admin.akun.store'), [
            'name' => 'Default Password User',
            'username' => '87654321',
            'role' => 'mahasiswa',
            'password' => '',
            'password_default_confirm' => '1',
            'nama_dosen_pa' => $dosen->username,
        ]);

        $response->assertRedirect(route('admin.akun.index'));

        $user = User::where('username', '87654321')->firstOrFail();
        $this->assertTrue(Hash::check('87654321', $user->password));
    }

    public function test_admin_must_confirm_default_password_when_password_empty(): void
    {
        /** @var User $dosen */
        $dosen = User::factory()->dosen()->create();

        $response = $this->actingAs($this->admin)->post(route('admin.akun.store'), [
            'name' => 'Default Password User',
            'username' => '87654322',
            'role' => 'mahasiswa',
            'password' => '',
            'nama_dosen_pa' => $dosen->username,
        ]);

        $response->assertSessionHasErrors('password_default_confirm');
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

    public function test_admin_cannot_delete_user_with_proposal_data(): void
    {
        /** @var User $target */
        $target = User::factory()->mahasiswa()->create();
        ProposalMahasiswa::factory()->create([
            'user_id' => $target->id,
            'nim' => $target->username,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.akun.destroy', encryptUrl($target->username)));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $target->id]);
    }

    public function test_admin_cannot_change_role_when_username_has_relations(): void
    {
        /** @var User $dosen */
        $dosen = User::factory()->dosen()->create();
        User::factory()->mahasiswa()->create([
            'nama_dosen_pa' => $dosen->username,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.akun.update', encryptUrl($dosen->username)), [
            'name' => $dosen->name,
            'username' => $dosen->username,
            'role' => 'mentor',
        ]);

        $response->assertSessionHasErrors('role');
    }

    public function test_guest_cannot_access_admin_routes(): void
    {
        $response = $this->get(route('admin.akun.index'));
        $response->assertRedirect(route('login'));
    }
}
