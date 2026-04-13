<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\ProposalMahasiswa;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Mail\SystemNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProposalService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function store(User $user, array $data, ?UploadedFile $skmFile): ProposalMahasiswa
    {
        return DB::transaction(function () use ($user, $data, $skmFile) {
            $this->ensureMentorAccountExists(
                $data['email_mentor'],
                $data['nama_mentor'],
                $data['hp_mentor']
            );

            $skmName = null;
            if ($skmFile) {
                $skmName = $this->uploadFile($skmFile, 'skm', $data['nim']);
            }

            $proposal = ProposalMahasiswa::create([
                'user_id' => $user->id,
                'nim' => $data['nim'],
                'nama' => $data['nama'],
                'kd_lokal' => $data['kd_lokal'] ?? null,
                'jns_pkl' => $data['jns_pkl'],
                'judul_pkl' => $data['judul_pkl'],
                'tempat_riset' => $data['tempat_riset'],
                'nama_mentor' => $data['nama_mentor'],
                'hp_mentor' => $data['hp_mentor'],
                'email_mentor' => $data['email_mentor'],
                'email_perusahaan' => $data['email_perusahaan'] ?? null,
                'dosen_pa' => $user->nama_dosen_pa,
                'skm' => $skmName,
            ]);

            ActivityLog::log($user->id, 'Input proposal PKL');

            // Send Notifications
            $this->sendProposalNotifications($proposal, $user);

            return $proposal;
        });
    }

    /**
     * Send email notifications to relevant parties after proposal submission.
     */
    protected function sendProposalNotifications(ProposalMahasiswa $proposal, User $mahasiswa): void
    {
        try {
            // 1. Notify Mahasiswa (Confirmation)
            // Dinonaktifkan sesuai permintaan:
            // Mail::to($mahasiswa->email)->send(new SystemNotification(
            //     'Konfirmasi Pengajuan PKL - ' . $mahasiswa->name,
            //     'Pengajuan PKL Berhasil',
            //     "Halo {$mahasiswa->name}, pengajuan PKL Anda dengan judul '{$proposal->judul_pkl}' telah berhasil dikirim dan sedang dalam proses review.",
            //     route('mahasiswa.proposal.index')
            // ));

            // 2. Notify Mentor Industri
            if ($proposal->email_mentor) {
                Mail::to($proposal->email_mentor)->send(new SystemNotification(
                    'Pendaftaran Mahasiswa PKL - ' . $mahasiswa->name,
                    'Permintaan Bimbingan PKL',
                    "Halo {$proposal->nama_mentor}, mahasiswa kami {$mahasiswa->name} telah mengajukan permintaan bimbingan PKL di tempat Anda dengan judul '{$proposal->judul_pkl}'.",
                    null
                ));
            }

            // 3. Notify Dosen PA
            $dosen = User::where('name', $mahasiswa->nama_dosen_pa)->where('role', UserRole::Dosen->value)->first();
            if ($dosen && $dosen->email) {
                Mail::to($dosen->email)->send(new SystemNotification(
                    'Pengajuan PKL Baru - ' . $mahasiswa->name,
                    'Review Proposal PKL',
                    "Halo {$dosen->name}, mahasiswa bimbingan Anda {$mahasiswa->name} baru saja mengajukan proposal PKL. Silakan melakukan review melalui dashboard.",
                    route('dosen.mahasiswa.pkl')
                ));
            }
        } catch (\Exception $e) {
            // Log error but don't stop the process
            \Log::error('Failed to send proposal notifications: ' . $e->getMessage());
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(ProposalMahasiswa $proposal, User $user, array $data, ?UploadedFile $skmFile): ProposalMahasiswa
    {
        return DB::transaction(function () use ($proposal, $user, $data, $skmFile) {
            $updateData = collect($data)->only([
                'nim', 'nama', 'kd_lokal', 'jns_pkl', 'judul_pkl',
                'tempat_riset', 'nama_mentor', 'hp_mentor',
                'email_mentor', 'email_perusahaan',
            ])->toArray();

            if ($skmFile) {
                $this->deleteOldFile($proposal->skm);
                $updateData['skm'] = $this->uploadFile($skmFile, 'skm', $data['nim']);
            }

            $proposal->update($updateData);

            ActivityLog::log($user->id, 'Update proposal PKL');

            return $proposal;
        });
    }

    public function uploadFile(UploadedFile $file, string $prefix, string $identifier): string
    {
        $fileName = $prefix.'_'.$identifier.'_'.uniqid().'.'.$file->getClientOriginalExtension();
        $file->storeAs('uploads', $fileName, 'public');

        return $fileName;
    }

    public function deleteOldFile(?string $fileName): void
    {
        if (! $fileName) {
            return;
        }

        if (Storage::disk('public')->exists('uploads/'.$fileName)) {
            Storage::disk('public')->delete('uploads/'.$fileName);

            return;
        }

        $legacyPath = public_path('uploads/'.$fileName);
        if (file_exists($legacyPath)) {
            unlink($legacyPath);
        }
    }

    protected function ensureMentorAccountExists(string $email, string $name, string $phone): void
    {
        if (User::where('username', $email)->exists()) {
            return;
        }

        User::create([
            'name' => $name,
            'username' => $email,
            'email' => $email,
            'password' => Str::random(16),
            'role' => UserRole::Mentor->value,
            'phone' => $phone,
        ]);
    }
}
