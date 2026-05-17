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
// Kita arahkan ke UndianController@index karena method ini yang sudah lengkap melempar data $peserta, $plants, dan $hadiah
Route::get('/', [UndianController::class, 'index'])->name('peserta.index');


// --- 2. OPERASI DATA KARYAWAN / PESERTA ---
// Sekarang semua dialihkan ke UndianController sesuai fungsi yang sudah kamu buat di dalamnya
Route::post('/peserta/store', [UndianController::class, 'store'])->name('peserta.store');
Route::put('/peserta/update/{id}', [UndianController::class, 'update'])->name('peserta.update');
Route::delete('/peserta/hapus/{id}', [UndianController::class, 'destroy'])->name('peserta.destroy');
Route::post('/peserta/import', [UndianController::class, 'importCsv'])->name('peserta.import');
Route::post('/peserta/reset', [UndianController::class, 'resetMenang'])->name('peserta.reset');
Route::post('/peserta/delete-all', [UndianController::class, 'deleteAll'])->name('peserta.deleteAll');


// --- 3. MANAJEMEN MASTER HADIAH ---
// Form input hadiah tetap dikelola oleh HadiahController agar kodingan tidak menumpuk di satu tempat
Route::post('/hadiah/store', [HadiahController::class, 'store'])->name('hadiah.store');
Route::put('/hadiah/update/{id}', [HadiahController::class, 'update'])->name('hadiah.update');
Route::delete('/hadiah/hapus/{id}', [HadiahController::class, 'destroy'])->name('hadiah.destroy');


// --- 4. MANAJEMEN MASTER PLANT ---
Route::get('/plants', [PlantController::class, 'index'])->name('plant.index');
Route::post('/plants/store', [PlantController::class, 'store'])->name('plant.store');
Route::delete('/plants/hapus/{id}', [PlantController::class, 'destroy'])->name('plant.destroy');


// --- 5. SCREEN LIVE UNDIAN UTAMA (PANGGUNG UTAMA) ---
Route::get('/undian-page', [UndianController::class, 'halamanKocok'])->name('undian.kocok');
Route::post('/undian/proses', [UndianController::class, 'prosesKocok'])->name('undian.proses');

// Route Gacha Borongan
Route::post('/undi/proses-borongan', [UndianController::class, 'LogikaUndianBorongan'])->name('undi.borongan');

Route::resource('plants', PlantController::class);
