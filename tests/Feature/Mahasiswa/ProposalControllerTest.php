<?php

namespace Tests\Feature\Mahasiswa;

use App\Models\OpeningHour;
use App\Models\ProposalMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProposalControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $mahasiswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mahasiswa = User::factory()->mahasiswa()->create();

        OpeningHour::create([
            'open_time' => now()->subDays(7),
            'close_time' => now()->addDays(7),
            'open_laporan' => now()->subDays(7),
            'close_laporan' => now()->addDays(7),
            'open_nilai' => now()->subDays(7),
            'close_nilai' => now()->addDays(7),
        ]);
    }

    public function test_mahasiswa_can_view_proposal_page(): void
    {
        $response = $this->actingAs($this->mahasiswa)->get(route('mahasiswa.proposal.index'));

        $response->assertOk();
        $response->assertViewIs('mahasiswa.proposal');
    }

    public function test_mahasiswa_can_create_proposal(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('skm.pdf', 1000);

        $response = $this->actingAs($this->mahasiswa)->post(route('mahasiswa.proposal.store'), [
            'nim' => $this->mahasiswa->username,
            'nama' => $this->mahasiswa->name,
            'jns_pkl' => 'Magang',
            'judul_pkl' => 'Test Proposal Title',
            'tempat_riset' => 'Test Company',
            'nama_mentor' => 'Test Mentor',
            'hp_mentor' => '08123456789',
            'email_mentor' => 'mentor@test.com',
            'skm' => $file,
        ]);

        $response->assertRedirect(route('mahasiswa.proposal.index'));
        $this->assertDatabaseHas('proposal_mahasiswas', [
            'nim' => $this->mahasiswa->username,
            'judul_pkl' => 'Test Proposal Title',
        ]);
    }

    public function test_mahasiswa_can_update_proposal(): void
    {
        $this->withoutExceptionHandling();
        Storage::fake('public');

        $proposal = ProposalMahasiswa::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'nim' => $this->mahasiswa->username,
        ]);

        $file = UploadedFile::fake()->create('skm_new.pdf', 1000);

        $response = $this->actingAs($this->mahasiswa)->put(route('mahasiswa.proposal.update'), [
            'nim' => $this->mahasiswa->username,
            'nama' => $this->mahasiswa->name,
            'jns_pkl' => 'Magang',
            'judul_pkl' => 'Updated Title',
            'tempat_riset' => 'Updated Company',
            'nama_mentor' => 'Updated Mentor',
            'hp_mentor' => '08123456789',
            'email_mentor' => 'updated@test.com',
            'skm' => $file,
        ]);

        $response->assertRedirect(route('mahasiswa.proposal.index'));
        $this->assertDatabaseHas('proposal_mahasiswas', [
            'id' => $proposal->id,
            'judul_pkl' => 'Updated Title',
        ]);
    }

    public function test_mahasiswa_cannot_update_other_proposal(): void
    {
        $otherMahasiswa = User::factory()->mahasiswa()->create();
        ProposalMahasiswa::factory()->create([
            'user_id' => $otherMahasiswa->id,
            'nim' => $otherMahasiswa->username,
        ]);

        $response = $this->actingAs($this->mahasiswa)->put(route('mahasiswa.proposal.update'), [
            'nim' => $this->mahasiswa->username,
            'nama' => $this->mahasiswa->name,
            'jns_pkl' => 'Magang',
            'judul_pkl' => 'Updated Title',
            'tempat_riset' => 'Updated Company',
            'nama_mentor' => 'Updated Mentor',
            'hp_mentor' => '08123456789',
            'email_mentor' => 'updated@test.com',
        ]);

        $response->assertNotFound();
    }

    public function test_proposal_submission_closed_outside_window(): void
    {
        OpeningHour::query()->update([
            'open_time' => now()->subDays(30),
            'close_time' => now()->subDays(1),
        ]);

        Storage::fake('public');
        $file = UploadedFile::fake()->create('skm.pdf', 1000);

        $response = $this->actingAs($this->mahasiswa)->post(route('mahasiswa.proposal.store'), [
            'nim' => $this->mahasiswa->username,
            'nama' => $this->mahasiswa->name,
            'jns_pkl' => 'Magang',
            'judul_pkl' => 'Test Proposal Title',
            'tempat_riset' => 'Test Company',
            'nama_mentor' => 'Test Mentor',
            'hp_mentor' => '08123456789',
            'email_mentor' => 'mentor@test.com',
            'skm' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_guest_cannot_access_proposal(): void
    {
        $response = $this->get(route('mahasiswa.proposal.index'));

        $response->assertRedirect(route('login'));
    }
}
