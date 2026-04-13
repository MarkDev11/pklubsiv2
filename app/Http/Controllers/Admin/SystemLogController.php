<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ErrorLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SystemLogController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', ErrorLog::class);

        $logs = ErrorLog::with('user')->latest()->paginate(50);

        return view('admin.logs.error', compact('logs'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $log = ErrorLog::findOrFail($id);

        $this->authorize('delete', $log);

        $log->delete();

        return back()->with('success', 'Log error berhasil dihapus.');
    }

    public function clear(): RedirectResponse
    {
        $this->authorize('clear', ErrorLog::class);

        ErrorLog::truncate();

        return back()->with('success', 'Seluruh log error telah dibersihkan.');
    }
}
