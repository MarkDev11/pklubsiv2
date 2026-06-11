<?php

namespace Tests\Feature\Mahasiswa;

use App\Models\OpeningHour;
use App\Models\ProposalMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LaporanControllerTest extends TestCase
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

    public function test_mahasiswa_can_view_laporan_page(): void
    {
        $proposal = ProposalMahasiswa::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'nim' => $this->mahasiswa->username,
        ]);

        $response = $this->actingAs($this->mahasiswa)->get(route('mahasiswa.laporan.index'));

        $response->assertOk();
        $response->assertViewIs('mahasiswa.laporan');
    }

    public function test_mahasiswa_can_upload_laporan(): void
    {
        Storage::fake('public');

        $proposal = ProposalMahasiswa::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'nim' => $this->mahasiswa->username,
        ]);

        $lp = UploadedFile::fake()->create('laporan.pdf', 1000);
        $lpp = UploadedFile::fake()->create('penilaian.pdf', 1000);
        $skp = UploadedFile::fake()->create('surat.pdf', 1000);

        $response = $this->actingAs($this->mahasiswa)->post(route('mahasiswa.laporan.upload'), [
            'lp' => $lp,
            'lpp' => $lpp,
            'skp' => $skp,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('proposal_mahasiswas', [
            'id' => $proposal->id,
        ]);

        $proposal->refresh();
        $this->assertNotNull($proposal->lp);
        $this->assertNotNull($proposal->lpp);
        $this->assertNotNull($proposal->skp);
    }

    public function test_mahasiswa_upload_uses_nim_ownership_when_user_id_differs(): void
    {
        Storage::fake('public');

        $staleUser = User::factory()->mahasiswa()->create();
        $proposal = ProposalMahasiswa::factory()->create([
            'user_id' => $staleUser->id,
            'nim' => $this->mahasiswa->username,
        ]);

        $lp = UploadedFile::fake()->create('laporan.pdf', 1000);

        $response = $this->actingAs($this->mahasiswa)->post(route('mahasiswa.laporan.upload'), [
            'lp' => $lp,
        ]);

        $response->assertRedirect(route('mahasiswa.dashboard'));

        $proposal->refresh();
        $this->assertNotNull($proposal->lp);
    }

    public function test_mahasiswa_cannot_upload_empty_laporan_request(): void
    {
        ProposalMahasiswa::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'nim' => $this->mahasiswa->username,
        ]);

        $response = $this->actingAs($this->mahasiswa)->post(route('mahasiswa.laporan.upload'), []);

        $response->assertSessionHasErrors('lp');
    }

    public function test_mahasiswa_can_reset_laporan(): void
    {
        Storage::fake('public');

        $proposal = ProposalMahasiswa::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'nim' => $this->mahasiswa->username,
            'lp' => 'old_laporan.pdf',
            'lpp' => 'old_penilaian.pdf',
            'skp' => 'old_surat.pdf',
        ]);

        $response = $this->actingAs($this->mahasiswa)->post(route('mahasiswa.laporan.reset'));

        $response->assertRedirect();

        $proposal->refresh();
        $this->assertNull($proposal->lp);
        $this->assertNull($proposal->lpp);
        $this->assertNull($proposal->skp);
    }

    public function test_mahasiswa_cannot_reset_laporan_outside_window(): void
    {
        OpeningHour::query()->update([
            'open_laporan' => now()->subDays(30),
            'close_laporan' => now()->subDays(1),
        ]);

        $proposal = ProposalMahasiswa::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'nim' => $this->mahasiswa->username,
            'lp' => 'old_laporan.pdf',
            'lpp' => 'old_penilaian.pdf',
            'skp' => 'old_surat.pdf',
        ]);

        $response = $this->actingAs($this->mahasiswa)->post(route('mahasiswa.laporan.reset'));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $proposal->refresh();
        $this->assertSame('old_laporan.pdf', $proposal->lp);
        $this->assertSame('old_penilaian.pdf', $proposal->lpp);
        $this->assertSame('old_surat.pdf', $proposal->skp);
    }

    public function test_mahasiswa_cannot_upload_laporan_outside_window(): void
    {
        OpeningHour::query()->update([
            'open_laporan' => now()->subDays(30),
            'close_laporan' => now()->subDays(1),
        ]);

        $proposal = ProposalMahasiswa::factory()->create([
            'user_id' => $this->mahasiswa->id,
            'nim' => $this->mahasiswa->username,
        ]);

        Storage::fake('public');
        $lp = UploadedFile::fake()->create('laporan.pdf', 1000);

        $response = $this->actingAs($this->mahasiswa)->post(route('mahasiswa.laporan.upload'), [
            'lp' => $lp,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_non_owner_cannot_upload_laporan(): void
    {
        User::factory()->mahasiswa()->create();

        Storage::fake('public');
        $lp = UploadedFile::fake()->create('laporan.pdf', 1000);

        $response = $this->actingAs($this->mahasiswa)->post(route('mahasiswa.laporan.upload'), [
            'lp' => $lp,
        ]);

        $response->assertNotFound();
    }
}
