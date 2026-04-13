<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PdfService;
use Illuminate\Http\Response;

class PdfController extends Controller
{
    public function __construct(
        protected PdfService $pdfService,
    ) {}

    public function pklPdf(): Response
    {
        return $this->pdfService->streamPklPdf($this->authenticatedUser());
    }

    public function msibPdf(): Response
    {
        return $this->pdfService->streamMsibPdf($this->authenticatedUser());
    }
}
