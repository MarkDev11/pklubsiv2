<?php

namespace App\Services;

use App\Models\Export;
use App\Models\ProposalMahasiswa;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ScopedExportService
{
    protected const MAX_RECORDS = 1000;

    public function create(User $user, Builder $query, string $category, string $type): Export
    {
        $type = $type === 'excel' ? 'excel' : 'pdf';
        $existingExport = Export::where('user_id', $user->id)
            ->where('category', $category)
            ->where('type', $type)
            ->where('status', 'completed')
            ->where('expires_at', '>', now())
            ->whereNotNull('path')
            ->latest()
            ->first();

        if ($existingExport && $existingExport->path && Storage::exists($existingExport->path)) {
            return $existingExport;
        }

        $count = (clone $query)->count();

        if ($count > self::MAX_RECORDS) {
            throw ValidationException::withMessages([
                'type' => "Terlalu banyak data ({$count} records). Maksimal ".self::MAX_RECORDS.'.',
            ]);
        }

        $records = $query->with(['user', 'dosenPaUser'])->get();
        $timestamp = now()->format('Ymd_His');
        $filename = "{$user->role->value}_{$category}_{$type}_{$timestamp}." . ($type === 'pdf' ? 'pdf' : 'csv');
        $path = "exports/{$filename}";

        if ($type === 'pdf') {
            $pdf = Pdf::loadView("pdf.rekap-nilai-{$category}", [
                'user' => $user,
                'proposals' => $records,
            ]);

            Storage::put($path, $pdf->output());
        } else {
            Storage::put($path, $this->makeCsv($records));
        }

        return Export::create([
            'user_id' => $user->id,
            'type' => $type,
            'category' => $category,
            'total_records' => $count,
            'processed_records' => $count,
            'status' => 'completed',
            'filename' => $filename,
            'path' => $path,
            'expires_at' => now()->addHour(),
        ]);
    }

    public function history(User $user, string $category)
    {
        return Export::where('user_id', $user->id)
            ->where('category', $category)
            ->select('id', 'type', 'category', 'total_records', 'status', 'filename', 'path', 'expires_at', 'download_count', 'created_at')
            ->latest()
            ->limit(10)
            ->get();
    }

    public function dosenQuery(User $user, string $category): Builder
    {
        return $this->categoryQuery($category)->byDosen($user->username);
    }

    public function mentorQuery(User $user, string $category): Builder
    {
        return $this->categoryQuery($category)->where('email_mentor', $user->username);
    }

    protected function categoryQuery(string $category): Builder
    {
        return $category === 'pkl'
            ? ProposalMahasiswa::magang()
            : ProposalMahasiswa::msib();
    }

    protected function makeCsv($records): string
    {
        $handle = fopen('php://temp', 'r+');

        fputcsv($handle, ['NIM', 'Nama', 'Judul PKL', 'Tempat Riset', 'Nama Mentor', 'Email Perusahaan', 'Dosen PA', 'Nilai', 'Penilai', 'Terakhir Update']);

        foreach ($records as $record) {
            fputcsv($handle, [
                $this->escapeCsvValue($record->nim),
                $this->escapeCsvValue($record->nama),
                $this->escapeCsvValue($record->judul_pkl),
                $this->escapeCsvValue($record->tempat_riset),
                $this->escapeCsvValue($record->nama_mentor),
                $this->escapeCsvValue($record->email_perusahaan),
                $this->escapeCsvValue($record->dosenPaLabel()),
                $record->nilai,
                $this->escapeCsvValue($record->penilai),
                $this->escapeCsvValue($record->updated_at?->format('d/m/Y H:i')),
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }

    protected function escapeCsvValue(mixed $value): mixed
    {
        if (! is_string($value) || $value === '') {
            return $value;
        }

        return in_array($value[0], ['=', '+', '-', '@'], true) ? "'{$value}" : $value;
    }
}
