<?php

namespace Tests\Feature;

use App\Models\ProposalMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DosenPaNipTest extends TestCase
{
    use RefreshDatabase;

    public function test_duplicate_dosen_names_are_isolated_by_username(): void
    {
        User::factory()->dosen()->create(['name' => 'Dr. Siti Pratama', 'username' => 'DSN000014']);
        User::factory()->dosen()->create(['name' => 'Dr. Siti Pratama', 'username' => 'DSN000122']);

        $firstStudent = User::factory()->mahasiswa()->create(['nama_dosen_pa' => 'DSN000014']);
        $secondStudent = User::factory()->mahasiswa()->create(['nama_dosen_pa' => 'DSN000122']);

        ProposalMahasiswa::factory()->magang()->create([
            'user_id' => $firstStudent->id,
            'nim' => $firstStudent->username,
            'dosen_pa' => 'DSN000014',
        ]);

        ProposalMahasiswa::factory()->magang()->create([
            'user_id' => $secondStudent->id,
            'nim' => $secondStudent->username,
            'dosen_pa' => 'DSN000122',
        ]);

        $this->assertSame(1, ProposalMahasiswa::byDosen('DSN000014')->count());
        $this->assertSame(1, ProposalMahasiswa::byDosen('DSN000122')->count());
    }

    public function test_backfill_dry_run_does_not_write_and_write_mode_updates_unique_names(): void
    {
        User::factory()->dosen()->create(['name' => 'Dr. Unique', 'username' => 'DSNUNIQUE']);
        User::factory()->dosen()->create(['name' => 'Dr. Duplicate', 'username' => 'DSNDUP001']);
        User::factory()->dosen()->create(['name' => 'Dr. Duplicate', 'username' => 'DSNDUP002']);

        $uniqueStudent = User::factory()->mahasiswa()->create(['nama_dosen_pa' => 'Dr. Unique']);
        $duplicateStudent = User::factory()->mahasiswa()->create(['nama_dosen_pa' => 'Dr. Duplicate']);

        ProposalMahasiswa::factory()->create([
            'user_id' => $uniqueStudent->id,
            'nim' => $uniqueStudent->username,
            'dosen_pa' => 'Dr. Unique',
        ]);

        ProposalMahasiswa::factory()->create([
            'user_id' => $duplicateStudent->id,
            'nim' => $duplicateStudent->username,
            'dosen_pa' => 'Dr. Duplicate',
        ]);

        $this->artisan('pkl:backfill-dosen-pa-nip --dry-run')->assertExitCode(0);

        $this->assertDatabaseHas('users', ['id' => $uniqueStudent->id, 'nama_dosen_pa' => 'Dr. Unique']);
        $this->assertDatabaseHas('proposal_mahasiswas', ['nim' => $uniqueStudent->username, 'dosen_pa' => 'Dr. Unique']);

        $this->artisan('pkl:backfill-dosen-pa-nip')->assertExitCode(0);

        $this->assertDatabaseHas('users', ['id' => $uniqueStudent->id, 'nama_dosen_pa' => 'DSNUNIQUE']);
        $this->assertDatabaseHas('proposal_mahasiswas', ['nim' => $uniqueStudent->username, 'dosen_pa' => 'DSNUNIQUE']);
        $this->assertDatabaseHas('users', ['id' => $duplicateStudent->id, 'nama_dosen_pa' => 'Dr. Duplicate']);
        $this->assertDatabaseHas('proposal_mahasiswas', ['nim' => $duplicateStudent->username, 'dosen_pa' => 'Dr. Duplicate']);
    }

    public function test_proposal_submit_rejects_invalid_dosen_pa_nip(): void
    {
        Storage::fake('public');
        $student = User::factory()->mahasiswa()->create(['nama_dosen_pa' => 'MISSINGNIP']);

        $response = $this->actingAs($student)->post(route('mahasiswa.proposal.store'), [
            'nim' => $student->username,
            'nama' => $student->name,
            'kd_lokal' => $student->kd_lokal,
            'jns_pkl' => 'Magang',
            'judul_pkl' => 'Sistem Informasi PKL',
            'tempat_riset' => 'PT Test',
            'nama_mentor' => 'Mentor Test',
            'hp_mentor' => '081234567890',
            'email_mentor' => 'mentor@test.com',
            'email_perusahaan' => 'hr@test.com',
            'skm' => UploadedFile::fake()->create('skm.pdf', 100, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('dosen_pa');
        $this->assertDatabaseCount('proposal_mahasiswas', 0);
    }

    public function test_admin_cannot_change_referenced_dosen_username(): void
    {
        $admin = User::factory()->admin()->create();
        $dosen = User::factory()->dosen()->create(['username' => 'DSNLOCKED']);
        User::factory()->mahasiswa()->create(['nama_dosen_pa' => 'DSNLOCKED']);

        $response = $this->actingAs($admin)->put(route('admin.akun.update', encryptUrl($dosen->username)), [
            'name' => $dosen->name,
            'username' => 'DSNNEW001',
            'role' => 'dosen',
            'jenis' => null,
            'nama_dosen_pa' => null,
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertDatabaseHas('users', ['id' => $dosen->id, 'username' => 'DSNLOCKED']);
    }
}
