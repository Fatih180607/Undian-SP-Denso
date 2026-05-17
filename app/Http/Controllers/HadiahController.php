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
        $hadiah = Hadiah::with('kuotaPerPlant')->get();
        $peserta = Peserta::all();
        $plants = Plant::all();

        return view('dashboard_peserta', compact('hadiah', 'peserta', 'plants'));
    }

    // 1. PROSES SIMPAN HADIAH BARU (STORE)
    public function store(Request $request)
    {
        $request->validate([
            'nama_hadiah'         => 'required|string|max:255',
            'tipe_hadiah'         => 'required|in:all_plant,per_plant',
            'total_kuota_global'  => 'nullable|integer|min:0',
            'foto_hadiah'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'nama_hadiah'         => $request->nama_hadiah,
            'tipe_hadiah'         => $request->tipe_hadiah,
            'total_kuota_global'  => ($request->tipe_hadiah == 'all_plant') ? ($request->total_kuota_global ?? 1) : 0,
            'is_active'           => 1
        ];

        if ($request->hasFile('foto_hadiah')) {
            $file = $request->file('foto_hadiah');
            $nama_foto = 'hadiah_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/hadiah'), $nama_foto);
            $data['foto_hadiah'] = 'uploads/hadiah/' . $nama_foto;
        }

        // Simpan ke tabel 'hadiah' dan dapatkan ID-nya
        $hadiah = Hadiah::create($data);

        // Jika tipenya per_plant, looping & simpan ke tabel 'hadiah_kuota'
        if ($request->tipe_hadiah == 'per_plant' && $request->has('kuota')) {
            foreach ($request->kuota as $item) {
                // Hanya simpan jika jumlah pemenang di atas 0
                if (isset($item['jumlah']) && $item['jumlah'] > 0) {
                    HadiahKuota::create([
                        'hadiah_id'       => $hadiah->id, // ID dari tabel hadiah utama
                        'target_plant'    => $item['plant'],
                        'label_tampilan'  => $item['label'] ?? $item['plant'],
                        'jumlah_pemenang' => $item['jumlah']
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Master Hadiah baru berhasil ditambahkan!');
    }

    // 2. PROSES UPDATE HADIAH & DETAIL KUOTA PLANT (UPDATE)
    public function update(Request $request, $id)
    {
        $hadiah = Hadiah::findOrFail($id);

        $request->validate([
            'nama_hadiah' => 'required|string|max:255',
            'tipe_hadiah' => 'required|in:all_plant,per_plant',
            'foto_hadiah' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $hadiah->nama_hadiah = $request->nama_hadiah;
        $hadiah->tipe_hadiah = $request->tipe_hadiah;

        if ($request->tipe_hadiah == 'all_plant') {
            $hadiah->total_kuota_global = $request->total_kuota_global ?? 0;

            // Opsional: Jika tipe berubah jadi Global, hapus batasan per plant yang lama di DB
            HadiahKuota::where('hadiah_id', $hadiah->id)->delete();
        } else {
            $hadiah->total_kuota_global = 0;
        }

        if ($request->hasFile('foto_hadiah')) {
            if ($hadiah->foto_hadiah && File::exists(public_path($hadiah->foto_hadiah))) {
                File::delete(public_path($hadiah->foto_hadiah));
            }

            $file = $request->file('foto_hadiah');
            $nama_foto = 'hadiah_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/hadiah'), $nama_foto);
            $hadiah->foto_hadiah = 'uploads/hadiah/' . $nama_foto;
        }

        $hadiah->save();

        // SINKRONISASI COCOK UNTUK STRUKTUR TABEL `hadiah_kuota` SAAT UPDATE
        if ($request->tipe_hadiah == 'per_plant' && $request->has('kuota')) {
            foreach ($request->kuota as $item) {
                // Ambil target plant dari item data array form
                $targetPlant = $item['plant'];
                $jumlah = $item['jumlah'] ?? 0;

                if ($jumlah > 0) {
                    // Jika kuota diisi > 0, insert atau update datanya
                    HadiahKuota::updateOrCreate(
                        [
                            'hadiah_id'    => $hadiah->id,
                            'target_plant' => $targetPlant // Dicari berdasarkan paduan 2 kolom ini
                        ],
                        [
                            'label_tampilan'  => $item['label'] ?? $targetPlant,
                            'jumlah_pemenang' => $jumlah
                        ]
                    );
                } else {
                    // Jika operator set Qty-nya menjadi 0, hapus baris kuota plant tersebut dari DB agar clean
                    HadiahKuota::where('hadiah_id', $hadiah->id)
                               ->where('target_plant', $targetPlant)
                               ->delete();
                }
            }
        }

        return redirect()->back()->with('success', 'Master Hadiah dan kuota berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $hadiah = Hadiah::findOrFail($id);

        if ($hadiah->foto_hadiah && File::exists(public_path($hadiah->foto_hadiah))) {
            File::delete(public_path($hadiah->foto_hadiah));
        }

        // Karena relasi database, pastikan hapus juga anak-anak kuotanya di tabel hadiah_kuota
        HadiahKuota::where('hadiah_id', $hadiah->id)->delete();

        $hadiah->delete();
        return redirect()->back()->with('success', 'Hadiah berhasil dihapus dari sistem!');
    }
}
