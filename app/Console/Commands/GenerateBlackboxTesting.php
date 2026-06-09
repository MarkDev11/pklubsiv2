<?php

namespace App\Console\Commands;

use App\Exports\BlackboxTestingExport;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

#[Signature('generate:blackbox-testing')]
#[Description('Generate Blackbox Testing Excel file')]
class GenerateBlackboxTesting extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating Blackbox Testing Excel file...');

        $filename = 'Blackbox_Testing_PKLv2.xlsx';
        $path = base_path($filename);

        Excel::store(new BlackboxTestingExport, $filename, null, \Maatwebsite\Excel\Excel::XLSX);

        $this->info("Excel file generated successfully at: {$path}");
        
        return Command::SUCCESS;
    }
}
