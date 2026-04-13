<?php

namespace App\Services;

use App\Models\ProposalMahasiswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class MahasiswaService
{
    /**
     * @return array{totalBelum: int, totalProses: int, totalSelesai: int}
     */
    public function getStats(): array
    {
        return [
            'totalBelum' => User::mahasiswa()->whereDoesntHave('proposalMahasiswa')->count(),
            'totalProses' => ProposalMahasiswa::where(function ($q) {
                $q->whereNull('lp')->orWhereNull('lpp')->orWhereNull('skp');
            })->count(),
            'totalSelesai' => ProposalMahasiswa::whereNotNull('lp')
                ->whereNotNull('lpp')
                ->whereNotNull('skp')
                ->count(),
        ];
    }

    /**
     * @return array{recordsTotal: int, recordsFiltered: int, data: array<int, array<string, int|string>>}
     */
    public function getDatatableData(string $keyword = '', string $statusFilter = '', int $start = 0, int $length = 10): array
    {
        $query = User::mahasiswa()->with('proposalMahasiswa');

        $this->applySearch($query, $keyword);
        $this->applyStatusFilter($query, $statusFilter);

        $total = User::mahasiswa()->count();
        $filtered = $query->count();
        $users = $query->orderBy('name', 'asc')->skip($start)->take($length)->get();

        $data = $this->transformDatatableRows($users, $start);

        return [
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ];
    }

    /**
     * @param  Builder<User>  $query
     */
    protected function applySearch(Builder $query, string $keyword): void
    {
        if (! $keyword) {
            return;
        }

        $query->where(function ($q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
                ->orWhere('username', 'like', "%{$keyword}%")
                ->orWhereHas('proposalMahasiswa', function ($p) use ($keyword) {
                    $p->where('tempat_riset', 'like', "%{$keyword}%")
                        ->orWhere('nama_mentor', 'like', "%{$keyword}%");
                });
        });
    }

    /**
     * @param  Builder<User>  $query
     */
    protected function applyStatusFilter(Builder $query, string $statusFilter): void
    {
        switch ($statusFilter) {
            case 'belum':
                $query->whereDoesntHave('proposalMahasiswa');
                break;
            case 'proses':
                $query->whereHas('proposalMahasiswa', function ($p) {
                    $p->where(function ($sub) {
                        $sub->whereNull('lp')->orWhereNull('lpp')->orWhereNull('skp');
                    });
                });
                break;
            case 'selesai':
                $query->whereHas('proposalMahasiswa', function ($p) {
                    $p->whereNotNull('lp')->whereNotNull('lpp')->whereNotNull('skp');
                });
                break;
        }
    }

    /**
     * @param  Collection<int, User>  $users
     * @return array<int, array<string, int|string>>
     */
    protected function transformDatatableRows(Collection $users, int $start): array
    {
        $data = [];

        foreach ($users as $index => $u) {
            $proposal = $u->proposalMahasiswa;
            $status = $this->determineStatus($proposal);

            $data[] = [
                'no' => $start + $index + 1,
                'nim' => e($u->username),
                'nama' => e($u->name),
                'kd_lokal' => e($u->kd_lokal ?? '-'),
                'jenis' => $proposal ? e($proposal->jns_pkl) : '-',
                'tempat' => $proposal ? e($proposal->tempat_riset) : '-',
                'mentor' => $proposal ? e($proposal->nama_mentor) : '-',
                'status_class' => $status['class'],
                'status_label' => $status['label'],
            ];
        }

        return $data;
    }

    /**
     * @return array{class: string, label: string}
     */
    protected function determineStatus(?ProposalMahasiswa $proposal): array
    {
        if (! $proposal) {
            return ['class' => 'badge-red', 'label' => 'Belum Input'];
        }

        if ($proposal->lp && $proposal->lpp && $proposal->skp) {
            return ['class' => 'badge-green', 'label' => 'Selesai (Komplit)'];
        }

        return ['class' => 'badge-yellow', 'label' => 'Sedang Proses'];
    }
}
