<?php

namespace Tests\Feature;

use App\Models\ProposalMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_admin_can_access_any_file(): void
    {
        $admin = User::factory()->admin()->create();
        $proposal = ProposalMahasiswa::factory()->create(['lp' => 'test_laporan.pdf']);

        Storage::disk('public')->put('uploads/test_laporan.pdf', 'content');

        $response = $this->actingAs($admin)->get(route('files.serve', 'test_laporan.pdf'));

        $response->assertOk();
    }

    public function test_mahasiswa_can_access_own_files(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();
        $proposal = ProposalMahasiswa::factory()->create([
            'user_id' => $mahasiswa->id,
            'nim' => $mahasiswa->username,
            'lp' => 'own_laporan.pdf',
        ]);

        Storage::disk('public')->put('uploads/own_laporan.pdf', 'content');

        $response = $this->actingAs($mahasiswa)->get(route('files.serve', 'own_laporan.pdf'));

        $response->assertOk();
    }

    public function test_mahasiswa_cannot_access_others_files(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();
        $other = User::factory()->mahasiswa()->create();
        $proposal = ProposalMahasiswa::factory()->create([
            'user_id' => $other->id,
            'nim' => $other->username,
            'lp' => 'other_laporan.pdf',
        ]);

        Storage::disk('public')->put('uploads/other_laporan.pdf', 'content');

        $response = $this->actingAs($mahasiswa)->get(route('files.serve', 'other_laporan.pdf'));

        $response->assertForbidden();
    }

    public function test_dosen_can_access_their_students_files(): void
    {
        $dosen = User::factory()->dosen()->create(['name' => 'Dr. Test']);
        $mahasiswa = User::factory()->mahasiswa()->create(['nama_dosen_pa' => 'Dr. Test']);
        $proposal = ProposalMahasiswa::factory()->create([
            'user_id' => $mahasiswa->id,
            'nim' => $mahasiswa->username,
            'lp' => 'student_laporan.pdf',
        ]);

        Storage::disk('public')->put('uploads/student_laporan.pdf', 'content');

        $response = $this->actingAs($dosen)->get(route('files.serve', 'student_laporan.pdf'));

        $response->assertOk();
    }

    public function test_dosen_cannot_access_non_student_files(): void
    {
        $dosen = User::factory()->dosen()->create(['name' => 'Dr. Test']);
        $mahasiswa = User::factory()->mahasiswa()->create(['nama_dosen_pa' => 'Dr. Other']);
        $proposal = ProposalMahasiswa::factory()->create([
            'user_id' => $mahasiswa->id,
            'nim' => $mahasiswa->username,
            'lp' => 'non_student_laporan.pdf',
        ]);

        Storage::disk('public')->put('uploads/non_student_laporan.pdf', 'content');

        $response = $this->actingAs($dosen)->get(route('files.serve', 'non_student_laporan.pdf'));

        $response->assertForbidden();
    }

    public function test_mentor_can_access_their_mentee_files(): void
    {
        $mentor = User::factory()->mentor()->create(['username' => 'mentor@test.com']);
        $proposal = ProposalMahasiswa::factory()->create([
            'email_mentor' => 'mentor@test.com',
            'lp' => 'mentee_laporan.pdf',
        ]);

        Storage::disk('public')->put('uploads/mentee_laporan.pdf', 'content');

        $response = $this->actingAs($mentor)->get(route('files.serve', 'mentee_laporan.pdf'));

        $response->assertOk();
    }

    public function test_blocks_path_traversal_attack(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('files.serve', '../../../etc/passwd'));

        // Could be 403 (blocked) or 404 (route param encoding)
        $this->assertContains($response->status(), [403, 404]);
    }

    public function test_blocks_slash_in_filename(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('files.serve', 'path/to/file.pdf'));

        // Could be 403 (blocked) or 404 (route param encoding)
        $this->assertContains($response->status(), [403, 404]);
    }

    public function test_returns_404_for_nonexistent_file(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('files.serve', 'nonexistent.pdf'));

        $response->assertNotFound();
    }

    public function test_guest_cannot_access_files(): void
    {
        Storage::disk('public')->put('uploads/test.pdf', 'content');

        $response = $this->get(route('files.serve', 'test.pdf'));

        // Guest is redirected to login (302) or forbidden (403)
        $this->assertContains($response->status(), [302, 403]);
    }
}
