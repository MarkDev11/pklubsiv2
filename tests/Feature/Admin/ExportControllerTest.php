<?php

namespace Tests\Feature\Admin;

use App\Models\Export;
use App\Models\ProposalMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_excel_export_chunks_use_stored_filters_and_finalize_server_side(): void
    {
        Storage::fake('local');

        $admin = User::factory()->admin()->create();
        ProposalMahasiswa::factory()->magang()->create([
            'nama' => 'Included Student',
            'tempat_riset' => 'Company, With Comma',
            'nilai' => 90,
        ]);
        ProposalMahasiswa::factory()->magang()->create([
            'nama' => 'Other Student',
            'tempat_riset' => '=Formula Company',
            'nilai' => 80,
        ]);

        $export = Export::create([
            'user_id' => $admin->id,
            'type' => 'excel',
            'category' => 'pkl',
            'filters' => ['search' => 'Included'],
            'total_records' => 1,
            'status' => 'pending',
        ]);

        $chunk = $this->actingAs($admin)->postJson(route('admin.exports.chunk', $export), [
            'offset' => 0,
            'limit' => 100,
            'search' => 'Other',
        ]);

        $chunk->assertOk()->assertJson([
            'success' => true,
            'processed' => 1,
        ]);

        $finalize = $this->actingAs($admin)->postJson(route('admin.exports.finalize', $export), [
            'data' => [['nama' => 'Injected Browser Data']],
        ]);

        $finalize->assertOk()->assertJson(['success' => true]);

        $export->refresh();
        Storage::disk('local')->assertExists($export->path);

        $csv = Storage::disk('local')->get($export->path);
        $this->assertStringContainsString('Included Student', $csv);
        $this->assertStringNotContainsString('Other Student', $csv);
        $this->assertStringNotContainsString('Injected Browser Data', $csv);
        $this->assertStringContainsString('"Company, With Comma"', $csv);
    }

    public function test_export_download_requires_completed_export_with_path(): void
    {
        $admin = User::factory()->admin()->create();

        $export = Export::create([
            'user_id' => $admin->id,
            'type' => 'excel',
            'category' => 'pkl',
            'total_records' => 1,
            'status' => 'pending',
            'short_code' => 'abc1234567',
        ]);

        $this->actingAs($admin)
            ->get(route('exports.download', $export->short_code))
            ->assertNotFound();
    }

    public function test_public_export_download_routes_are_rate_limited(): void
    {
        $this->assertContains('throttle:20,1', Route::getRoutes()->getByName('exports.download')->middleware());
        $this->assertContains('throttle:20,1', Route::getRoutes()->getByName('exports.file.download')->middleware());
    }
}
