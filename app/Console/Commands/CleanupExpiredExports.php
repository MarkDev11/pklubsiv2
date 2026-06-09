<?php

namespace App\Console\Commands;

use App\Models\Export;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

#[Signature('exports:cleanup')]
#[Description('Delete expired export files and records')]
class CleanupExpiredExports extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning up expired exports...');

        $expired = Export::where('expires_at', '<', now())
                        ->where('status', 'completed')
                        ->get();

        if ($expired->isEmpty()) {
            $this->info('No expired exports found.');
            return 0;
        }

        $deleted = 0;

        foreach ($expired as $export) {
            // Delete file from storage
            if ($export->path && Storage::exists($export->path)) {
                Storage::delete($export->path);
                $this->line("Deleted file: {$export->path}");
            }

            // Delete database record
            $export->delete();
            $deleted++;
        }

        $this->info("Cleanup complete. Deleted {$deleted} expired exports.");

        return 0;
    }
}
