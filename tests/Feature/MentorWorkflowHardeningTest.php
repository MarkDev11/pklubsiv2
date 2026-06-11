<?php

namespace Tests\Feature;

use App\Models\ProposalMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class MentorWorkflowHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_mentor_nilai_save_route_is_rate_limited(): void
    {
        $this->assertContains('throttle:60,1', Route::getRoutes()->getByName('mentor.nilai.save')->middleware());
    }

    public function test_mentor_export_routes_are_disabled(): void
    {
        $this->assertNull(Route::getRoutes()->getByName('mentor.exports.pkl'));
        $this->assertNull(Route::getRoutes()->getByName('mentor.exports.msib'));
    }

    public function test_mentor_cannot_save_nilai_for_other_mentor_student(): void
    {
        $mentor = User::factory()->mentor()->create(['username' => 'mentor-a@example.test']);
        $proposal = ProposalMahasiswa::factory()->magang()->create([
            'email_mentor' => 'mentor-b@example.test',
            'nilai' => 0,
        ]);

        $this->actingAs($mentor)
            ->postJson(route('mentor.nilai.save'), [
                'form_id' => [$proposal->id],
                'nilai' => [88],
            ])
            ->assertForbidden();

        $this->assertSame(0, $proposal->fresh()->nilai);
    }

    public function test_mentor_can_save_nilai_for_own_student(): void
    {
        $mentor = User::factory()->mentor()->create(['username' => 'mentor-a@example.test']);
        $proposal = ProposalMahasiswa::factory()->magang()->create([
            'email_mentor' => $mentor->username,
            'nilai' => 0,
        ]);

        $this->actingAs($mentor)
            ->postJson(route('mentor.nilai.save'), [
                'form_id' => [$proposal->id],
                'nilai' => [88],
            ])
            ->assertOk()
            ->assertJson(['message' => 'Nilai berhasil disimpan otomatis.']);

        $this->assertSame(88, $proposal->fresh()->nilai);
    }
}
