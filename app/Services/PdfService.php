<?php

namespace App\Services;

use App\Models\ProposalMahasiswa;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PdfService
{
    public function streamPklPdf(?User $user = null): Response
    {
        return $this->generateRekapPkl($user);
    }

    public function streamMsibPdf(?User $user = null): Response
    {
        return $this->generateRekapMsib($user);
    }

    public function generateRekapPkl(?User $scopedUser = null): Response
    {
        return $this->generateRekap('magang', 'rekap-nilai-pkl', $scopedUser);
    }

    public function generateRekapMsib(?User $scopedUser = null): Response
    {
        return $this->generateRekap('msib', 'rekap-nilai-msib', $scopedUser);
    }

    protected function generateRekap(string $scope, string $viewName, ?User $scopedUser): Response
    {
        $query = $scope === 'magang'
            ? ProposalMahasiswa::magang()
            : ProposalMahasiswa::msib();

        if ($scopedUser) {
            if ($scopedUser->isDosen()) {
                $query->byDosen($scopedUser->name);
            } elseif ($scopedUser->isMentor()) {
                $query->where('email_mentor', $scopedUser->username);
            }
        }

        $proposals = $query->with('user')->chunkMap(fn ($p) => $p, 500);

        $pdf = Pdf::loadView("pdf.{$viewName}", [
            'user' => $scopedUser,
            'proposals' => $proposals,
        ]);

        $suffix = $scopedUser ? "-{$scopedUser->username}" : '';

        return $pdf->stream("{$viewName}{$suffix}.pdf");
    }
}
