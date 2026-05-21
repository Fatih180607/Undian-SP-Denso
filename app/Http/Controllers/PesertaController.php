<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PesertaUndian; // Menggunakan nama model yang benar & singel
use App\Models\Plant;
use App\Models\Hadiah;
use Illuminate\Support\Facades\DB;

class PesertaController extends Controller
{
    /**
     * Menampilkan Halaman Utama Data Peserta dengan Filter Pencarian
     */
    public function index(Request $request)
    {
        $query = PesertaUndian::query();

        // Filter Pencarian Berdasarkan NPK atau Nama Karyawan
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('npk', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_karyawan', 'like', '%' . $request->search . '%');
            });
        }

        // Filter Berdasarkan Cabang / Plant
        if ($request->filled('plant')) {
            $query->where('plant', $request->plant);
        }

        // Filter Berdasarkan Status Kondisi Undian (Menggunakan is_winner angka asli db)
        if ($request->filled('status')) {
            if ($request->status === 'gugur') {
                $query->where('is_winner', 2); // 2 = Gugur
            } elseif ($request->status === 'winner') {
                $query->where('is_winner', 1); // 1 = Menang
            } elseif ($request->status === 'ready') {
                $query->where('is_winner', 0); // 0 = Ready
            }
        }

        $peserta = $query->latest()->get();

        // AMBIL DATA DARI TABEL PLANTS UNTUK ISI DROPDOWN SECARA DINAMIS
        $plants = Plant::all();
        $hadiah = Hadiah::with('kuotaPerPlant')->get();

        // Diarahkan ke file view blade dashboard_peserta Anda
        return view('dashboard_peserta', compact('peserta', 'plants', 'hadiah'));
    }

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
                PesertaUndian::updateOrCreate(
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
            'npk'           => 'required|string|max:50|unique:peserta_undians,npk',
            'nama_karyawan' => 'required|string|max:255',
            'seksi'         => 'required|string|max:100',
            'plant'         => 'required|string|max:100',
        ]);

        PesertaUndian::create([
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
            'npk' => 'required|unique:peserta_undians,npk,' . $id,
            'nama_karyawan' => 'required',
            'seksi' => 'required',
            'plant' => 'required',
        ]);

        $peserta = PesertaUndian::findOrFail($id);
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
        $peserta = PesertaUndian::findOrFail($id);
        $peserta->delete();
        return back()->with('success', 'Data karyawan berhasil dihapus!');
    }

    public function reset()
    {
        PesertaUndian::query()->update(['is_winner' => 0]);
        return back()->with('success', 'Semua status pemenang telah di-reset menjadi Ready!');
    }

    public function deleteAll()
    {
        PesertaUndian::truncate();
        return back()->with('success', 'Seluruh database peserta telah dikosongkan!');
    }

    // =========================================================================
    // FITUR UNDIAN BORONGAN LOGIKANYA TETAP UTUH & AMAN
    // =========================================================================
    public function LogikaUndianBorongan(Request $request)
    {
        $request->validate([
            'hadiah_id' => 'required|exists:hadiah,id'
        ]);

        $hadiah = Hadiah::with('kuotaPerPlant')->findOrFail($request->hadiah_id);

        return DB::transaction(function () use ($hadiah) {
            $pemenangIds = [];

            // KONDISI 1: JIKA DOORPRIZE ALL PLANT (GLOBAL)
            if ($hadiah->tipe_hadiah == 'all_plant') {
                $limit = $hadiah->total_kuota_global;

                $pemenangIds = PesertaUndian::where('is_winner', 0)
                                    ->inRandomOrder()
                                    ->limit($limit)
                                    ->pluck('id')
                                    ->toArray();
            }
            // KONDISI 2: JIKA GRANDPRIZE PER PLANT (KHUSUS CABANG LOKASI)
            else {
                $listKuotaPlant = $hadiah->kuotaPerPlant;

                foreach ($listKuotaPlant as $kp) {
                    $ids = PesertaUndian::where('is_winner', 0)
                                ->where('plant', $kp->target_plant)
                                ->inRandomOrder()
                                ->limit($kp->jumlah_pemenang)
                                ->pluck('id')
                                ->toArray();

                    $pemenangIds = array_merge($pemenangIds, $ids);
                }
            }

            if (empty($pemenangIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada karyawan yang memenuhi syarat atau kuota hadiah sudah habis!'
                ], 422);
            }

            $pemenangTerpilih = PesertaUndian::whereIn('id', $pemenangIds)->get();

            PesertaUndian::whereIn('id', $pemenangIds)->update(['is_winner' => 1]);

            return response()->json([
                'success'  => true,
                'hadiah'   => $hadiah->nama_hadiah,
                'tipe'     => $hadiah->tipe_hadiah,
                'pemenang' => $pemenangTerpilih
            ]);
        });
    }
}
