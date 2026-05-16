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
        Schema::create('hadiah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_hadiah');
            $table->timestamps();
        });

        Schema::create('hadiah_kuota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hadiah_id')->constrained('hadiah')->onDelete('cascade');
            $table->string('label_tampilan'); // Label yang muncul di layar (Contoh: BEKASI & SUNTER)
            $table->string('target_plant');    // Filter database (Contoh: BEKASI)
            $table->integer('jumlah_pemenang');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus child-nya dulu baru parent-nya biar gak error constraint
        Schema::dropIfExists('hadiah_kuota');
        Schema::dropIfExists('hadiah');
    }
};
