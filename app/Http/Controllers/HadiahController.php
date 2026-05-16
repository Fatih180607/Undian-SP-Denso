<?php

namespace App\Http\Controllers;

use App\Models\Hadiah;
use App\Models\HadiahKuota;
use App\Models\Peserta;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class HadiahController extends Controller
{
    public function index()
    {
        // 1. Ambil data hadiah beserta rincian kuotanya
        $hadiah = Hadiah::with('kuotaPerPlant')->get();

        // 2. Ambil data semua peserta biar tabel karyawan tetep muncul datanya
        $peserta = Peserta::all();

        // 3. Ambil data plants untuk modal tambah karyawan manual
        $plants = Plant::all();

        // 4. Arahkan langsung ke dashboard_peserta
        return view('dashboard_peserta', compact('hadiah', 'peserta', 'plants'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input Form Hadiah
        $request->validate([
            'nama_hadiah'         => 'required|string|max:255',
            'tipe_hadiah'         => 'required|in:all_plant,per_plant',
            'total_kuota_global'  => 'required|integer|min:0',
            'foto_hadiah'         => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Diwajibkan saat buat baru
        ]);

        $data = $request->only(['nama_hadiah', 'tipe_hadiah', 'total_kuota_global']);
        $data['is_active'] = 1;

        // 2. Proses Upload Foto Langsung ke Folder Public
        if ($request->hasFile('foto_hadiah')) {
            $file = $request->file('foto_hadiah');

            // Membuat nama file unik yang aman
            $nama_foto = 'hadiah_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Pindahkan langsung ke public/uploads/hadiah
            $file->move(public_path('uploads/hadiah'), $nama_foto);

            // Simpan path relatifnya ke database
            $data['foto_hadiah'] = 'uploads/hadiah/' . $nama_foto;
        }

        // 3. Simpan Data Hadiah Utama
        $hadiah = Hadiah::create($data);

        // 4. JIKA TIPE PER PLANT: Simpan Rincian Kuota ke tabel hadiah_kuota
        if ($request->tipe_hadiah == 'per_plant' && $request->has('kuota')) {
            foreach ($request->kuota as $item) {
                if (isset($item['jumlah']) && $item['jumlah'] > 0) {
                    HadiahKuota::create([
                        'hadiah_id'       => $hadiah->id,
                        'target_plant'    => $item['plant'],
                        'label_tampilan'  => $item['label'] ?? $item['plant'],
                        'jumlah_pemenang' => $item['jumlah']
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Master Hadiah baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $hadiah = Hadiah::findOrFail($id);

        $request->validate([
            'nama_hadiah' => 'required|string|max:255',
            'foto_hadiah' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $hadiah->nama_hadiah = $request->nama_hadiah;

        // Proses Update Foto jika user mengunggah foto baru
        if ($request->hasFile('foto_hadiah')) {
            // Hapus foto lama di folder jika file fisiknya ada
            if ($hadiah->foto_hadiah && File::exists(public_path($hadiah->foto_hadiah))) {
                File::delete(public_path($hadiah->foto_hadiah));
            }

            $file = $request->file('foto_hadiah');
            $nama_foto = 'hadiah_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/hadiah'), $nama_foto);

            $hadiah->foto_hadiah = 'uploads/hadiah/' . $nama_foto;
        }

        $hadiah->save();

        return redirect()->back()->with('success', 'Master Hadiah berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $hadiah = Hadiah::findOrFail($id);

        // Hapus berkas gambar dari folder sebelum menghapus data dari database
        if ($hadiah->foto_hadiah && File::exists(public_path($hadiah->foto_hadiah))) {
            File::delete(public_path($hadiah->foto_hadiah));
        }

        $hadiah->delete();
        return redirect()->back()->with('success', 'Hadiah berhasil dihapus dari sistem!');
    }
}
