<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOpeningHourRequest;
use App\Models\ActivityLog;
use App\Models\OpeningHour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class OpeningHourController extends Controller
{
    public function index(): View
    {
        $openingHours = OpeningHour::first();

        return view('admin.tanggal', compact('openingHours'));
    }

    public function update(UpdateOpeningHourRequest $request): RedirectResponse
    {
        $user = $this->authenticatedUser();
        $data = $request->validated();

        $openingHour = OpeningHour::first();

        if ($openingHour) {
            $openingHour->update($data);
        } else {
            OpeningHour::create($data);
        }

        Cache::forget('opening_hours');

        ActivityLog::log($user->id, 'Update tanggal deadline');

        return redirect()->route('admin.tanggal.index')
            ->with('success', 'Pengaturan tanggal berhasil diperbarui.');
    }
}
