<?php

namespace Tests\Unit\Services;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = app(UserService::class);
    }

    public function test_creates_account_with_default_password(): void
    {
        $data = [
            'name' => 'Test User',
            'username' => '12345678',
            'role' => UserRole::Mahasiswa->value,
        ];

        $user = $this->userService->createAccount($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('12345678', $user->username);
        $this->assertEquals('12345678@bsi.ac.id', $user->email);
        $this->assertEquals(UserRole::Mahasiswa, $user->role);
        $this->assertNotNull($user->password);
    }

    public function test_creates_account_with_custom_password(): void
    {
        $data = [
            'name' => 'Test User',
            'username' => '12345678',
            'role' => UserRole::Mahasiswa->value,
            'password' => 'SecureP@ss123',
        ];

        $user = $this->userService->createAccount($data);

        $this->assertTrue(Hash::check('SecureP@ss123', $user->password));
    }

    public function test_sets_dosen_pa_for_mahasiswa(): void
    {
        $data = [
            'name' => 'Test Mahasiswa',
            'username' => '12345678',
            'role' => UserRole::Mahasiswa->value,
            'nama_dosen_pa' => 'DSNTEST001',
            'jenis' => 'Magang',
        ];

        $user = $this->userService->createAccount($data);

        $this->assertEquals('DSNTEST001', $user->nama_dosen_pa);
        $this->assertEquals('Magang', $user->jenis);
    }

    public function test_does_not_set_dosen_pa_for_non_mahasiswa(): void
    {
        $data = [
            'name' => 'Test Dosen',
            'username' => '87654321',
            'role' => UserRole::Dosen->value,
            'nama_dosen_pa' => 'Should Not Set',
            'jenis' => 'Should Not Set',
        ];

        $user = $this->userService->createAccount($data);

        $this->assertNull($user->nama_dosen_pa);
        $this->assertNull($user->jenis);
    }

    public function test_generates_secure_password(): void
    {
        $password = $this->userService->generateSecurePassword();

        $this->assertEquals(12, strlen($password));
        $this->assertMatchesRegularExpression('/[a-zA-Z]/', $password);
        $this->assertMatchesRegularExpression('/[0-9]/', $password);
    }

    public function test_updates_account(): void
    {
        $user = User::factory()->mahasiswa()->create();

        $this->userService->updateAccount($user, [
            'name' => 'Updated Name',
        ]);

        $this->assertEquals('Updated Name', $user->fresh()->name);
    }

    public function test_deletes_account(): void
    {
        $user = User::factory()->mahasiswa()->create();

        $this->userService->deleteAccount($user);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_cannot_delete_admin_account(): void
    {
        $admin = User::factory()->admin()->create();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Akun admin tidak dapat dihapus.');

        $this->userService->deleteAccount($admin);
    }

    public function test_resets_password(): void
    {
        $user = User::factory()->mahasiswa()->create();

        $this->userService->resetPassword($user, 'NewSecureP@ss123');

        $this->assertTrue(Hash::check('NewSecureP@ss123', $user->fresh()->password));
    }
}
