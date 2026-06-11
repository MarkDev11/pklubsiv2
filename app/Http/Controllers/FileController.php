<?php

namespace App\Http\Controllers;

use App\Models\ProposalMahasiswa;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    public function serve(string $encrypted): Response
    {
        // Decrypt the filename
        $filename = decryptUrl($encrypted);

        if (str_contains($filename, '..') || str_contains($filename, '/') || str_contains($filename, '\\')) {
            abort(403, 'Akses ditolak.');
        }

        $user = $this->authenticatedUser();

        $allowedFiles = $this->getAllowedFilesForUser($user, $filename);

        if (! in_array($filename, $allowedFiles)) {
            abort(403, 'Anda tidak memiliki akses ke file ini.');
        }

        $storagePath = 'uploads/'.$filename;

        if (Storage::disk('public')->exists($storagePath)) {
            return response()->file(
                Storage::disk('public')->path($storagePath)
            );
        }

        $legacyPath = public_path('uploads/'.$filename);
        if (file_exists($legacyPath)) {
            return response()->file($legacyPath);
        }

        abort(404, 'File tidak ditemukan.');
    }

    public function downloadProposalDocument(string $id, string $type): Response
    {
        // Decrypt the proposal ID
        $proposalId = decryptUrl($id);

        // Find the proposal
        $proposal = ProposalMahasiswa::findOrFail($proposalId);

        // Get the filename based on type
        $filename = match ($type) {
            'lp' => $proposal->lp,
            'lpp' => $proposal->lpp,
            'skp' => $proposal->skp,
            default => abort(400, 'Tipe dokumen tidak valid.')
        };

        // Check if file exists in proposal
        if (! $filename) {
            abort(404, 'Dokumen belum diupload.');
        }

        // Check authorization
        $user = $this->authenticatedUser();
        $allowedFiles = $this->getAllowedFilesForUser($user, $filename);

        if (! in_array($filename, $allowedFiles)) {
            abort(403, 'Anda tidak memiliki akses ke file ini.');
        }

        // Serve the file
        $storagePath = 'uploads/'.$filename;

        if (Storage::disk('public')->exists($storagePath)) {
            return response()->download(
                Storage::disk('public')->path($storagePath),
                $filename
            );
        }

        $legacyPath = public_path('uploads/'.$filename);
        if (file_exists($legacyPath)) {
            return response()->download($legacyPath, $filename);
        }

        abort(404, 'File tidak ditemukan.');
    }

    /**
     * @return array<string>
     */
    protected function getAllowedFilesForUser(User $user, string $filename): array
    {
        if ($user->isAdmin()) {
            return [$filename];
        }

        if ($user->isMahasiswa()) {
            $proposal = ProposalMahasiswa::where('nim', $user->username)->first();

            return array_values(array_filter([
                $proposal?->skm,
                $proposal?->lp,
                $proposal?->lpp,
                $proposal?->skp,
            ]));
        }

        if ($user->isDosen()) {
            $proposals = ProposalMahasiswa::whereHas('user', function ($q) use ($user) {
                $q->where('nama_dosen_pa', $user->username);
            })->get();

            return $proposals->flatMap(function ($proposal) {
                return array_filter([$proposal->skm, $proposal->lp, $proposal->lpp, $proposal->skp]);
            })->toArray();
        }

        if ($user->isMentor()) {
            $proposals = ProposalMahasiswa::where('email_mentor', $user->username)->get();

            return $proposals->flatMap(function ($proposal) {
                return array_filter([$proposal->skm, $proposal->lp, $proposal->lpp, $proposal->skp]);
            })->toArray();
        }

        return [];
    }
}
