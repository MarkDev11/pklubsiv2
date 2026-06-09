<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = $this->authenticatedUser()->load('dosenPa');

        return view('profile.edit', compact('user'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $this->authenticatedUser();

        if (! $request->validateCurrentPassword($user)) {
            return back()->with('error', 'Password lama tidak cocok.');
        }

        $user->update(['password' => $request->password_baru]);

        ActivityLog::log($user->id, 'Update password');

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
