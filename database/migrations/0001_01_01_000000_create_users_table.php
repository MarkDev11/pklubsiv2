<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('username', 100)->unique()->comment('NIM untuk mahasiswa, NIP untuk dosen/admin, email untuk mentor');
            $table->string('email')->nullable()->comment('Email untuk OTP & notifikasi');
            $table->string('email_bsi')->nullable()->comment('Email BSI untuk Google OAuth');
            $table->string('password')->nullable();
            $table->enum('role', ['mahasiswa', 'dosen', 'mentor', 'admin'])->default('mahasiswa')->index();
            $table->string('nama_dosen_pa', 100)->nullable()->comment('Nama dosen pembimbing akademik');
            $table->enum('jenis', ['Magang', 'Program Magang khusus (PMK/GNIK/MBKM/MSIB/PMMB)'])->nullable();
            $table->string('kd_lokal', 15)->nullable();
            $table->string('phone', 20)->nullable()->comment('Nomor HP untuk WA notification');
            $table->string('google_id')->nullable()->comment('Google OAuth ID');
            $table->string('otp_code', 6)->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->boolean('otp_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index('name');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
