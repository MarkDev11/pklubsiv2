<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('opening_hours', function (Blueprint $table) {
            $table->id();
            $table->dateTime('open_time')->nullable()->comment('Buka pendaftaran PKL');
            $table->dateTime('close_time')->nullable()->comment('Tutup pendaftaran PKL');
            $table->dateTime('open_laporan')->nullable()->comment('Buka upload laporan');
            $table->dateTime('close_laporan')->nullable()->comment('Tutup upload laporan');
            $table->dateTime('open_nilai')->nullable()->comment('Buka input nilai');
            $table->dateTime('close_nilai')->nullable()->comment('Tutup input nilai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opening_hours');
    }
};
