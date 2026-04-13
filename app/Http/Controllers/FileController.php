<?php

namespace App\Http\Controllers;

use App\Models\ProposalMahasiswa;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    public function serve(string $filename): Response
    {
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

    /**
     * @return array<string>
     */
    protected function getAllowedFilesForUser(User $user, string $filename): array
    {
        if ($user->isAdmin()) {
            return [$filename];
        }

        if ($user->isMahasiswa()) {
            $proposal = ProposalMahasiswa::where('user_id', $user->id)->first();

            return array_values(array_filter([
                $proposal?->skm,
                $proposal?->lp,
                $proposal?->lpp,
                $proposal?->skp,
            ]));
        }

        if ($user->isDosen()) {
            $proposals = ProposalMahasiswa::whereHas('user', function ($q) use ($user) {
                $q->where('nama_dosen_pa', $user->name);
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
