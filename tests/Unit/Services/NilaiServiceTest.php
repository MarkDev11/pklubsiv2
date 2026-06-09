<?php

namespace Tests\Unit\Services;

use App\Models\ProposalMahasiswa;
use App\Models\User;
use App\Services\NilaiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class NilaiServiceTest extends TestCase
{
    use RefreshDatabase;

    protected NilaiService $nilaiService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->nilaiService = app(NilaiService::class);
    }

    /**
     * Login user supaya Owen-It Auditing menyimpan user_id pada baris audit.
     * recalculateFinal() bergantung pada user_id audit untuk mengetahui rater per role.
     */
    protected function actAs(User $user): void
    {
        Auth::login($user);
    }

    public function test_dosen_input_above_threshold_becomes_final(): void
    {
        $dosen = User::factory()->dosen()->create(['name' => 'Dr. Budi']);
        $proposal = ProposalMahasiswa::factory()->create(['nilai' => 0]);

        $this->actAs($dosen);

        $this->nilaiService->saveNilai(
            [$proposal->id],
            [85],
            $dosen,
            fn ($q) => $q,
        );

        $proposal->refresh();
        $this->assertSame(85, $proposal->nilai);
        $this->assertSame('Dosen PA: Dr. Budi', $proposal->penilai);
    }

    public function test_dosen_and_mentor_both_above_threshold_average_final(): void
    {
        $dosen = User::factory()->dosen()->create(['name' => 'Dr. Budi']);
        $mentor = User::factory()->mentor()->create(['name' => 'Pak Andi']);
        $proposal = ProposalMahasiswa::factory()->create(['nilai' => 0]);

        $this->actAs($dosen);
        $this->nilaiService->saveNilai([$proposal->id], [80], $dosen, fn ($q) => $q);

        $this->actAs($mentor);
        $this->nilaiService->saveNilai([$proposal->id], [90], $mentor, fn ($q) => $q);

        $proposal->refresh();
        $this->assertSame(85, $proposal->nilai);
        $this->assertStringContainsString('Rata-rata', (string) $proposal->penilai);
        $this->assertStringContainsString('Dr. Budi', (string) $proposal->penilai);
        $this->assertStringContainsString('Pak Andi', (string) $proposal->penilai);
    }

    public function test_below_threshold_input_excluded_from_average(): void
    {
        $dosen = User::factory()->dosen()->create(['name' => 'Dr. Tono']);
        $mentor = User::factory()->mentor()->create(['name' => 'Pak Joko']);
        $proposal = ProposalMahasiswa::factory()->create(['nilai' => 0]);

        $this->actAs($dosen);
        $this->nilaiService->saveNilai([$proposal->id], [70], $dosen, fn ($q) => $q);

        $this->actAs($mentor);
        $this->nilaiService->saveNilai([$proposal->id], [90], $mentor, fn ($q) => $q);

        $proposal->refresh();
        $this->assertSame(90, $proposal->nilai);
        $this->assertSame('Mentor Industri: Pak Joko', $proposal->penilai);
    }

    public function test_admin_input_overrides_average(): void
    {
        $dosen = User::factory()->dosen()->create(['name' => 'Dr. Budi']);
        $mentor = User::factory()->mentor()->create(['name' => 'Pak Andi']);
        $admin = User::factory()->admin()->create(['name' => 'Admin Sistem']);
        $proposal = ProposalMahasiswa::factory()->create(['nilai' => 0]);

        $this->actAs($dosen);
        $this->nilaiService->saveNilai([$proposal->id], [80], $dosen, fn ($q) => $q);

        $this->actAs($mentor);
        $this->nilaiService->saveNilai([$proposal->id], [90], $mentor, fn ($q) => $q);

        $this->actAs($admin);
        $this->nilaiService->saveNilai([$proposal->id], [95], $admin, fn ($q) => $q);

        $proposal->refresh();
        $this->assertSame(95, $proposal->nilai);
        $this->assertSame('Admin: Admin Sistem', $proposal->penilai);
    }

    public function test_no_valid_input_keeps_nilai_zero(): void
    {
        $dosen = User::factory()->dosen()->create(['name' => 'Dr. Budi']);
        $proposal = ProposalMahasiswa::factory()->create(['nilai' => 0]);

        $this->actAs($dosen);
        $this->nilaiService->saveNilai([$proposal->id], [60], $dosen, fn ($q) => $q);

        $proposal->refresh();
        $this->assertSame(0, $proposal->nilai);
    }

    public function test_auto_fill_unscored_assigns_75_sistem(): void
    {
        $a = ProposalMahasiswa::factory()->create(['nilai' => 0]);
        $b = ProposalMahasiswa::factory()->create(['nilai' => 0]);
        $alreadyScored = ProposalMahasiswa::factory()->create(['nilai' => 88, 'penilai' => 'Dosen PA: X']);

        $count = $this->nilaiService->autoFillUnscored();

        $this->assertSame(2, $count);
        $this->assertSame(75, $a->fresh()->nilai);
        $this->assertSame('Sistem', $a->fresh()->penilai);
        $this->assertSame(75, $b->fresh()->nilai);
        $this->assertSame('Sistem', $b->fresh()->penilai);
        $this->assertSame(88, $alreadyScored->fresh()->nilai);
        $this->assertSame('Dosen PA: X', $alreadyScored->fresh()->penilai);
    }

    public function test_auto_fill_is_idempotent(): void
    {
        ProposalMahasiswa::factory()->create(['nilai' => 0]);

        $first = $this->nilaiService->autoFillUnscored();
        $second = $this->nilaiService->autoFillUnscored();

        $this->assertSame(1, $first);
        $this->assertSame(0, $second);
    }

    public function test_skips_nonexistent_proposals(): void
    {
        $dosen = User::factory()->dosen()->create(['name' => 'Dr. Test']);
        $proposal = ProposalMahasiswa::factory()->create(['nilai' => 0]);

        $this->actAs($dosen);

        $this->nilaiService->saveNilai(
            [$proposal->id, 9999],
            [85, 90],
            $dosen,
            fn ($q) => $q,
        );

        $this->assertSame(85, $proposal->fresh()->nilai);
    }

    public function test_validates_admin_ownership(): void
    {
        $admin = User::factory()->admin()->create();
        $proposal = ProposalMahasiswa::factory()->create();

        $this->assertTrue($this->nilaiService->validateProposalOwnership($proposal, $admin));
    }

    public function test_validates_dosen_ownership(): void
    {
        $dosen = User::factory()->dosen()->create(['name' => 'Dr. Test']);
        $mahasiswa = User::factory()->mahasiswa()->create(['nama_dosen_pa' => 'Dr. Test']);
        $proposal = ProposalMahasiswa::factory()->create(['user_id' => $mahasiswa->id]);

        $this->assertTrue($this->nilaiService->validateProposalOwnership($proposal, $dosen));
    }

    public function test_validates_mentor_ownership(): void
    {
        $mentor = User::factory()->mentor()->create(['username' => 'mentor@test.com']);
        $proposal = ProposalMahasiswa::factory()->create(['email_mentor' => 'mentor@test.com']);

        $this->assertTrue($this->nilaiService->validateProposalOwnership($proposal, $mentor));
    }

    public function test_rejects_invalid_ownership(): void
    {
        $dosen = User::factory()->dosen()->create(['name' => 'Dr. Wrong']);
        $mahasiswa = User::factory()->mahasiswa()->create(['nama_dosen_pa' => 'Dr. Right']);
        $proposal = ProposalMahasiswa::factory()->create(['user_id' => $mahasiswa->id]);

        $this->assertFalse($this->nilaiService->validateProposalOwnership($proposal, $dosen));
    }
}
