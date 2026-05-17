<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peserta;
use App\Models\Plant;
use App\Models\Hadiah;
use Illuminate\Support\Facades\DB;

class PesertaController extends Controller
{
    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $fileData = file_get_contents($file->getRealPath());

        // Bersihkan BOM (karakter aneh dari excel)
        $fileData = str_replace("\xEF\xBB\xBF", "", $fileData);

        $rows = explode("\n", str_replace("\r", "", $fileData));
        $count = 0;

        foreach ($rows as $key => $row) {
            if ($key == 0 || empty(trim($row))) continue;

            $data = str_getcsv($row, ";");

            if (isset($data[0]) && !empty(trim($data[0]))) {
                Peserta::updateOrCreate(
                    ['npk' => trim($data[0])],
                    [
                        'nama_karyawan' => isset($data[1]) ? trim($data[1]) : '-',
                        'seksi'         => isset($data[2]) ? trim($data[2]) : '-',
                        'plant'         => isset($data[3]) ? trim($data[3]) : '-',
                        'is_winner'     => 0,
                    ]
                );
                $count++;
            }
        }

        return back()->with('success', "$count data karyawan berhasil diimport.");
    }

    public function store(Request $request)
    {
        $request->validate([
            'npk'           => 'required|string|max:50',
            'nama_karyawan' => 'required|string|max:255',
            'seksi'         => 'required|string|max:100',
            'plant'         => 'required|string|max:100',
        ]);

        Peserta::create([
            'npk'           => $request->npk,
            'nama_karyawan' => $request->nama_karyawan,
            'seksi'         => $request->seksi,
            'plant'         => $request->plant,
            'is_winner'     => 0
        ]);

        return back()->with('success', 'Karyawan baru berhasil ditambahkan manual!');
    }

public function update(Request $request, $id)
{
    $request->validate([
        'npk' => 'required',
        'nama_karyawan' => 'required',
        'seksi' => 'required',
        'plant' => 'required',
    ]);

    // Cari peserta berdasarkan ID dan update datanya
    $peserta = \App\Models\Peserta::findOrFail($id); // Sesuaikan nama model pesertamu
    $peserta->update([
        'npk' => $request->npk,
        'nama_karyawan' => $request->nama_karyawan,
        'seksi' => $request->seksi,
        'plant' => $request->plant,
    ]);

    return redirect()->back()->with('success', 'Data peserta berhasil diperbarui!');
}

    public function destroy($id)
    {
        $peserta = Peserta::findOrFail($id);
        $peserta->delete();
        return back()->with('success', 'Data karyawan berhasil dihapus!');
    }

    public function reset()
    {
        Peserta::query()->update(['is_winner' => 0]);
        return back()->with('success', 'Semua status pemenang telah di-reset menjadi Ready!');
    }

    public function deleteAll()
    {
        Peserta::truncate();
        return back()->with('success', 'Seluruh database peserta telah dikosongkan!');
    }

    // =========================================================================
    // FIX: LOGIKA UNDIAN BORONGAN YANG BEBAS DARI ERROR MERAH VS CODE
    // =========================================================================
    public function LogikaUndianBorongan(Request $request)
    {
        $request->validate([
            'hadiah_id' => 'required|exists:hadiah,id'
        ]);

        $hadiah = Hadiah::with('kuotaPerPlant')->findOrFail($request->hadiah_id);

        return DB::transaction(function () use ($hadiah) {
            // Kita tampung ID dari para pemenang yang didapatkan
            $pemenangIds = [];

            // KONDISI 1: JIKA DOORPRIZE ALL PLANT (GLOBAL)
            if ($hadiah->tipe_hadiah == 'all_plant') {
                $limit = $hadiah->total_kuota_global;

                $pemenangIds = Peserta::where('is_winner', 0)
                                    ->inRandomOrder()
                                    ->limit($limit)
                                    ->pluck('id') // Ambil array ID nya saja
                                    ->toArray();
            }
            // KONDISI 2: JIKA GRANDPRIZE PER PLANT (KHUSUS CABANG LOKASI)
            else {
                $listKuotaPlant = $hadiah->kuotaPerPlant;

                foreach ($listKuotaPlant as $kp) {
                    $ids = Peserta::where('is_winner', 0)
                                ->where('plant', $kp->target_plant)
                                ->inRandomOrder()
                                ->limit($kp->jumlah_pemenang)
                                ->pluck('id')
                                ->toArray();

                    // Gabungkan ID ke array utama
                    $pemenangIds = array_merge($pemenangIds, $ids);
                }
            }

            // Jika tidak ada ID karyawan terkumpul (kosong / habis)
            if (empty($pemenangIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada karyawan yang memenuhi syarat atau kuota hadiah sudah habis!'
                ], 422);
            }

            // Ambil data lengkap peserta berdasarkan ID terkumpul sebagai object Model asli
            $pemenangTerpilih = Peserta::whereIn('id', $pemenangIds)->get();

            // Kunci status pemenang langsung lewat Mass Update (Sangat cepat & VS Code seneng)
            Peserta::whereIn('id', $pemenangIds)->update(['is_winner' => 1]);

            return response()->json([
                'success'  => true,
                'hadiah'   => $hadiah->nama_hadiah,
                'tipe'     => $hadiah->tipe_hadiah,
                'pemenang' => $pemenangTerpilih
            ]);
        });
    }
}
