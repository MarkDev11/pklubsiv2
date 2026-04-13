<?php

namespace Tests\Feature\Admin;

use App\Models\ErrorLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorLogControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->mahasiswa = User::factory()->mahasiswa()->create();
    }

    public function test_admin_can_view_error_logs(): void
    {
        ErrorLog::create([
            'type' => 'TestException',
            'message' => 'Test error message',
            'request_url' => '/test',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.logs.error'));

        $response->assertOk();
        $response->assertViewIs('admin.logs.error');
    }

    public function test_non_admin_cannot_view_error_logs(): void
    {
        $response = $this->actingAs($this->mahasiswa)->get(route('admin.logs.error'));

        $response->assertForbidden();
    }

    public function test_admin_can_delete_single_error_log(): void
    {
        $log = ErrorLog::create([
            'type' => 'TestException',
            'message' => 'Test error message',
            'request_url' => '/test',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.logs.error.destroy', $log->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('error_logs', ['id' => $log->id]);
    }

    public function test_admin_can_clear_all_error_logs(): void
    {
        ErrorLog::create(['type' => 'Test1', 'message' => 'Error 1', 'request_url' => '/test1']);
        ErrorLog::create(['type' => 'Test2', 'message' => 'Error 2', 'request_url' => '/test2']);

        $response = $this->actingAs($this->admin)->delete(route('admin.logs.error.clear'));

        $response->assertRedirect();
        $this->assertEquals(0, ErrorLog::count());
    }

    public function test_guest_cannot_access_error_logs(): void
    {
        $response = $this->get(route('admin.logs.error'));

        $response->assertRedirect(route('login'));
    }
}
