<?php

namespace App\Policies;

use App\Models\ErrorLog;
use App\Models\User;

class ErrorLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, ErrorLog $log): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, ErrorLog $log): bool
    {
        return $user->isAdmin();
    }

    public function clear(User $user): bool
    {
        return $user->isAdmin();
    }
}
