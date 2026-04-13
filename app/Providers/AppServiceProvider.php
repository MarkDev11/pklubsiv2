<?php

namespace App\Providers;

use App\Models\ErrorLog;
use App\Models\OpeningHour;
use App\Models\ProposalMahasiswa;
use App\Models\User;
use App\Policies\ErrorLogPolicy;
use App\Policies\ProposalPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(ErrorLog::class, ErrorLogPolicy::class);
        Gate::policy(ProposalMahasiswa::class, ProposalPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        ProposalMahasiswa::created(fn () => $this->flushDashboardCache());
        ProposalMahasiswa::updated(fn () => $this->flushDashboardCache());
        ProposalMahasiswa::deleted(fn () => $this->flushDashboardCache());
        User::created(fn () => $this->flushDashboardCache());
        User::updated(fn () => $this->flushDashboardCache());
        User::deleted(fn () => $this->flushDashboardCache());
        OpeningHour::saved(fn () => Cache::forget('opening_hours'));
    }

    protected function flushDashboardCache(): void
    {
        Cache::forget('dashboard.admin');
        Cache::forget('dashboard.recent_logs');
        Cache::flush();
    }
}
