<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportRequest;
use App\Jobs\ImportUsersJob;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ImportController extends Controller
{
    public function index(): View
    {
        return view('admin.import');
    }

    public function import(ImportRequest $request): RedirectResponse
    {
        $file = $request->file('upload_excel');

        if (! $file) {
            throw ValidationException::withMessages([
                'upload_excel' => 'File import tidak ditemukan.',
            ]);
        }

        $path = $file->storeAs('imports', 'import_'.uniqid().'.'.$file->getClientOriginalExtension(), 'local');

        if ($path === false) {
            throw ValidationException::withMessages([
                'upload_excel' => 'Gagal menyimpan file import.',
            ]);
        }

        $adminId = Auth::user()?->id;

        if (! is_int($adminId)) {
            throw ValidationException::withMessages([
                'upload_excel' => 'Sesi admin tidak valid, silakan login ulang.',
            ]);
        }

        ImportUsersJob::dispatch($path, $adminId);

        ActivityLog::log($adminId, 'Import data: diproses di background');

        return back()->with('success', 'File import sedang diproses di background. Hasil akan muncul segera.');
    }
}
