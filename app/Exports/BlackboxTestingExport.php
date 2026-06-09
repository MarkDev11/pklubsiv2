<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BlackboxTestingExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new LoginSheet(),
            new AdminSheet(),
            new MahasiswaSheet(),
            new DosenMentorSheet(),
        ];
    }
}
