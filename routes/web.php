<?php

use App\Http\Controllers\UndianController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\HadiahController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Aplikasi Undian Digital Denso
|--------------------------------------------------------------------------
*/

// --- 1. HALAMAN DASHBOARD UTAMA ADMIN ---
// Menampilkan halaman dashboard utama yang berisi list data peserta, dropdown plant, dan data hadiah
Route::get('/', [UndianController::class, 'index'])->name('peserta.index');


// --- 2. OPERASI DATA KARYAWAN / PESERTA ---
Route::post('/peserta/store', [UndianController::class, 'store'])->name('peserta.store');
Route::put('/peserta/update/{id}', [UndianController::class, 'update'])->name('peserta.update');
Route::delete('/peserta/hapus/{id}', [UndianController::class, 'destroy'])->name('peserta.destroy');
Route::post('/peserta/import', [UndianController::class, 'importCsv'])->name('peserta.import');
Route::post('/peserta/reset', [UndianController::class, 'resetMenang'])->name('peserta.reset');
Route::post('/peserta/delete-all', [UndianController::class, 'deleteAll'])->name('peserta.deleteAll');


// --- 3. MANAJEMEN MASTER HADIAH ---
Route::post('/hadiah/store', [HadiahController::class, 'store'])->name('hadiah.store');
Route::put('/hadiah/update/{id}', [HadiahController::class, 'update'])->name('hadiah.update');
Route::delete('/hadiah/hapus/{id}', [HadiahController::class, 'destroy'])->name('hadiah.destroy');


// --- 4. MANAJEMEN MASTER PLANT ---
Route::get('/plants', [PlantController::class, 'index'])->name('plant.index');
Route::post('/plants/store', [PlantController::class, 'store'])->name('plant.store');
Route::delete('/plants/hapus/{id}', [PlantController::class, 'destroy'])->name('plant.destroy');
Route::resource('plants', PlantController::class);


// --- 5. SCREEN LIVE UNDIAN UTAMA (PANGGUNG LIVE STREAM) ---
// Route AJAX untuk memproses putaran teks acak bayangan (running text NPK) biar meriah
Route::post('/undian/kocok-proses', [UndianController::class, 'kocokProses'])->name('undian.proses_kocok_proses');

// Route AJAX Utama untuk mengunci semua pemenang sekaligus di DB sesuai total kuota hadiah (Multi-Draw)
Route::post('/api/undian/proses-kocok-kuota', [UndianController::class, 'kocokSesuaiKuota'])->name('api.undian.kocok_kuota');

// Halaman panggung live screen utama yang di-load dalam bentuk fullscreen (Menerima parameter ID Hadiah dari Popup)
Route::get('/undian/kocok/{hadiah_id}', [UndianController::class, 'halamanKocok'])->name('undian.kocok');


// --- 6. ROUTE CADANGAN / ALTERNATIF ---
Route::post('/undian/proses', [UndianController::class, 'prosesKocok'])->name('undian.proses');
Route::post('/undi/proses-borongan', [UndianController::class, 'LogikaUndianBorongan'])->name('undi.borongan');
Route::post('/api/undian/proses-kocok', [UndianController::class, 'prosesKocok']);
Route::get('/undian-page', [UndianController::class, 'halamanKocok'])->name('undian.kocok_page_lama');
