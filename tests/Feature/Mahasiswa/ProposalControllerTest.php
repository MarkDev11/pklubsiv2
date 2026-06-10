<?php

namespace Tests\Feature\Mahasiswa;

use App\Models\OpeningHour;
use App\Models\ProposalMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProposalControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $mahasiswa;
    protected User $dosen;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create dosen first for valid Dosen PA reference
        $this->dosen = User::factory()->dosen()->create();
        
        $this->mahasiswa = User::factory()->mahasiswa()->create([
            'nama_dosen_pa' => $this->dosen->username,
        ]);

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

    public function test_update_proposal_creates_new_mentor_account_when_email_changes(): void
    {
        $oldMentor = User::factory()->mentor()->create([
            'name' => 'Old Mentor',
            'username' => 'old-mentor@test.com',
            'email' => 'old-mentor@test.com',
            'phone' => '08111111111',
        ]);

        $proposal = ProposalMahasiswa::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'nim' => $this->mahasiswa->username,
            'nama_mentor' => $oldMentor->name,
            'hp_mentor' => $oldMentor->phone,
            'email_mentor' => $oldMentor->username,
        ]);

        $response = $this->actingAs($this->mahasiswa)->put(route('mahasiswa.proposal.update'), [
            'nim' => $this->mahasiswa->username,
            'nama' => $this->mahasiswa->name,
            'jns_pkl' => 'Magang',
            'judul_pkl' => 'Updated Title',
            'tempat_riset' => 'Updated Company',
            'nama_mentor' => 'New Mentor',
            'hp_mentor' => '08222222222',
            'email_mentor' => 'new-mentor@test.com',
        ]);

        $response->assertRedirect(route('mahasiswa.proposal.index'));

        $this->assertDatabaseHas('proposal_mahasiswas', [
            'id' => $proposal->id,
            'email_mentor' => 'new-mentor@test.com',
            'nama_mentor' => 'New Mentor',
            'hp_mentor' => '08222222222',
        ]);

        $this->assertDatabaseHas('users', [
            'username' => 'old-mentor@test.com',
            'role' => 'mentor',
        ]);

        $newMentor = User::where('username', 'new-mentor@test.com')->firstOrFail();
        $this->assertTrue($newMentor->isMentor());
        $this->assertSame('New Mentor', $newMentor->name);
        $this->assertSame('08222222222', $newMentor->phone);
        $this->assertTrue(Hash::check('08222222222', $newMentor->password));
    }

    public function test_update_proposal_syncs_existing_mentor_name_and_phone_without_changing_password(): void
    {
        $mentor = User::factory()->mentor()->create([
            'name' => 'Old Mentor Name',
            'username' => 'mentor-existing@test.com',
            'email' => 'mentor-existing@test.com',
            'password' => 'old-password',
            'phone' => '08111111111',
        ]);

        ProposalMahasiswa::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'nim' => $this->mahasiswa->username,
            'email_mentor' => $mentor->username,
        ]);

        $response = $this->actingAs($this->mahasiswa)->put(route('mahasiswa.proposal.update'), [
            'nim' => $this->mahasiswa->username,
            'nama' => $this->mahasiswa->name,
            'jns_pkl' => 'Magang',
            'judul_pkl' => 'Updated Title',
            'tempat_riset' => 'Updated Company',
            'nama_mentor' => 'Correct Mentor Name',
            'hp_mentor' => '08333333333',
            'email_mentor' => 'mentor-existing@test.com',
        ]);

        $response->assertRedirect(route('mahasiswa.proposal.index'));

        $mentor->refresh();
        $this->assertSame('Correct Mentor Name', $mentor->name);
        $this->assertSame('08333333333', $mentor->phone);
        $this->assertTrue(Hash::check('old-password', $mentor->password));
    }

    public function test_update_proposal_rejects_mentor_email_used_by_non_mentor(): void
    {
        $nonMentor = User::factory()->dosen()->create([
            'username' => 'not-mentor@test.com',
            'email' => 'not-mentor@test.com',
        ]);

        $proposal = ProposalMahasiswa::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'nim' => $this->mahasiswa->username,
            'email_mentor' => 'mentor@test.com',
        ]);

        $response = $this->actingAs($this->mahasiswa)->put(route('mahasiswa.proposal.update'), [
            'nim' => $this->mahasiswa->username,
            'nama' => $this->mahasiswa->name,
            'jns_pkl' => 'Magang',
            'judul_pkl' => 'Updated Title',
            'tempat_riset' => 'Updated Company',
            'nama_mentor' => 'Invalid Mentor',
            'hp_mentor' => '08444444444',
            'email_mentor' => $nonMentor->username,
        ]);

        $response->assertSessionHasErrors('email_mentor');

        $this->assertDatabaseHas('proposal_mahasiswas', [
            'id' => $proposal->id,
            'email_mentor' => 'mentor@test.com',
        ]);
    }

    public function test_mahasiswa_cannot_update_other_proposal(): void
    {
        $otherMahasiswa = User::factory()->mahasiswa()->create([
            'nama_dosen_pa' => $this->dosen->username,
        ]);
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
