<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportRequest;
use App\Models\ActivityLog;
use App\Models\Import;
use App\Services\ImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ImportController extends Controller
{
    public function __construct(private readonly ImportService $importService) {}

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

        $totalRows = $this->importService->countDataRows(storage_path('app/private/'.$path));

        if ($totalRows === 0) {
            Storage::disk('local')->delete($path);

            throw ValidationException::withMessages([
                'upload_excel' => 'File import tidak memiliki data akun.',
            ]);
        }

        $import = Import::create([
            'user_id' => $adminId,
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'total_rows' => $totalRows,
            'status' => 'pending',
        ]);

        ActivityLog::log($adminId, 'Import data: proses chunk dimulai');

        return redirect()->route('admin.import.progress', $import);
    }

    public function progress(Import $import): View
    {
        $this->authorizeImport($import);

        return view('admin.import-progress', compact('import'));
    }

    public function processChunk(Request $request, Import $import): JsonResponse
    {
        $this->authorizeImport($import);

        $validated = $request->validate([
            'limit' => ['required', 'integer', 'min:1', 'max:250'],
        ]);

        try {
            $import->update(['status' => 'processing', 'error_message' => null]);
            $result = $this->importService->processChunk($import->fresh(), $validated['limit']);

            return response()->json([
                'success' => true,
                ...$result,
                'total' => $import->fresh()->total_rows,
            ]);
        } catch (\Throwable $e) {
            $import->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function finalize(Import $import): JsonResponse
    {
        $this->authorizeImport($import);

        if ($import->processed_rows < $import->total_rows) {
            return response()->json([
                'success' => false,
                'error' => 'Import belum selesai diproses.',
            ], 422);
        }

        if ($import->path && Storage::disk('local')->exists($import->path)) {
            Storage::disk('local')->delete($import->path);
        }

        $import->update([
            'status' => 'completed',
            'completed_at' => $import->completed_at ?? now(),
        ]);

        return response()->json(['success' => true]);
    }

    private function authorizeImport(Import $import): void
    {
        if ($import->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
