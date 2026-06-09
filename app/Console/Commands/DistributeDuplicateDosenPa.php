<?php

namespace App\Console\Commands;

use App\Models\ProposalMahasiswa;
use App\Models\User;
use Illuminate\Console\Command;

class DistributeDuplicateDosenPa extends Command
{
    protected $signature = 'pkl:distribute-duplicate-dosen-pa {--dry-run : Preview distribution without writing}';

    protected $description = 'Distribute mahasiswa with duplicate dosen names evenly among dosen with same name';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $stats = [
            'total_distributed' => 0,
            'updated_proposals' => 0,
            'dosen_groups' => 0,
        ];

        // Get all dosen grouped by name (only duplicates)
        $dosenByName = User::dosen()
            ->get(['name', 'username'])
            ->groupBy('name')
            ->filter(fn ($group) => $group->count() > 1);

        $dosenNipsByName = $dosenByName->map(fn ($group) => $group->pluck('username')->values());
        
        $this->info("Found {$dosenByName->count()} duplicate dosen names");

        foreach ($dosenByName as $dosenName => $dosenList) {
            $dosenNips = $dosenList->pluck('username')->values();
            $dosenCount = $dosenNips->count();

            // Get all mahasiswa assigned to this duplicate name
            $mahasiswaList = User::mahasiswa()
                ->where('nama_dosen_pa', $dosenName)
                ->get(['id', 'username', 'nama_dosen_pa']);

            if ($mahasiswaList->isEmpty()) {
                continue;
            }

            $stats['dosen_groups']++;

            // Get proposals to separate PKL vs MSIB
            $mahasiswaWithProposals = $mahasiswaList->map(function ($mhs) {
                $proposal = ProposalMahasiswa::where('user_id', $mhs->id)->first();
                return [
                    'user_id' => $mhs->id,
                    'username' => $mhs->username,
                    'jns_pkl' => $proposal?->jns_pkl ?? 'Unknown',
                ];
            });

            // Separate by type
            $magang = $mahasiswaWithProposals->filter(fn ($m) => $m['jns_pkl'] === 'Magang')->values();
            $msib = $mahasiswaWithProposals->filter(fn ($m) => $m['jns_pkl'] !== 'Magang')->values();

            // Distribute evenly
            $assignments = [];
            
            // Distribute Magang
            $magang->chunk(max(1, (int) ceil($magang->count() / $dosenCount)))->each(function ($chunk, $index) use ($dosenNips, &$assignments) {
                $dosenNip = $dosenNips[$index % $dosenNips->count()];
                foreach ($chunk as $mhs) {
                    $assignments[$mhs['user_id']] = $dosenNip;
                }
            });

            // Distribute MSIB
            $msib->chunk(max(1, (int) ceil($msib->count() / $dosenCount)))->each(function ($chunk, $index) use ($dosenNips, &$assignments) {
                $dosenNip = $dosenNips[$index % $dosenNips->count()];
                foreach ($chunk as $mhs) {
                    $assignments[$mhs['user_id']] = $dosenNip;
                }
            });

            // Apply assignments
            foreach ($assignments as $userId => $dosenNip) {
                if (!$dryRun) {
                    User::where('id', $userId)->update(['nama_dosen_pa' => $dosenNip]);
                }
                $stats['total_distributed']++;
            }

            // Update proposals
            foreach ($assignments as $userId => $dosenNip) {
                if (!$dryRun) {
                    ProposalMahasiswa::where('user_id', $userId)->update(['dosen_pa' => $dosenNip]);
                }
                $stats['updated_proposals']++;
            }

            // Show distribution summary
            $distribution = collect($assignments)->groupBy(fn ($nip) => $nip)->map(fn ($group) => $group->count());
            $this->info("  {$dosenName}: " . $distribution->map(fn ($count, $nip) => "{$nip}={$count}")->implode(', '));
        }

        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Dosen groups processed', $stats['dosen_groups']],
                ['Mahasiswa distributed', $stats['total_distributed']],
                ['Proposals updated', $stats['updated_proposals']],
            ]
        );

        if ($dryRun) {
            $this->info('Dry run only. Run without --dry-run to write changes.');
        } else {
            $this->info('Distribution complete!');
        }

        return self::SUCCESS;
    }
}
