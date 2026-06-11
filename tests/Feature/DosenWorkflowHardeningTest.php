<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Export;
use App\Models\ProposalMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DosenWorkflowHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_dosen_sensitive_routes_are_rate_limited(): void
    {
        $this->assertContains('throttle:60,1', Route::getRoutes()->getByName('dosen.nilai.save')->middleware());
        $this->assertContains('throttle:5,1', Route::getRoutes()->getByName('dosen.exports.pkl')->middleware());
        $this->assertContains('throttle:5,1', Route::getRoutes()->getByName('dosen.exports.msib')->middleware());
    }

    public function test_dosen_cannot_save_nilai_for_other_dosen_student(): void
    {
        $dosen = User::factory()->dosen()->create(['username' => 'DSN000001']);
        $otherDosen = User::factory()->dosen()->create(['username' => 'DSN000002']);
        $mahasiswa = User::factory()->mahasiswa()->create(['nama_dosen_pa' => $otherDosen->username]);
        $proposal = ProposalMahasiswa::factory()->magang()->create([
            'user_id' => $mahasiswa->id,
            'nilai' => 0,
        ]);

        $this->actingAs($dosen)
            ->postJson(route('dosen.nilai.save'), [
                'form_id' => [$proposal->id],
                'nilai' => [88],
            ])
            ->assertForbidden();

        $this->assertSame(0, $proposal->fresh()->nilai);
    }

    public function test_dosen_export_csv_escapes_formula_values(): void
    {
        Storage::fake('local');

        $dosen = User::factory()->dosen()->create(['username' => 'DSN000001']);
        $mahasiswa = User::factory()->mahasiswa()->create(['nama_dosen_pa' => $dosen->username]);

        ProposalMahasiswa::factory()->magang()->create([
            'user_id' => $mahasiswa->id,
            'nim' => '12345678',
            'nama' => '=Bad Student',
            'tempat_riset' => '+Bad Company',
            'nama_mentor' => '@Bad Mentor',
            'penilai' => '-Bad Penilai',
        ]);

        $this->actingAs($dosen)
            ->from(route('dosen.nilai.pkl'))
            ->post(route('dosen.exports.pkl'), ['type' => 'excel'])
            ->assertRedirect(route('dosen.nilai.pkl'));

        $export = Export::firstOrFail();
        $csv = Storage::disk('local')->get($export->path);

        $this->assertStringContainsString("'=Bad Student", $csv);
        $this->assertStringContainsString("'+Bad Company", $csv);
        $this->assertStringContainsString("'@Bad Mentor", $csv);
        $this->assertStringContainsString("'-Bad Penilai", $csv);
    }

    public function test_dosen_export_reuses_existing_valid_export(): void
    {
        Storage::fake('local');

        $dosen = User::factory()->dosen()->create(['username' => 'DSN000001']);
        $mahasiswa = User::factory()->mahasiswa()->create(['nama_dosen_pa' => $dosen->username]);
        ProposalMahasiswa::factory()->magang()->create(['user_id' => $mahasiswa->id]);

        $existing = Export::create([
            'user_id' => $dosen->id,
            'type' => 'excel',
            'category' => 'pkl',
            'total_records' => 1,
            'processed_records' => 1,
            'status' => 'completed',
            'filename' => 'existing.csv',
            'path' => 'exports/existing.csv',
            'expires_at' => now()->addMinutes(30),
        ]);
        Storage::put($existing->path, 'existing-content');

        $this->actingAs($dosen)
            ->from(route('dosen.nilai.pkl'))
            ->post(route('dosen.exports.pkl'), ['type' => 'excel'])
            ->assertRedirect(route('dosen.nilai.pkl'));

        $this->assertSame(1, Export::count());
        $this->assertSame('existing.csv', Export::firstOrFail()->filename);
    }

    public function test_dosen_export_rejects_more_than_one_thousand_records(): void
    {
        Storage::fake('local');

        $dosen = User::factory()->dosen()->create(['username' => 'DSN000001']);
        $now = now();
        $users = [];
        $proposals = [];

        for ($i = 1; $i <= 1001; $i++) {
            $nim = str_pad((string) $i, 8, '0', STR_PAD_LEFT);
            $users[] = [
                'name' => "Student {$i}",
                'username' => $nim,
                'email' => "student{$i}@example.test",
                'password' => 'password',
                'role' => UserRole::Mahasiswa->value,
                'nama_dosen_pa' => $dosen->username,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($users, 200) as $chunk) {
            DB::table('users')->insert($chunk);
        }

        $mahasiswaIds = User::mahasiswa()->where('nama_dosen_pa', $dosen->username)->pluck('id', 'username');

        foreach ($mahasiswaIds as $nim => $id) {
            $proposals[] = [
                'user_id' => $id,
                'nim' => $nim,
                'nama' => "Student {$nim}",
                'jns_pkl' => 'Magang',
                'judul_pkl' => 'Judul PKL',
                'tempat_riset' => 'Company',
                'nama_mentor' => 'Mentor',
                'dosen_pa' => $dosen->username,
                'nilai' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($proposals, 200) as $chunk) {
            DB::table('proposal_mahasiswas')->insert($chunk);
        }

        $this->actingAs($dosen)
            ->from(route('dosen.nilai.pkl'))
            ->post(route('dosen.exports.pkl'), ['type' => 'excel'])
            ->assertRedirect(route('dosen.nilai.pkl'))
            ->assertSessionHasErrors('type');

        $this->assertSame(0, Export::count());
    }
}
