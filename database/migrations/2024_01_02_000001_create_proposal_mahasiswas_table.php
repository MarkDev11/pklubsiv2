<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('proposal_mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nim', 8)->unique();
            $table->string('nama', 100);
            $table->string('kd_lokal', 15)->nullable();
            $table->string('jns_pkl', 100)->nullable()->comment('Jenis PKL: Magang / PMK/GNIK/MBKM/MSIB/PMMB');
            $table->string('judul_pkl', 255)->nullable();
            $table->string('tempat_riset', 100)->nullable();
            $table->string('nama_mentor', 100)->nullable();
            $table->string('hp_mentor', 20)->nullable();
            $table->string('email_mentor', 100)->nullable();
            $table->string('email_perusahaan', 100)->nullable();
            $table->string('dosen_pa', 100)->nullable();
            $table->string('skm', 255)->nullable()->comment('File Surat Keterangan Magang');
            $table->string('proposal', 255)->nullable()->comment('File Proposal');
            $table->string('lp', 255)->nullable()->comment('File Laporan');
            $table->string('lpp', 255)->nullable()->comment('File Lembar Penilaian');
            $table->string('skp', 255)->nullable()->comment('File Surat Keterangan Perusahaan');
            $table->integer('nilai')->default(0);
            $table->string('penilai', 100)->nullable()->comment('Nama penilai terakhir');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_mahasiswas');
    }
};
