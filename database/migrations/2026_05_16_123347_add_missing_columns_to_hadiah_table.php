<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hadiah', function (Blueprint $table) {
            // Menambahkan kolom foto, tipe distribusi, dan kuota global jika belum ada
            if (!Schema::hasColumn('hadiah', 'foto_hadiah')) {
                $table->string('foto_hadiah')->nullable()->after('nama_hadiah');
            }
            if (!Schema::hasColumn('hadiah', 'tipe_hadiah')) {
                $table->string('tipe_hadiah')->default('all_plant')->after('foto_hadiah'); // all_plant / per_plant
            }
            if (!Schema::hasColumn('hadiah', 'total_kuota_global')) {
                $table->integer('total_kuota_global')->default(1)->after('tipe_hadiah');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hadiah', function (Blueprint $table) {
            $table->dropColumn(['foto_hadiah', 'tipe_hadiah', 'total_kuota_global']);
        });
    }
};
