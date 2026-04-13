<?php

namespace Tests\Unit\Policies;

use App\Models\ErrorLog;
use App\Models\User;
use App\Policies\ErrorLogPolicy;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ErrorLogPolicyTest extends TestCase
{
    protected ErrorLogPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new ErrorLogPolicy;
    }

    #[Test]
    public function admin_can_view_any_error_logs(): void
    {
        $admin = User::factory()->admin()->make();

        $this->assertTrue($this->policy->viewAny($admin));
    }

    #[Test]
    public function non_admin_cannot_view_any_error_logs(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->make();
        $dosen = User::factory()->dosen()->make();
        $mentor = User::factory()->mentor()->make();

        $this->assertFalse($this->policy->viewAny($mahasiswa));
        $this->assertFalse($this->policy->viewAny($dosen));
        $this->assertFalse($this->policy->viewAny($mentor));
    }

    #[Test]
    public function admin_can_view_error_log(): void
    {
        $admin = User::factory()->admin()->make();
        $log = new ErrorLog;

        $this->assertTrue($this->policy->view($admin, $log));
    }

    #[Test]
    public function non_admin_cannot_view_error_log(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->make();
        $log = new ErrorLog;

        $this->assertFalse($this->policy->view($mahasiswa, $log));
    }

    #[Test]
    public function admin_can_delete_error_log(): void
    {
        $admin = User::factory()->admin()->make();
        $log = new ErrorLog;

        $this->assertTrue($this->policy->delete($admin, $log));
    }

    #[Test]
    public function admin_can_clear_all_error_logs(): void
    {
        $admin = User::factory()->admin()->make();

        $this->assertTrue($this->policy->clear($admin));
    }

    #[Test]
    public function non_admin_cannot_clear_error_logs(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->make();

        $this->assertFalse($this->policy->clear($mahasiswa));
    }
}
