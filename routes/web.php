    <?php

    use App\Http\Controllers\UndianController;
    use App\Http\Controllers\PesertaController;
    use App\Http\Controllers\PlantController;
    use App\Http\Controllers\HadiahController;
    use Illuminate\Support\Facades\Route;

    /*
    |--------------------------------------------------------------------------
    | Web Routes - Aplikasi Undian Digital Denso
    |--------------------------------------------------------------------------
    */

    // --- 1. HALAMAN DASHBOARD UTAMA ADMIN ---
    Route::get('/', [PesertaController::class, 'index'])->name('peserta.index');


    // --- 2. OPERASI DATA KARYAWAN / PESERTA ---
    Route::post('/peserta/store', [PesertaController::class, 'store'])->name('peserta.store');
    Route::put('/peserta/update/{id}', [PesertaController::class, 'update'])->name('peserta.update');
    Route::delete('/peserta/hapus/{id}', [PesertaController::class, 'destroy'])->name('peserta.destroy');
    Route::post('/peserta/import', [PesertaController::class, 'importCsv'])->name('peserta.import');
Route::post('/peserta/reset', [UndianController::class, 'resetMenang'])->name('peserta.reset');
    Route::post('/peserta/delete-all', [PesertaController::class, 'deleteAll'])->name('peserta.deleteAll');


    // --- 3. MANAJEMEN MASTER HADIAH ---
    Route::post('/hadiah/store', [HadiahController::class, 'store'])->name('hadiah.store');
    Route::put('/hadiah/update/{id}', [HadiahController::class, 'update'])->name('hadiah.update');
    Route::delete('/hadiah/hapus/{id}', [HadiahController::class, 'destroy'])->name('hadiah.destroy');


    // --- 4. MANAJEMEN MASTER PLANT ---
    Route::get('/plants', [PlantController::class, 'index'])->name('plants.index');
    Route::post('/plants/store', [PlantController::class, 'store'])->name('plant.store');
    Route::delete('/plants/hapus/{id}', [PlantController::class, 'destroy'])->name('plant.destroy');
    Route::resource('plants', PlantController::class)->except(['index', 'store', 'destroy']);


// --- 5. SCREEN LIVE UNDIAN UTAMA (PANGGUNG LIVE STREAM) ---
Route::get('/undian/kocok/{hadiah_id?}', [UndianController::class, 'halamanKocok'])->name('undian.kocok');
Route::post('/undian/kocok-proses', [UndianController::class, 'kocokProses'])->name('undian.proses_kocok_proses');

// --- 6. API CORE UNTUK JQUERY LIVE SCREEN (SINKRON SATU PER SATU) ---
// FIX TOTAL: Mengarahkan route satu-slot ke fungsi kocokSatuSlot yang benar!
Route::post('/api/undian/kocok-satu-slot', [UndianController::class, 'kocokSatuSlot'])->name('api.undian.kocok_satu_slot');
Route::post('/api/undian/gugurkan-peserta', [UndianController::class, 'gugurkanPeserta'])->name('api.undian.gugurkan_peserta');

// Route cadangan / borongan (Biarkan saja atau matikan jika tidak dipakai)
Route::post('/api/undian/proses-kocok-kuota', [UndianController::class, 'kocokSesuaiKuota'])->name('api.undian.kocok_kuota');
Route::post('/undian/proses', [UndianController::class, 'prosesKocok'])->name('undian.proses');
