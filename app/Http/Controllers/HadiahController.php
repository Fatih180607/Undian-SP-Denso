<?php

namespace App\Http\Controllers;

use App\Models\Hadiah;
use App\Models\HadiahKuota;
use App\Models\Peserta;
use App\Models\Plant;
use Illuminate\Http\Request;

class HadiahController extends Controller
{
    public function index()
    {
        // 1. Ambil data hadiah beserta rincian kuotanya
        $hadiah = Hadiah::with('kuotaPerPlant')->get();

        // 2. Ambil data semua peserta biar tabel karyawan lu tetep muncul datanya
        $peserta = Peserta::all();

        // 3. FIX: Arahkan langsung ke dashboard_peserta (bukan hadiah.index)
        return view('dashboard_peserta', compact('hadiah', 'peserta'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input Form Hadiah
        $request->validate([
            'nama_hadiah'         => 'required|string|max:255',
            'tipe_hadiah'         => 'required|in:all_plant,per_plant',
            'total_kuota_global'  => 'required|integer|min:0',
            'foto_hadiah'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['nama_hadiah', 'tipe_hadiah', 'total_kuota_global']);
        $data['is_active'] = 1;

        // 2. Proses Upload Foto jika ada
        if ($request->hasFile('foto_hadiah')) {
            $file = $request->file('foto_hadiah');
            $nama_foto = $file->hashName();
            $file->storeAs('public/hadiah', $nama_foto);
            $data['foto_hadiah'] = 'hadiah/' . $nama_foto;
        }

        // 3. Simpan Data Hadiah Utama
        $hadiah = Hadiah::create($data);

        // 4. JIKA TIPE PER PLANT: Simpan Rincian Kuota ke tabel hadiah_kuota
        if ($request->tipe_hadiah == 'per_plant' && $request->has('kuota')) {
            foreach ($request->kuota as $item) {
                if (isset($item['jumlah']) && $item['jumlah'] > 0) {
                    HadiahKuota::create([
                        'hadiah_id'       => $hadiah->id,
                        'target_plant'    => $item['plant'], // Sesuai nama kolom di DB lu
                        'label_tampilan'  => $item['label'] ?? $item['plant'],
                        'jumlah_pemenang' => $item['jumlah']
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Master Hadiah berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $hadiah = Hadiah::findOrFail($id);
        $hadiah->delete();
        return redirect()->back()->with('success', 'Hadiah berhasil dihapus!');
    }
}
