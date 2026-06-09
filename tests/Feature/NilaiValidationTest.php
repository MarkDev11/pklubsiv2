<?php

namespace Tests\Feature;

use App\Models\ProposalMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Validation rule for nilai input lives in App\Http\Requests\SaveNilaiRequest:
 * accept 0 (kosong/belum dinilai) atau 75-100. Cover di 3 role.
 */
class NilaiValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_save_nilai_below_75_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $proposal = ProposalMahasiswa::factory()->magang()->create(['nilai' => 0]);

        $response = $this->actingAs($admin)
            ->postJson(route('admin.nilai.save'), [
                'form_id' => [$proposal->id],
                'nilai' => [60],
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('nilai.0');
        $this->assertSame(0, $proposal->fresh()->nilai);
    }

    public function test_admin_save_nilai_75_to_100_is_accepted_with_json_response(): void
    {
        $admin = User::factory()->admin()->create();
        $proposal = ProposalMahasiswa::factory()->magang()->create(['nilai' => 0]);

        $response = $this->actingAs($admin)
            ->postJson(route('admin.nilai.save'), [
                'form_id' => [$proposal->id],
                'nilai' => [85],
            ]);

        $response->assertOk();
        $response->assertJson(['message' => 'Nilai berhasil disimpan otomatis.']);
        $this->assertSame(85, $proposal->fresh()->nilai);
    }

    public function test_admin_save_nilai_zero_is_accepted_as_kosong(): void
    {
        $admin = User::factory()->admin()->create();
        $proposal = ProposalMahasiswa::factory()->magang()->create(['nilai' => 0]);

        $response = $this->actingAs($admin)
            ->postJson(route('admin.nilai.save'), [
                'form_id' => [$proposal->id],
                'nilai' => [0],
            ]);

        $response->assertOk();
        $this->assertSame(0, $proposal->fresh()->nilai);
    }

    public function test_dosen_save_nilai_below_75_is_rejected(): void
    {
        $dosen = User::factory()->dosen()->create(['name' => 'Dr. Budi']);
        $mahasiswa = User::factory()->mahasiswa()->create(['nama_dosen_pa' => 'Dr. Budi']);
        $proposal = ProposalMahasiswa::factory()->magang()->create([
            'user_id' => $mahasiswa->id,
            'nilai' => 0,
        ]);

        $response = $this->actingAs($dosen)
            ->postJson(route('dosen.nilai.save'), [
                'form_id' => [$proposal->id],
                'nilai' => [74],
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('nilai.0');
        $this->assertSame(0, $proposal->fresh()->nilai);
    }

    public function test_dosen_save_nilai_in_passing_range_accepted_via_json(): void
    {
        $dosen = User::factory()->dosen()->create(['name' => 'Dr. Budi']);
        $mahasiswa = User::factory()->mahasiswa()->create(['nama_dosen_pa' => 'Dr. Budi']);
        $proposal = ProposalMahasiswa::factory()->magang()->create([
            'user_id' => $mahasiswa->id,
            'nilai' => 0,
        ]);

        $response = $this->actingAs($dosen)
            ->postJson(route('dosen.nilai.save'), [
                'form_id' => [$proposal->id],
                'nilai' => [88],
            ]);

        $response->assertOk();
        $response->assertJson(['message' => 'Nilai berhasil disimpan otomatis.']);
        $this->assertSame(88, $proposal->fresh()->nilai);
    }

    public function test_mentor_save_nilai_below_75_is_rejected(): void
    {
        $mentor = User::factory()->mentor()->create(['username' => 'mentor@x.com']);
        $proposal = ProposalMahasiswa::factory()->magang()->create([
            'email_mentor' => 'mentor@x.com',
            'nilai' => 0,
        ]);

        $response = $this->actingAs($mentor)
            ->postJson(route('mentor.nilai.save'), [
                'form_id' => [$proposal->id],
                'nilai' => [50],
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('nilai.0');
        $this->assertSame(0, $proposal->fresh()->nilai);
    }

    public function test_mentor_save_nilai_in_passing_range_accepted_via_json(): void
    {
        $mentor = User::factory()->mentor()->create(['username' => 'mentor@x.com']);
        $proposal = ProposalMahasiswa::factory()->magang()->create([
            'email_mentor' => 'mentor@x.com',
            'nilai' => 0,
        ]);

        $response = $this->actingAs($mentor)
            ->postJson(route('mentor.nilai.save'), [
                'form_id' => [$proposal->id],
                'nilai' => [92],
            ]);

        $response->assertOk();
        $response->assertJson(['message' => 'Nilai berhasil disimpan otomatis.']);
        $this->assertSame(92, $proposal->fresh()->nilai);
    }

    public function test_nilai_above_100_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $proposal = ProposalMahasiswa::factory()->magang()->create(['nilai' => 0]);

        $response = $this->actingAs($admin)
            ->postJson(route('admin.nilai.save'), [
                'form_id' => [$proposal->id],
                'nilai' => [101],
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('nilai.0');
    }

    public function test_admin_save_nilai_html_form_post_redirects_back_with_success(): void
    {
        $admin = User::factory()->admin()->create();
        $proposal = ProposalMahasiswa::factory()->magang()->create(['nilai' => 0]);

        $response = $this->actingAs($admin)
            ->from(route('admin.nilai.pkl'))
            ->post(route('admin.nilai.save'), [
                'form_id' => [$proposal->id],
                'nilai' => [80],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertSame(80, $proposal->fresh()->nilai);
    }
}
