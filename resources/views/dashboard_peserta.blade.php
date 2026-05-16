<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Admin - Undian Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #334155;
        }
        .navbar-custom {
            background: #0f172a;
            border-bottom: 1px solid #1e293b;
        }
        .main-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
            padding: 24px;
            margin-bottom: 24px;
        }
        .table-container {
            max-height: 400px;
            overflow-y: auto;
            border-radius: 12px;
            border: 1px solid #edf2f7;
        }
        .table thead th {
            position: sticky;
            top: 0;
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
            z-index: 10;
        }
        .btn-modern {
            border-radius: 10px;
            padding: 8px 16px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s ease;
        }
        .btn-modern-primary {
            background: #4f46e5;
            color: #ffffff;
            border: none;
        }
        .btn-modern-primary:hover { background: #4338ca; color: #ffffff; }
        .action-icon {
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.2s;
            border: none;
            background: none;
        }
        .action-edit { color: #3b82f6; }
        .action-edit:hover { background: #eff6ff; }
        .action-delete { color: #ef4444; }
        .action-delete:hover { background: #fef2f2; }
        .badge-status {
            padding: 5px 10px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 11px;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border-color: #cbd5e1;
            padding: 10px 14px;
            font-size: 14px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        /* --- STYLING DISPLAY HADIAH PREMIUM --- */
        .gift-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .gift-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px -5px rgba(15, 23, 42, 0.08);
            border-color: #cbd5e1;
        }
        .gift-image-wrapper {
            position: relative;
            width: 100%;
            height: 180px;
            background: #f8fafc;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid #f1f5f9;
        }
        .gift-img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            padding: 12px;
            transition: transform 0.3s ease;
        }
        .gift-card:hover .gift-img {
            transform: scale(1.05);
        }
        .gift-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            z-index: 5;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 6px 12px;
            border-radius: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .gift-meta-box {
            background: #f8fafc;
            border-radius: 10px;
            padding: 10px 12px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3 shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold text-white d-flex align-items-center" href="#">
            <span class="p-2 bg-primary rounded-3 me-2 d-flex align-items-center justify-content-center" style="width:35px; height:35px;">
                <i class="fas fa-user-shield text-white" style="font-size: 16px;"></i>
            </span>
            PANEL KONTROL ADMIN
        </a>
        <div class="ms-auto d-flex align-items-center gap-2">
            <a class="btn btn-warning btn-modern fw-bold text-dark shadow-sm px-4" href="/undian-page" target="_blank">
                <i class="fas fa-play-circle me-2"></i>BUKA SCREEN UNDIAN LIVE
            </a>
        </div>
    </div>
</nav>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-check-circle me-2 fs-5"></i>
            <div class="fw-medium small">{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="main-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h5 class="fw-bold text-dark mb-1"><i class="fas fa-users text-muted me-2"></i>Database Master Peserta</h5>
                <p class="text-muted small mb-0">Kelola informasi karyawan, status pemenang, serta import berkas massal.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-light btn-modern border text-secondary" data-bs-toggle="modal" data-bs-target="#modalImportCSV">
                    <i class="fas fa-file-csv text-success me-1"></i> Import CSV
                </button>
                <button class="btn btn-modern btn-modern-primary" data-bs-toggle="modal" data-bs-target="#modalTambahPeserta">
                    <i class="fas fa-user-plus me-1"></i> Tambah Karyawan
                </button>
                <form action="{{ route('peserta.reset') }}" method="POST" onsubmit="return confirm('Kembalikan semua status menjadi Belum Menang?')" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-light btn-modern border text-warning"><i class="fas fa-history me-1"></i> Reset Status</button>
                </form>
                <form action="{{ route('peserta.deleteAll') }}" method="POST" onsubmit="return confirm('Hapus bersih seluruh data peserta dari sistem?')" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-light btn-modern border text-danger"><i class="fas fa-trash-alt me-1"></i> Kosongkan</button>
                </form>
            </div>
        </div>

        <div class="table-container">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3" width="120">NPK</th>
                        <th>Nama Karyawan</th>
                        <th>Seksi / Departemen</th>
                        <th>Plant Terdaftar</th>
                        <th class="text-center" width="120">Status Undian</th>
                        <th class="text-center" width="80">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peserta as $p)
                    <tr>
                        <td class="fw-bold ps-3 text-secondary">{{ $p->npk }}</td>
                        <td class="fw-semibold text-dark">{{ $p->nama_karyawan }}</td>
                        <td>{{ $p->seksi }}</td>
                        <td><span class="badge bg-light border text-secondary px-2 py-1 rounded">{{ $p->plant }}</span></td>
                        <td class="text-center">
                            @if($p->is_winner)
                                <span class="badge badge-status bg-success-subtle text-success border border-success"><i class="fas fa-trophy me-1"></i> MENANG</span>
                            @else
                                <span class="badge badge-status bg-light text-muted border text-uppercase">Ready</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <form action="{{ route('peserta.destroy', $p->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-icon action-delete" onclick="return confirm('Hapus karyawan ini?')"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5 small">
                            <i class="fas fa-folder-open d-block fs-3 text-slate-300 mb-2"></i> Data peserta kosong. Gunakan fitur Tambah Manual atau Import CSV.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="main-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h5 class="fw-bold text-dark mb-1"><i class="fas fa-gift text-muted me-2"></i>Katalog & Manajemen Hadiah</h5>
                <p class="text-muted small mb-0">Daftar item doorprize & grandprize lengkap beserta konfigurasi kuotanya.</p>
            </div>
            <button class="btn btn-modern btn-modern-primary" data-bs-toggle="modal" data-bs-target="#modalTambahHadiah">
                <i class="fas fa-plus-circle me-1"></i> Buat Master Hadiah
            </button>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
            @forelse($hadiah as $h)
            <div class="col">
                <div class="gift-card h-100 shadow-sm d-flex flex-column">
                    <div class="gift-image-wrapper">
                        @if($h->tipe_hadiah == 'all_plant')
                            <span class="gift-badge bg-success text-white"><i class="fas fa-globe me-1"></i>DOORPRIZE</span>
                        @else
                            <span class="gift-badge bg-indigo text-white" style="background:#4f46e5;"><i class="fas fa-star me-1"></i>GRANDPRIZE</span>
                        @endif

                        @if($h->foto_hadiah)
                            <img src="{{ asset('storage/' . $h->foto_hadiah) }}" class="gift-img" alt="Item">
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-image fs-1 opacity-25 d-block mb-1"></i>
                                <span style="font-size:11px;">Belum Ada Gambar</span>
                            </div>
                        @endif
                    </div>

                    <div class="p-3 d-flex flex-column flex-grow-1 justify-content-between">
                        <div class="mb-3">
                            <h6 class="fw-bold text-dark text-truncate mb-2" title="{{ $h->nama_hadiah }}">{{ $h->nama_hadiah }}</h6>

                            <div class="gift-meta-box">
                                @if($h->tipe_hadiah == 'all_plant')
                                    <div class="small text-success fw-bold d-flex align-items-center justify-content-between">
                                        <span><i class="fas fa-layer-group me-1"></i> Kuota Global:</span>
                                        <span class="fs-6">{{ $h->total_kuota_global }} Unit</span>
                                    </div>
                                @else
                                    <span class="small text-muted fw-bold d-block mb-1" style="font-size:11px;"><i class="fas fa-industry me-1"></i> Kuota Per Sub-Plant:</span>
                                    <div class="d-flex flex-wrap gap-1" style="max-height:65px; overflow-y:auto;">

                                        @foreach($h->kuotaPerPlant as $k)
                                            <span class="badge bg-white border text-dark text-start" style="font-size:10px; font-weight:500; padding:4px 6px;">
                                                <b>{{ $k->label_tampilan }}</b>: {{ $k->jumlah_pemenang }}
                                            </span>
                                        @endforeach

                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                            <span class="text-muted" style="font-size:11px;">ID Item: #{{ $h->id }}</span>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-light border text-primary px-2 btnUpdateHadiahModal"
                                        data-id="{{ $h->id }}"
                                        data-nama="{{ $h->nama_hadiah }}"
                                        data-bs-toggle="modal" data-bs-target="#modalEditHadiah" title="Edit Data">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('hadiah.destroy', $h->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger px-2" onclick="return confirm('Hapus item hadiah beserta kuota relasinya?')" title="Hapus Hadiah">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-5">
                <i class="fas fa-box-open fs-2 d-block mb-2 opacity-50"></i>
                <p class="small mb-0">Belum ada item hadiah yang dibuat. Silakan klik tombol "Buat Master Hadiah" di atas.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahPeserta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h6 class="modal-title fw-bold"><i class="fas fa-user-plus me-2 text-warning"></i>Tambah Karyawan Manual</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('peserta.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">NPK Karyawan</label>
                        <input type="text" name="npk" class="form-control" placeholder="Masukkan NPK" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Nama Lengkap</label>
                        <input type="text" name="nama_karyawan" class="form-control" placeholder="Masukkan Nama Lengkap" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Seksi / Departemen</label>
                        <input type="text" name="seksi" class="form-control" placeholder="Contoh: Production" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-secondary">Plant / Unit Lokasi</label>
                        <select name="plant" class="form-select" required>
                            <option value="">-- Pilih Plant --</option>
                            @foreach($plants as $pl)
                                <option value="{{ $pl->nama_plant }}">{{ $pl->nama_plant }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-modern btn-modern-primary px-4">Simpan Peserta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalImportCSV" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h6 class="modal-title fw-bold"><i class="fas fa-file-csv me-2 text-success"></i>Upload Spreadsheet CSV</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('peserta.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-secondary">Pilih Berkas (.csv)</label>
                        <input type="file" name="file" class="form-control" accept=".csv" required>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-modern px-4">Proses Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahHadiah" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h6 class="modal-title fw-bold"><i class="fas fa-box-open text-warning me-2"></i>Form Pembuatan Master Hadiah</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('hadiah.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Nama Item/Produk Hadiah</label>
                        <input type="text" name="nama_hadiah" class="form-control" placeholder="Contoh: KULKAS SHARP 2 PINTU" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Foto Gambar Hadiah</label>
                        <input type="file" name="foto_hadiah" class="form-control" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary d-block mb-2">Metode Distribusi Acak</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipe_hadiah" id="addTipeAll" value="all_plant" checked onclick="toggleAddSkema()">
                            <label class="form-check-label small fw-bold text-success" for="addTipeAll">Doorprize (All Plant)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipe_hadiah" id="addTipePer" value="per_plant" onclick="toggleAddSkema()">
                            <label class="form-check-label small fw-bold text-primary" for="addTipePer">Grandprize (Per Plant)</label>
                        </div>
                    </div>

                    <div id="addPanelAllPlant" class="p-3 bg-light rounded-3 border border-success mb-2">
                        <label class="form-label small fw-bold text-success">Total Kuota Pemenang</label>
                        <input type="number" name="total_kuota_global" class="form-control" value="1" min="1">
                    </div>

                    <div id="addPanelPerPlant" class="d-none" style="max-height: 220px; overflow-y: auto;">
                        <span class="small fw-bold text-muted d-block mb-2">Atur Kuota Sub-Plant:</span>
                        @foreach($plants as $index => $pl)
                        <div class="p-2 bg-light rounded-3 border border-primary mb-2">
                            <span class="badge bg-primary mb-1">{{ $pl->nama_plant }}</span>
                            <input type="hidden" name="kuota[{{ $index }}][plant]" value="{{ $pl->nama_plant }}">
                            <div class="row g-2">
                                <div class="col-8">
                                    <input type="text" name="kuota[{{ $index }}][label]" class="form-control form-control-sm" value="{{ $pl->nama_plant }}">
                                </div>
                                <div class="col-4">
                                    <input type="number" name="kuota[{{ $index }}][jumlah]" class="form-control form-control-sm" value="0" min="0">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-modern btn-modern-primary px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditHadiah" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h6 class="modal-title fw-bold"><i class="fas fa-edit text-primary me-2"></i>Edit Informasi Master Hadiah</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditHadiah" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Nama Item/Produk Hadiah</label>
                        <input type="text" id="editNamaHadiah" name="nama_hadiah" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Ganti Foto Baru (Kosongkan jika tidak ingin diubah)</label>
                        <input type="file" name="foto_hadiah" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-modern px-4">Update Hadiah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function toggleAddSkema() {
        const isAll = document.getElementById('addTipeAll').checked;
        if(isAll) {
            document.getElementById('addPanelAllPlant').classList.remove('d-none');
            document.getElementById('addPanelPerPlant').classList.add('d-none');
        } else {
            document.getElementById('addPanelAllPlant').classList.add('d-none');
            document.getElementById('addPanelPerPlant').classList.remove('d-none');
        }
    }

    $(document).ready(function() {
        $('.btnUpdateHadiahModal').on('click', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            $('#editNamaHadiah').val(nama);
            $('#formEditHadiah').attr('action', '/hadiah/update/' + id);
        });
    });
</script>
</body>
</html>
