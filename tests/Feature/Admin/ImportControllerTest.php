<?php

namespace Tests\Feature\Admin;

use App\Models\Import;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ImportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $dosen;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $admin */
        $admin = User::factory()->admin()->create();
        $this->admin = $admin;

        /** @var User $dosen */
        $dosen = User::factory()->dosen()->create(['username' => 'DSN001']);
        $this->dosen = $dosen;
    }

    public function test_import_upload_redirects_to_chunk_progress(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.import.store'), [
            'upload_excel' => $this->xlsxUpload(),
            'password_default_confirm' => '1',
        ]);

        $import = Import::firstOrFail();
        $response->assertRedirect(route('admin.import.progress', $import));
        $this->assertSame(3, $import->total_rows);
    }

    public function test_admin_can_process_import_in_chunks(): void
    {
        $this->actingAs($this->admin)->post(route('admin.import.store'), [
            'upload_excel' => $this->xlsxUpload(),
            'password_default_confirm' => '1',
        ]);

        $import = Import::firstOrFail();

        $first = $this->actingAs($this->admin)->postJson(route('admin.import.chunk', $import), [
            'limit' => 2,
        ]);

        $first->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('processed', 2);

        $second = $this->actingAs($this->admin)->postJson(route('admin.import.chunk', $import), [
            'limit' => 2,
        ]);

        $second->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('processed', 3)
            ->assertJsonPath('imported', 2)
            ->assertJsonPath('skipped', 1)
            ->assertJsonPath('completed', true);

        $this->assertDatabaseHas('users', ['username' => 'MHS001']);
        $this->assertDatabaseMissing('users', ['username' => 'MHS002']);
        $this->assertDatabaseHas('users', ['username' => 'DSN002']);

        $user = User::where('username', 'MHS001')->firstOrFail();
        $this->assertTrue(Hash::check('MHS001', $user->password));
    }

    public function test_finalize_deletes_uploaded_import_file(): void
    {
        $this->actingAs($this->admin)->post(route('admin.import.store'), [
            'upload_excel' => $this->xlsxUpload(),
            'password_default_confirm' => '1',
        ]);

        $import = Import::firstOrFail();
        Storage::disk('local')->assertExists($import->path);

        $this->actingAs($this->admin)->postJson(route('admin.import.chunk', $import), ['limit' => 3]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.import.finalize', $import));

        $response->assertOk()->assertJsonPath('success', true);
        Storage::disk('local')->assertMissing($import->path);
    }

    private function xlsxUpload(): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'import').'.xlsx';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['Nama', 'Username', 'Password', 'Role', 'Dosen PA', 'Jenis', 'Kelas'],
            ['Mahasiswa Satu', 'MHS001', '', 'mahasiswa', 'DSN001', 'Magang', '12.7A.01'],
            ['Mahasiswa Invalid', 'MHS002', '', 'mahasiswa', 'DSN999', 'Magang', '12.7A.01'],
            ['Dosen Dua', 'DSN002', 'dosen123', 'dosen', '', '', ''],
        ]);

        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        return new UploadedFile($path, 'import.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }
}
