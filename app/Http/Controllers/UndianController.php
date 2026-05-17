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
        // 1. Ambil data peserta terbaru
        $peserta = PesertaUndian::orderBy('created_at', 'desc')->get();

        // 2. Ambil data plant untuk dropdown di form modal
        $plants = Plant::all();

        // 3. Ambil data hadiah beserta relasinya ke tabel hadiah_kuota
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
                    'is_winner'     => false
                ]
            );
        }

        fclose($file);
        return redirect()->back()->with('success', 'Data CSV berhasil di-import dengan aman!');
    }

    /**
     * DIUPADATE: Menampilkan halaman panggung live screen dengan ID Hadiah dari parameter route
     */
    public function halamanKocok(Request $request, $hadiah_id = null)
    {
        // Jika parameter hadiah_id tidak dikirim lewat URL, coba cek dari query string (?hadiah_id=x)
        if (!$hadiah_id) {
            $hadiah_id = $request->get('hadiah_id');
        }

        $hadiah = Hadiah::with('kuotaPerPlant')->get();
        $hadiahAktif = $hadiah_id ? Hadiah::with('kuotaPerPlant')->find($hadiah_id) : null;

        // Hitung total kuota yang dialokasikan untuk hadiah ini
        $totalKuota = 0;
        if ($hadiahAktif) {
            $totalKuota = ($hadiahAktif->tipe_hadiah == 'all_plant')
                ? $hadiahAktif->total_kuota_global
                : $hadiahAktif->kuotaPerPlant->sum('jumlah_pemenang');
        }

        return view('undian_screen', compact('hadiah', 'hadiahAktif', 'totalKuota'));
    }

    /**
     * Menampilkan layar screen undian alternatif (Disamakan strukturnya)
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

    /**
     * Mengosongkan Database Peserta
     */
    public function deleteAll()
    {
        PesertaUndian::truncate();
        return redirect()->back()->with('success', 'Seluruh database peserta telah dikosongkan!');
    }

    /**
     * Logika AJAX Pengacakan Real-time (Untuk Loop Angka Bayangan Biar Ramai)
     */
    public function kocokProses(Request $request)
    {
        $action = $request->action;

        if ($action == 'init_loop') {
            // Ambil data acak secara berkala dari yang belum menang untuk variasi putaran teks gacha
            $peserta = PesertaUndian::where('is_winner', false)
                ->inRandomOrder()
                ->limit(40)
                ->get(['npk', 'nama_karyawan', 'plant', 'seksi']);

            return response()->json(['success' => true, 'data' => $peserta]);
        }
    }

    /**
     * OPTIMIZED: Logika Mengacak Sekaligus Sesuai Jumlah Kuota Hadiah (Multi-Draw)
     * Langsung mengunci semua pemenang ke DB agar sinkron, lalu dikirim ke Blade untuk dianimasikan beruntun.
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
                $kandidat = PesertaUndian::where('is_winner', false)
                    ->inRandomOrder()
                    ->limit($totalKuota)
                    ->get();

                if ($kandidat->isNotEmpty()) {
                    $ids = $kandidat->pluck('id')->toArray();
                    PesertaUndian::whereIn('id', $ids)->update(['is_winner' => true]);

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
                // Skema kuota per target plant masing-masing
                foreach ($hadiah->kuotaPerPlant as $kuotaPlant) {
                    if ($kuotaPlant->jumlah_pemenang > 0) {
                        $kandidatSubPlant = PesertaUndian::where('is_winner', false)
                            ->where('plant', $kuotaPlant->target_plant)
                            ->inRandomOrder()
                            ->limit($kuotaPlant->jumlah_pemenang)
                            ->get();

                        if ($kandidatSubPlant->isNotEmpty()) {
                            $subIds = $kandidatSubPlant->pluck('id')->toArray();
                            PesertaUndian::whereIn('id', $subIds)->update(['is_winner' => true]);

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
                // Acak urutan list biar pencampuran antar plant di panggung terasa adil saat muncul bergantian
                shuffle($listPemenang);
            }

            if (count($listPemenang) == 0) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Tidak ada peserta yang memenuhi kriteria atau semua sudah menang!']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'total_kuota' => $totalKuota,
                'data_pemenang' => $listPemenang
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memproses undian: ' . $e->getMessage()]);
        }
    }
}
