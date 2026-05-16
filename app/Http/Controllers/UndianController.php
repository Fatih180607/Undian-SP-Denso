<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hadiah;
use App\Models\PesertaUndian;
use App\Models\Plant; // Tambahkan ini supaya bisa baca data Plant

class UndianController extends Controller
{
    /**
     * Menampilkan Halaman Dashboard (List & Form Input)
     */
public function index()
{
    // 1. Ambil data peserta terbaru
    $peserta = PesertaUndian::orderBy('created_at', 'desc')->get();

    // 2. Ambil data plant untuk dropdown di form modal agar variabel $plants TIDAK undefined
    $plants = Plant::all();

    // 3. AMBIL DATA HADIAH BESERTA RELASINYA (Ini yang bikin error null di foreach)
    $hadiah = Hadiah::with('kuotaPerPlant')->get();

    // 4. Kirim semuanya sekaligus ke view dashboard_peserta
    return view('dashboard_peserta', compact('peserta', 'plants', 'hadiah'));
}

    /**
     * Menyimpan data peserta baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'npk' => 'required|unique:peserta_undians,npk',
            'nama_karyawan' => 'required',
            'seksi' => 'required',
            'plant' => 'required',
        ], [
            'npk.unique' => 'NPK ini sudah terdaftar!',
        ]);

        PesertaUndian::create([
            'npk' => $request->npk,
            'nama_karyawan' => $request->nama_karyawan,
            'seksi' => $request->seksi,
            'plant' => $request->plant,
            'is_winner' => false
        ]);

        return redirect()->back()->with('success', 'Peserta berhasil ditambahkan!');
    }

    /**
     * Update data peserta (Fungsi Edit)
     */
    public function update(Request $request, $id)
    {
        $peserta = PesertaUndian::findOrFail($id);

        $request->validate([
            'npk' => 'required|unique:peserta_undians,npk,' . $id,
            'nama_karyawan' => 'required',
            'seksi' => 'required',
            'plant' => 'required',
        ]);

        $peserta->update([
            'npk' => $request->npk,
            'nama_karyawan' => $request->nama_karyawan,
            'seksi' => $request->seksi,
            'plant' => $request->plant,
        ]);

        return redirect()->back()->with('success', 'Data peserta berhasil diperbarui!');
    }

    /**
     * Menghapus data peserta
     */
    public function destroy($id)
    {
        $peserta = PesertaUndian::findOrFail($id);
        $peserta->delete();

        return redirect()->back()->with('success', 'Data peserta berhasil dihapus!');
    }

    /**
     * Reset semua status kemenangan
     */
    public function resetMenang()
    {
        PesertaUndian::query()->update(['is_winner' => false]);
        return redirect()->back()->with('success', 'Semua status kemenangan berhasil di-reset!');
    }

    /**
     * Fitur Import CSV
     */
 public function importCsv(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:csv,txt'
    ]);

    $filePath = $request->file('file')->getRealPath();
    $file = fopen($filePath, 'r');

    // 1. Baca baris pertama (header) untuk mendeteksi pembatas otomatis
    $firstLine = fgets($file);

    // Cari tahu apakah file ini pakai pembatas titik koma (;) atau koma (,)
    $separator = (strpos($firstLine, ';') !== false) ? ';' : ',';

    // Reset kembali posisi pointer file ke awal setelah membaca baris pertama
    rewind($file);

    // Skip baris header dengan pembatas yang sudah dideteksi
    fgetcsv($file, 1000, $separator);

    // 2. Loop data baris per baris
    while (($row = fgetcsv($file, 1000, $separator)) !== FALSE) {
        // Lewati jika baris kosong atau NPK-nya tidak ada
        if (!isset($row[0]) || empty(trim($row[0]))) {
            continue;
        }

        PesertaUndian::updateOrCreate(
            ['npk' => trim($row[0])], // Cek berdasarkan NPK agar tidak duplikat
            [
                'nama_karyawan' => isset($row[1]) ? trim($row[1]) : '-',
                'seksi'         => isset($row[2]) ? trim($row[2]) : '-',
                'plant'         => isset($row[3]) ? trim($row[3]) : '-',
                'is_winner'     => false
            ]
        );
    }

    fclose($file);
    return redirect()->back()->with('success', 'Data CSV berhasil di-import dengan aman!');
}

    /**
     * Menampilkan Halaman Pengundian (Gacha)
     */
    public function halamanKocok()
    {
        return view('halaman_kocok');
    }

    /**
     * Logika Mengacak Pemenang
     */
    public function prosesKocok()
    {
        // Ambil 1 peserta secara acak yang BELUM menang (is_winner = 0)
        $pemenang = PesertaUndian::where('is_winner', false)
            ->inRandomOrder()
            ->first();

        if ($pemenang) {
            $pemenang->update(['is_winner' => true]);

            return response()->json([
                'success' => true,
                'npk' => $pemenang->npk,
                'nama' => $pemenang->nama_karyawan,
                'seksi' => $pemenang->seksi,
                'plant' => $pemenang->plant
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tidak ada peserta yang tersisa untuk diundi!'
        ], 404);
    }
    public function deleteAll()
{
    PesertaUndian::truncate();
    return redirect()->back()->with('success', 'Seluruh database peserta telah dikosongkan!');
}
}
