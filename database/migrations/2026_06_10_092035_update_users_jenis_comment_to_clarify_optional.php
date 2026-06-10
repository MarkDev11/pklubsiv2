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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('jenis', ['Magang', 'Program Magang khusus (PMK/GNIK/MBKM/MSIB/PMMB)'])
                ->nullable()
                ->comment('(Opsional) Jenis PKL dipilih mahasiswa saat submit proposal, bukan saat create user')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('jenis', ['Magang', 'Program Magang khusus (PMK/GNIK/MBKM/MSIB/PMMB)'])
                ->nullable()
                ->comment(null)
                ->change();
        });
    }
};
