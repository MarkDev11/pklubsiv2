<?php

namespace Tests\Unit\Services;

use App\Models\ProposalMahasiswa;
use App\Models\User;
use App\Services\NilaiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_saves_nilai_successfully(): void
    {
        $proposal = ProposalMahasiswa::factory()->create(['nilai' => 0]);

        $this->nilaiService->saveNilai(
            [$proposal->id],
            [85],
            'Test Penilai',
            fn ($q) => $q
        );

        $this->assertEquals(85, $proposal->fresh()->nilai);
        $this->assertEquals('Test Penilai', $proposal->fresh()->penilai);
    }

    public function test_updates_nilai_when_changed(): void
    {
        $proposal = ProposalMahasiswa::factory()->dinilai()->create(['nilai' => 70]);

        $this->nilaiService->saveNilai(
            [$proposal->id],
            [90],
            'Updated Penilai',
            fn ($q) => $q
        );

        $this->assertEquals(90, $proposal->fresh()->nilai);
        $this->assertEquals('Updated Penilai', $proposal->fresh()->penilai);
    }

    public function test_does_not_update_when_nilai_unchanged(): void
    {
        $proposal = ProposalMahasiswa::factory()->dinilai()->create(['nilai' => 80, 'penilai' => 'Original']);

        $this->nilaiService->saveNilai(
            [$proposal->id],
            [80],
            'New Penilai',
            fn ($q) => $q
        );

        $proposal->refresh();
        $this->assertEquals(80, $proposal->nilai);
        $this->assertEquals('Original', $proposal->penilai);
    }

    public function test_skips_nonexistent_proposals(): void
    {
        $proposal = ProposalMahasiswa::factory()->create(['nilai' => 0]);

        $this->nilaiService->saveNilai(
            [$proposal->id, 9999],
            [85, 90],
            'Test',
            fn ($q) => $q
        );

        $this->assertEquals(85, $proposal->fresh()->nilai);
    }

    public function test_validates_admin_ownership(): void
    {
        $admin = User::factory()->admin()->create();
        $proposal = ProposalMahasiswa::factory()->create();

        $result = $this->nilaiService->validateProposalOwnership($proposal, $admin);

        $this->assertTrue($result);
    }

    public function test_validates_dosen_ownership(): void
    {
        $dosen = User::factory()->dosen()->create(['name' => 'Dr. Test']);
        $mahasiswa = User::factory()->mahasiswa()->create(['nama_dosen_pa' => 'Dr. Test']);
        $proposal = ProposalMahasiswa::factory()->create(['user_id' => $mahasiswa->id]);

        $result = $this->nilaiService->validateProposalOwnership($proposal, $dosen);

        $this->assertTrue($result);
    }

    public function test_validates_mentor_ownership(): void
    {
        $mentor = User::factory()->mentor()->create(['username' => 'mentor@test.com']);
        $proposal = ProposalMahasiswa::factory()->create(['email_mentor' => 'mentor@test.com']);

        $result = $this->nilaiService->validateProposalOwnership($proposal, $mentor);

        $this->assertTrue($result);
    }

    public function test_rejects_invalid_ownership(): void
    {
        $dosen = User::factory()->dosen()->create(['name' => 'Dr. Wrong']);
        $mahasiswa = User::factory()->mahasiswa()->create(['nama_dosen_pa' => 'Dr. Right']);
        $proposal = ProposalMahasiswa::factory()->create(['user_id' => $mahasiswa->id]);

        $result = $this->nilaiService->validateProposalOwnership($proposal, $dosen);

        $this->assertFalse($result);
    }
}
