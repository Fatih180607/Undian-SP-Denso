<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hadiah;
use App\Models\PesertaUndian;
use App\Models\Plant;
use Illuminate\Support\Facades\DB;

class UndianController extends Controller
{
    /**
     * Menampilkan Halaman Dashboard (List & Form Input)
     */
    public function index()
    {
        $peserta = PesertaUndian::orderBy('created_at', 'desc')->get();
        $plants = Plant::all();
        $hadiah = Hadiah::with('kuotaPerPlant')->get();

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
            'is_winner' => 0,
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
     * Reset semua status kemenangan & status hangus kembali ke normal
     */
    public function resetMenang()
    {
        PesertaUndian::query()->update([
            'is_winner' => 0,
            'hadiah_id' => 0
        ]);
        return redirect()->back()->with('success', 'Semua status kemenangan & hangus berhasil di-reset!');
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

        $firstLine = fgets($file);
        $separator = (strpos($firstLine, ';') !== false) ? ';' : ',';

        rewind($file);
        fgetcsv($file, 1000, $separator);

        while (($row = fgetcsv($file, 1000, $separator)) !== FALSE) {
            if (!isset($row[0]) || empty(trim($row[0]))) {
                continue;
            }

            PesertaUndian::updateOrCreate(
                ['npk' => trim($row[0])],
                [
                    'nama_karyawan' => isset($row[1]) ? trim($row[1]) : '-',
                    'seksi'         => isset($row[2]) ? trim($row[2]) : '-',
                    'plant'         => isset($row[3]) ? trim($row[3]) : '-',
                    'is_winner'     => 0
                ]
            );
        }

        fclose($file);
        return redirect()->back()->with('success', 'Data CSV berhasil di-import dengan aman!');
    }

    /**
     * Menampilkan halaman panggung live screen dengan ID Hadiah dari parameter route
     */
    public function halamanKocok(Request $request, $hadiah_id = null)
    {
        if (!$hadiah_id) {
            $hadiah_id = $request->get('hadiah_id');
        }

        $hadiah = Hadiah::with('kuotaPerPlant')->get();
        $hadiahAktif = $hadiah_id ? Hadiah::with('kuotaPerPlant')->find($hadiah_id) : null;

        $totalKuota = 0;
        if ($hadiahAktif) {
            $totalKuota = ($hadiahAktif->tipe_hadiah == 'all_plant')
                ? $hadiahAktif->total_kuota_global
                : $hadiahAktif->kuotaPerPlant->sum('jumlah_pemenang');
        }

        return view('undian_screen', compact('hadiah', 'hadiahAktif', 'totalKuota'));
    }

    /**
     * Menampilkan layar screen undian alternatif
     */
    public function undianScreen(Request $request, $hadiah_id = null)
    {
        return $this->halamanKocok($request, $hadiah_id);
    }

    /**
     * API untuk mengambil detail informasi hadiah secara real-time via AJAX
     */
    public function getDetailHadiah($id)
    {
        $hadiah = Hadiah::with('kuotaPerPlant')->find($id);
        if (!$hadiah) {
            return response()->json(['success' => false, 'message' => 'Hadiah tidak ditemukan']);
        }

        $totalKuota = ($hadiah->tipe_hadiah == 'all_plant')
            ? $hadiah->total_kuota_global
            : $hadiah->kuotaPerPlant->sum('jumlah_pemenang');

        return response()->json([
            'success' => true,
            'hadiah' => $hadiah,
            'total_kuota' => $totalKuota
        ]);
    }

    /**
     * Logika Mengacak Pemenang Tunggal Bawaan Asli
     */
    public function prosesKocok()
    {
        $pemenang = PesertaUndian::where('is_winner', 0)
            ->inRandomOrder()
            ->first();

        if ($pemenang) {
            $pemenang->update(['is_winner' => 1]);

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

    /**
     * Mengosongkan Database Peserta
     */
    public function deleteAll()
    {
        PesertaUndian::truncate();
        return redirect()->back()->with('success', 'Seluruh database peserta telah dikosongkan!');
    }

    /**
     * Logika AJAX Pengacakan Real-time
     */
    public function kocokProses(Request $request)
    {
        $action = $request->action;

        if ($action == 'init_loop') {
            $peserta = PesertaUndian::where('is_winner', 0)
                ->inRandomOrder()
                ->limit(40)
                ->get(['npk', 'nama_karyawan', 'plant', 'seksi']);

            return response()->json(['success' => true, 'data' => $peserta]);
        }
    }

    /**
     * Logika Mengacak Sekaligus Sesuai Jumlah Kuota Hadiah (Multi-Draw)
     */
    public function kocokSesuaiKuota(Request $request)
    {
        $hadiahId = $request->hadiah_id;

        $hadiah = Hadiah::with('kuotaPerPlant')->find($hadiahId);
        if (!$hadiah) {
            return response()->json(['success' => false, 'message' => 'Data hadiah tidak ditemukan!']);
        }

        $totalKuota = ($hadiah->tipe_hadiah == 'all_plant')
            ? $hadiah->total_kuota_global
            : $hadiah->kuotaPerPlant->sum('jumlah_pemenang');

        if ($totalKuota <= 0) {
            return response()->json(['success' => false, 'message' => 'Kuota untuk item hadiah ini masih kosong atau di-set 0!']);
        }

        DB::beginTransaction();
        try {
            $listPemenang = [];

            if ($hadiah->tipe_hadiah == 'all_plant') {
                $kandidat = PesertaUndian::where('is_winner', 0)
                    ->inRandomOrder()
                    ->limit($totalKuota)
                    ->get();

                if ($kandidat->isNotEmpty()) {
                    $ids = $kandidat->pluck('id')->toArray();
                    PesertaUndian::whereIn('id', $ids)->update(['is_winner' => 1]);

                    foreach ($kandidat as $k) {
                        $listPemenang[] = [
                            'npk' => $k->npk,
                            'nama_karyawan' => $k->nama_karyawan,
                            'seksi' => $k->seksi,
                            'plant' => $k->plant
                        ];
                    }
                }
            } else {
                foreach ($hadiah->kuotaPerPlant as $kuotaPlant) {
                    if ($kuotaPlant->jumlah_pemenang > 0) {
                        $kandidatSubPlant = PesertaUndian::where('is_winner', 0)
                            ->where('plant', $kuotaPlant->target_plant)
                            ->inRandomOrder()
                            ->limit($kuotaPlant->jumlah_pemenang)
                            ->get();

                        if ($kandidatSubPlant->isNotEmpty()) {
                            $subIds = $kandidatSubPlant->pluck('id')->toArray();
                            PesertaUndian::whereIn('id', $subIds)->update(['is_winner' => 1]);

                            foreach ($kandidatSubPlant as $k) {
                                $listPemenang[] = [
                                    'npk' => $k->npk,
                                    'nama_karyawan' => $k->nama_karyawan,
                                    'seksi' => $k->seksi,
                                    'plant' => $k->plant
                                ];
                            }
                        }
                    }
                }
                shuffle($listPemenang);
            }

            if (count($listPemenang) == 0) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Tidak ada peserta yang memenuhi kriteria atau semua sudah menang!']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'kategori_undian' => $hadiah->tipe_hadiah,
                'total_kuota' => $totalKuota,
                'data_pemenang' => $listPemenang
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memproses undian: ' . $e->getMessage()]);
        }
    }

    /**
     * Logika Mengacak HANYA 1 Nama Berdasarkan Slot Antrean Panggung Aktif
     */
  /**
     * Logika Mengacak 1 Nama (Satu-Slot)
     */
    public function kocokSatuSlot(Request $request)
    {
        $hadiahId = $request->hadiah_id;
        $hadiah = Hadiah::find($hadiahId);

        if (!$hadiah) {
            return response()->json(['success' => false, 'message' => 'Data hadiah tidak ditemukan!']);
        }

        // 1. Logika Ketersediaan Kandidat
        // Filter: is_winner = 0 (Belum menang) DAN hadiah_id = 0 (Belum pernah diundi)
        if ($hadiah->tipe_hadiah == 'all_plant') {
            $queryKandidat = PesertaUndian::where('is_winner', 0)->where('hadiah_id', 0);

            if ($queryKandidat->count() <= 0) {
                return response()->json(['success' => false, 'message' => 'Peserta sudah habis!'], 422);
            }
        } else {
            // Logic Per Plant
            $allKuota = DB::table('hadiah_kuota')->where('hadiah_id', $hadiahId)->where('jumlah_pemenang', '>', 0)->get();
            $gabunganPlants = ['BEKASI', 'SUNTER'];

            // Hitung sisa kuota
            $plantTersedia = [];
            foreach ($allKuota as $kp) {
                $pName = strtoupper(trim($kp->target_plant));
                $pemenangPlantIni = PesertaUndian::where('hadiah_id', $hadiahId)
                                    ->where(DB::raw('UPPER(TRIM(plant))'), $pName)
                                    ->count();

                if ($pemenangPlantIni < $kp->jumlah_pemenang) {
                    $plantTersedia[] = $pName;
                }
            }

            if (empty($plantTersedia)) {
                return response()->json(['success' => false, 'message' => 'Kuota per plant habis!'], 422);
            }

            $queryKandidat = PesertaUndian::where('is_winner', 0)
                                          ->where('hadiah_id', 0)
                                          ->whereIn(DB::raw('UPPER(TRIM(plant))'), $plantTersedia);
        }

        // 2. Eksekusi Update
        DB::beginTransaction();
        try {
            $terpilih = $queryKandidat->inRandomOrder()->first();

            if (!$terpilih) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Tidak ada karyawan tersedia!']);
            }

            $terpilih->update([
                'is_winner' => 1,
                'hadiah_id' => $hadiahId
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data_pemenang' => [
                    'npk' => $terpilih->npk,
                    'nama_karyawan' => $terpilih->nama_karyawan,
                    'seksi' => $terpilih->seksi,
                    'plant' => $terpilih->plant
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    /**
     * Mengubah status peserta menjadi Gugur total (is_winner = 2) via AJAX
     *//**
 * Mengubah status peserta menjadi Gugur total (is_winner = 2)
 * dan mengosongkan hadiah_id via AJAX
 */
public function gugurkanPeserta(Request $request)
{
    $peserta = PesertaUndian::where('id', $request->id)
                            ->orWhere('npk', $request->npk)
                            ->first();

    if (!$peserta) {
        return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan!'], 404);
    }

    // Update is_winner ke 2 (Gugur) dan reset hadiah_id ke 0
    $peserta->update([
        'is_winner' => 2,
        'hadiah_id' => 0
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Peserta dengan NPK ' . $peserta->npk . ' berhasil digugurkan dan hadiah di-reset!'
    ]);
}
}
