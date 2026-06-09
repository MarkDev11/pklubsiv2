<?php

namespace App\Console\Commands;

use App\Models\ProposalMahasiswa;
use App\Models\User;
use Illuminate\Console\Command;

class BackfillDosenPaNip extends Command
{
    protected $signature = 'pkl:backfill-dosen-pa-nip {--dry-run : Preview changes without writing data}';

    protected $description = 'Backfill mahasiswa and proposal Dosen PA values from legacy names to dosen usernames.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $stats = [
            'already_nip' => 0,
            'updated_users' => 0,
            'updated_proposals' => 0,
            'skipped_duplicate' => 0,
            'skipped_not_found' => 0,
        ];
        $resolvedUserNips = [];
        $duplicateSkips = [];
        $notFoundSkips = [];

        $dosenByUsername = User::dosen()->pluck('username')->flip();
        $dosenByName = User::dosen()
            ->get(['name', 'username'])
            ->groupBy('name')
            ->map(fn ($rows) => $rows->pluck('username')->values());

        User::mahasiswa()
            ->whereNotNull('nama_dosen_pa')
            ->orderBy('id')
            ->chunkById(500, function ($users) use ($dryRun, $dosenByUsername, $dosenByName, &$stats, &$resolvedUserNips, &$duplicateSkips, &$notFoundSkips) {
                foreach ($users as $user) {
                    $current = trim((string) $user->nama_dosen_pa);

                    if ($current === '') {
                        continue;
                    }

                    if ($dosenByUsername->has($current)) {
                        $stats['already_nip']++;
                        $resolvedUserNips[$user->id] = $current;
                        continue;
                    }

                    $matches = $dosenByName->get($current, collect());

                    if ($matches->count() > 1) {
                        $stats['skipped_duplicate']++;
                        $duplicateSkips[$current][] = $user->username;
                        continue;
                    }

                    if ($matches->count() === 0) {
                        $stats['skipped_not_found']++;
                        $notFoundSkips[$current][] = $user->username;
                        continue;
                    }

                    $nip = $matches->first();
                    $resolvedUserNips[$user->id] = $nip;

                    if (! $dryRun) {
                        $user->forceFill(['nama_dosen_pa' => $nip])->save();
                    }

                    $stats['updated_users']++;
                }
            });

        ProposalMahasiswa::with('user')
            ->whereNotNull('dosen_pa')
            ->orderBy('id')
            ->chunkById(500, function ($proposals) use ($dryRun, $dosenByUsername, $resolvedUserNips, &$stats) {
                foreach ($proposals as $proposal) {
                    $nip = $resolvedUserNips[$proposal->user_id] ?? $proposal->user?->nama_dosen_pa;

                    if (! $nip || ! $dosenByUsername->has($nip) || $proposal->dosen_pa === $nip) {
                        continue;
                    }

                    if (! $dryRun) {
                        $proposal->forceFill(['dosen_pa' => $nip])->save();
                    }

                    $stats['updated_proposals']++;
                }
            });

        foreach ($duplicateSkips as $name => $nimList) {
            $sample = implode(', ', array_slice($nimList, 0, 5));
            $suffix = count($nimList) > 5 ? ', ...' : '';
            $this->warn("SKIP duplicate dosen name: {$name} (".count($nimList)." rows; sample: {$sample}{$suffix})");
        }

        foreach ($notFoundSkips as $name => $nimList) {
            $sample = implode(', ', array_slice($nimList, 0, 5));
            $suffix = count($nimList) > 5 ? ', ...' : '';
            $this->warn("SKIP dosen not found: {$name} (".count($nimList)." rows; sample: {$sample}{$suffix})");
        }

        $this->table(['Metric', 'Count'], collect($stats)->map(fn ($value, $key) => [$key, $value])->all());

        if ($dryRun) {
            $this->info('Dry run only. Run without --dry-run to write changes.');
        }

        return self::SUCCESS;
    }
}
