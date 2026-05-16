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
        Schema::create('peserta_undians', function (Blueprint $table) {
            $table->id(); // Ini buat ID otomatis (Primary Key)
            $table->string('npk')->unique(); // NPK karyawan, dibuat unik biar gak double
            $table->string('nama_karyawan');
            $table->string('seksi'); // Kolom Seksi
            $table->string('plant'); // Kolom Plant
            $table->boolean('is_winner')->default(false); // Penanda pemenang (0 = belum, 1 = sudah)
            $table->timestamps(); // Ini otomatis buat kolom created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta_undians');
    }
};
