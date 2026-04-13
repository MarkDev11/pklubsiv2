<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MahasiswaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MahasiswaController extends Controller
{
    public function __construct(
        protected MahasiswaService $mahasiswaService
    ) {}

    public function index(): View
    {
        $stats = $this->mahasiswaService->getStats();

        return view('admin.mahasiswa', $stats);
    }

    public function datatable(Request $request): JsonResponse
    {
        $draw = (int) $request->input('draw', 1);
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $keyword = trim($request->input('search.value', '') ?? '');
        $statusFilter = $request->input('status', '') ?? '';

        $result = $this->mahasiswaService->getDatatableData(
            $keyword,
            $statusFilter,
            $start,
            $length
        );

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data' => $result['data'],
        ]);
    }
}
