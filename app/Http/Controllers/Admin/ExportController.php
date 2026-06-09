<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Export;
use App\Models\ProposalMahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    public function pklConfig()
    {
        return view('admin.exports.config', [
            'type' => 'pkl',
            'category' => 'pkl',
            'dosens' => User::dosen()
                ->select('username', 'name')
                ->distinct('username')
                ->orderBy('name')
                ->get(),
            'mentors' => User::mentor()->orderBy('name')->take(100)->get(),
        ]);
    }

    public function msibConfig()
    {
        return view('admin.exports.config', [
            'type' => 'msib',
            'category' => 'msib',
            'dosens' => User::dosen()
                ->select('username', 'name')
                ->distinct('username')
                ->orderBy('name')
                ->get(),
            'mentors' => User::mentor()->orderBy('name')->take(100)->get(),
        ]);
    }

    public function history()
    {
        $exports = Export::where('user_id', auth()->id())
            ->select([
                'id',
                'type',
                'category',
                'total_records',
                'processed_records',
                'status',
                'filename',
                'short_code',
                'error_message',
                'expires_at',
                'download_count',
                'created_at',
            ])
            ->latest()
            ->paginate(20);

        return view('admin.exports.history', compact('exports'));
    }

    public function cleanup()
    {
        $expiredExports = Export::where('expires_at', '<', now())
            ->where('status', 'completed')
            ->get();

        $deleted = 0;

        foreach ($expiredExports as $export) {
            if ($export->path && Storage::exists($export->path)) {
                Storage::delete($export->path);
            }

            $export->delete();
            $deleted++;
        }

        return back()->with('success', "Cleanup selesai. {$deleted} export expired dihapus.");
    }

    public function cleanupAll()
    {
        $exports = Export::all();
        $deleted = 0;

        foreach ($exports as $export) {
            if ($export->path && Storage::exists($export->path)) {
                Storage::delete($export->path);
            }

            $export->delete();
            $deleted++;
        }

        return back()->with('success', "Cleanup all selesai. {$deleted} export dihapus.");
    }

    public function count(Request $request)
    {
        $query = $this->buildFilteredQuery($request);
        $count = $query->count();
        
        return response()->json([
            'count' => $count,
            'max' => 1000,
            'allowed' => $count <= 1000 && $count > 0
        ]);
    }

    public function initiate(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:pdf,excel',
            'category' => 'required|in:pkl,msib',
        ]);

        $query = $this->buildFilteredQuery($request);
        $count = $query->count();

        if ($count > 1000) {
            return back()->with('error', "Terlalu banyak data ({$count} records). Maksimal 1000. Gunakan filter lebih spesifik.");
        }

        if ($count === 0) {
            return back()->with('error', 'Tidak ada data yang sesuai dengan filter.');
        }

        $activeExport = Export::where('user_id', auth()->id())
                             ->whereIn('status', ['pending', 'processing'])
                             ->first();

        if ($activeExport) {
            return redirect()->route('admin.exports.progress', $activeExport);
        }

        $export = Export::create([
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'category' => $validated['category'],
            'filters' => $request->except(['_token', 'type', 'category']),
            'total_records' => $count,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.exports.progress', $export);
    }
    public function progress(Export $export)
    {
        if ($export->user_id !== auth()->id()) {
            abort(403);
        }

        return view('admin.exports.progress', compact('export'));
    }

    public function processChunk(Request $request, Export $export)
    {
        if ($export->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'offset' => 'required|integer|min:0',
            'limit' => 'required|integer|min:1|max:100',
        ]);

        try {
            $export->update(['status' => 'processing']);

            $query = $this->buildFilteredQuery($request, $export->category);
            
            $chunk = $query->with(['user', 'dosenPaUser'])
                          ->skip($validated['offset'])
                          ->take($validated['limit'])
                          ->get();

            $chunkData = $chunk->map(function($proposal) {
                return [
                    'nim' => $proposal->nim,
                    'nama' => $proposal->nama,
                    'judul_pkl' => $proposal->judul_pkl,
                    'tempat_riset' => $proposal->tempat_riset,
                    'nama_mentor' => $proposal->nama_mentor,
                    'email_perusahaan' => $proposal->email_perusahaan,
                    'dosen_pa' => $proposal->dosenPaLabel(),
                    'nilai' => $proposal->nilai,
                    'penilai' => $proposal->penilai,
                    'updated_at' => $proposal->updated_at?->format('d/m/Y H:i') ?? '-',
                ];
            })->toArray();

            $newProcessed = $validated['offset'] + $chunk->count();
            $export->update(['processed_records' => $newProcessed]);

            return response()->json([
                'success' => true,
                'processed' => $newProcessed,
                'total' => $export->total_records,
                'data' => $chunkData,
            ]);

        } catch (\Exception $e) {
            $export->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function finalize(Request $request, Export $export)
    {
        if ($export->user_id !== auth()->id()) {
            abort(403);
        }

        $allData = $request->input('data', []);

        try {
            $timestamp = now()->format('Ymd_His');
            $filename = "{$export->category}_{$export->type}_{$timestamp}";

            if ($export->type === 'pdf') {
                $path = $this->generatePdfFromData($allData, $export->category, $filename);
            } else {
                $path = $this->generateExcelFromData($allData, $export->category, $filename);
            }

            $shortCode = $this->generateShortCode();

            $export->update([
                'status' => 'completed',
                'filename' => basename($path),
                'path' => $path,
                'short_code' => $shortCode,
                'expires_at' => now()->addHours(24),
            ]);

            return response()->json([
                'success' => true,
                'short_code' => $shortCode,
                'filename' => basename($path),
            ]);

        } catch (\Exception $e) {
            $export->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function download(string $code)
    {
        $export = Export::where('short_code', $code)->firstOrFail();

        if ($export->isExpired()) {
            abort(404, 'Export telah kadaluarsa.');
        }

        if (!Storage::exists($export->path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $export->increment('download_count');

        return Storage::download($export->path, $export->filename);
    }

    public function downloadFile(Export $export)
    {
        if ($export->user_id !== auth()->id()) {
            abort(403);
        }

        if ($export->isExpired()) {
            abort(404, 'Export telah kadaluarsa.');
        }

        if (!Storage::exists($export->path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $export->increment('download_count');

        return Storage::download($export->path, $export->filename);
    }

    public function status(Export $export)
    {
        if ($export->user_id !== auth()->id()) {
            abort(403);
        }

        return response()->json([
            'status' => $export->status,
            'processed_records' => $export->processed_records,
            'total_records' => $export->total_records,
            'percentage' => $export->getProgressPercentage(),
            'short_code' => $export->short_code,
            'filename' => $export->filename,
            'error_message' => $export->error_message,
        ]);
    }

    protected function buildFilteredQuery(Request $request, ?string $category = null)
    {
        $category = $category ?? $request->input('category');
        
        $query = $category === 'pkl' 
            ? ProposalMahasiswa::magang() 
            : ProposalMahasiswa::msib();

        if ($request->filled('dosen_pa')) {
            $query->byDosen($request->dosen_pa);
        }

        if ($request->filled('status_nilai')) {
            if ($request->status_nilai === 'belum_dinilai') {
                $query->where('nilai', 0);
            } elseif ($request->status_nilai === 'sudah_dinilai') {
                $query->where('nilai', '>', 0);
            }
        }

        if ($request->filled('nilai_min')) {
            $query->where('nilai', '>=', $request->nilai_min);
        }

        if ($request->filled('nilai_max')) {
            $query->where('nilai', '<=', $request->nilai_max);
        }

        if ($request->filled('mentor')) {
            $query->where('nama_mentor', $request->mentor);
        }

        if ($request->filled('tempat_riset')) {
            $query->where('tempat_riset', 'like', '%' . $request->tempat_riset . '%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    protected function generatePdfFromData(array $data, string $category, string $filename): string
    {
        // Convert array data to objects for compatibility with PDF view
        $proposals = collect($data)->map(function($item) {
            return (object) $item;
        });
        
        $pdf = Pdf::loadView('pdf.rekap-nilai-' . $category, [
            'proposals' => $proposals,
            'user' => auth()->user(),
        ]);

        $path = "exports/{$filename}.pdf";
        Storage::put($path, $pdf->output());

        return $path;
    }

    protected function generateExcelFromData(array $data, string $category, string $filename): string
    {
        $path = "exports/{$filename}.csv";
        
        // Simple CSV generation as Excel
        $csvContent = "NIM,Nama,Tempat Riset,Mentor,Dosen PA,Nilai\n";
        foreach ($data as $row) {
            $csvContent .= implode(',', [
                $row['nim'],
                $row['nama'],
                $row['tempat_riset'] ?? '-',
                $row['nama_mentor'] ?? '-',
                $row['dosen_pa'] ?? '-',
                $row['nilai'] ?? '0',
            ]) . "\n";
        }

        Storage::put($path, $csvContent);

        return $path;
    }

    protected function generateShortCode(): string
    {
        do {
            $code = Str::random(8);
        } while (Export::where('short_code', $code)->exists());

        return $code;
    }
}
