<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Admin - Undian Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/Logo_SPDNIA.png') }}">
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
            <span class="me-3 d-flex align-items-center justify-content-center" style="width:55px; height:55px;">
                <img src="{{ asset('images/logo_Spdnia.png') }}" alt="Logo Spdnia" style="width: 100%; height: 100%; object-fit: contain;">
            </span>
            <span class="me-3 d-flex align-items-center justify-content-center" style="width:55px; height:55px;">
                <img src="{{ asset('images/logo_23TH.png') }}" alt="Logo 23TH" style="width: 100%; height: 100%; object-fit: contain;">
            </span>
            DASHBOARD PESERTA & HADIAH
        </a>
        <div class="ms-auto d-flex align-items-center gap-2">
            <li class="nav-item d-inline-block mx-2">
                <a href="{{ route('plants.index') }}"
                   class="btn {{ Request::is('plants*') ? 'btn-primary' : 'btn-secondary' }}">
                    <i class="fas fa-industry"></i>
                    <span>Setting Plant</span>
                </a>
            </li>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPilihHadiahLive">
    <i class="fas fa-play-circle me-1"></i> Buka Undian Secara Live
</button>
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
            <h5 class="fw-bold text-dark mb-1"><i class="fas fa-users text-muted me-2"></i>Data Peserta</h5>
            <p class="text-muted small mb-0">Informasi Peserta, status pemenang.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-light btn-modern border text-secondary" data-bs-toggle="modal" data-bs-target="#modalImportCSV">
                <i class="fas fa-file-csv text-success me-1"></i> Import CSV
            </button>
            <button class="btn btn-modern btn-modern-primary" data-bs-toggle="modal" data-bs-target="#modalTambahPeserta">
                <i class="fas fa-user-plus me-1"></i> Tambah Peserta
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

    <div class="p-3 bg-light rounded-3 mb-4 border">
        <form action="{{ request()->url() }}" method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label small fw-bold text-secondary mb-1">Cari Peserta</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Ketik NPK atau Nama..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label small fw-bold text-secondary mb-1">Filter Plant</label>
<select name="plant" class="form-control">
    <option value="">SEMUA PLANT</option>
    @if(isset($plants) && $plants->count() > 0)
        @foreach($plants as $pl)
            <option value="{{ $pl->nama_plant }}" {{ request('plant') == $pl->nama_plant ? 'selected' : '' }}>
                {{ $pl->nama_plant }}
            </option>
        @endforeach
    @else
        <option value="BEKASI" {{ request('plant') == 'BEKASI' ? 'selected' : '' }}>BEKASI</option>
        <option value="SUNTER" {{ request('plant') == 'SUNTER' ? 'selected' : '' }}>SUNTER</option>
        <option value="FAJAR" {{ request('plant') == 'FAJAR' ? 'selected' : '' }}>FAJAR</option>
        <option value="TACI" {{ request('plant') == 'TACI' ? 'selected' : '' }}>TACI</option>
    @endif
</select>
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label small fw-bold text-secondary mb-1">Status Undian</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>BELUM MENANG</option>
                    <option value="winner" {{ request('status') == 'winner' ? 'selected' : '' }}>MENANG</option>
                    <option value="gugur" {{ request('status') == 'gugur' ? 'selected' : '' }}>HANGUS</option>
                </select>
            </div>

            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-modern btn-modern-primary w-100 py-2">
                    Filter
                </button>
                @if(request()->has('search') || request()->has('plant') || request()->has('status'))
                    <a href="{{ request()->url() }}" class="btn btn-light border py-2 text-secondary" title="Reset Filter">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-container">

        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-3" width="120">NPK</th>
                    <th>Nama</th>
                    <th>Seksi / Departemen</th>
                    <th>Plant Terdaftar</th>
                    <th class="text-center" width="120">Status Undian</th>
                    <th class="text-center" width="100">Aksi</th>
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
                        @if($p->is_winner == 2)
                            <span class="badge badge-status bg-danger-subtle text-danger border border-danger">
                                <i class="fas fa-user-times me-1"></i> GUGUR
                            </span>
                        @elseif($p->is_winner == 1)
                            <span class="badge badge-status bg-success-subtle text-success border border-success">
                                <i class="fas fa-trophy me-1"></i> MENANG
                            </span>
                        @else
                            <span class="badge badge-status bg-light text-muted border text-uppercase">
                            BELUM MENANG
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <button type="button" class="action-icon action-edit btn-edit-peserta"
                                    data-id="{{ $p->id }}"
                                    data-npk="{{ $p->npk }}"
                                    data-nama="{{ $p->nama_karyawan }}"
                                    data-seksi="{{ $p->seksi }}"
                                    data-plant="{{ $p->plant }}"
                                    title="Edit Peserta">
                                <i class="fas fa-edit"></i>
                            </button>

                            <form action="{{ route('peserta.destroy', $p->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-icon action-delete" onclick="return confirm('Hapus karyawan ini?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5 small">
                        <i class="fas fa-folder-open d-block fs-3 text-slate-300 mb-2"></i> Data peserta kosong atau tidak ditemukan dengan filter tersebut. Gunakan fitur Tambah Manual atau Import CSV.
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
                <i class="fas fa-plus-circle me-1"></i> Tambah Hadiah
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
                            <span class="gift-badge bg-primary text-white" style="background:#4f46e5 !important;"><i class="fas fa-star me-1"></i>GRANDPRIZE</span>
                        @endif

                        @if($h->foto_hadiah)
                            <img src="{{ asset($h->foto_hadiah) }}" class="gift-img" alt="Item Hadiah">
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
                                        <span><i class="fas fa-layer-group me-1"></i> Jumlah: {{ $h->total_kuota_global }}</span>
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
                                        data-tipe="{{ $h->tipe_hadiah }}"
                                        data-kuotaglobal="{{ $h->total_kuota_global }}"
                                        data-kuotaperplant="{{ json_encode($h->kuotaPerPlant) }}"
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
                <h6 class="modal-title fw-bold"><i class="fas fa-user-plus me-2 text-warning"></i>Tambah Peserta</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('peserta.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">NPK</label>
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

<div class="modal fade" id="modalEditPeserta" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h6 class="modal-title fw-bold"><i class="fas fa-user-edit text-primary me-2"></i>Edit Informasi Karyawan</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditPeserta" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">NPK Karyawan</label>
                        <input type="text" id="editNpkPeserta" name="npk" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Nama Lengkap</label>
                        <input type="text" id="editNamaPeserta" name="nama_karyawan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Seksi / Departemen</label>
                        <input type="text" id="editSeksiPeserta" name="seksi" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-secondary">Plant / Unit Lokasi</label>
                        <select id="editPlantPeserta" name="plant" class="form-select" required>
                            <option value="">-- Pilih Plant --</option>
                            @foreach($plants as $pl)
                                <option value="{{ $pl->nama_plant }}">{{ $pl->nama_plant }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-modern px-4">Update Peserta</button>
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
                <h6 class="modal-title fw-bold"><i class="fas fa-box-open text-warning me-2"></i>Tambah Hadiah</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('hadiah.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Nama Item Hadiah</label>
                        <input type="text" name="nama_hadiah" class="form-control" placeholder="Contoh: Sepeda Motor Honda" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Tipe Undian / Distribusi</label>
                        <select id="selectTipeHadiah" name="tipe_hadiah" class="form-select" required>
                            <option value="all_plant">DOORPRIZE (Kuota Global / Acak Semua Plant)</option>
                            <option value="per_plant">GRANDPRIZE (Spesifik Batasan per Sub-Plant)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Foto/Gambar Produk</label>
                        <input type="file" name="foto_hadiah" class="form-control" accept="image/*">
                    </div>

                    <div id="panelAllPlant" class="mb-2">
                        <label class="form-label small fw-bold text-success"><i class="fas fa-layer-group me-1"></i>Total Kuota Pemenang</label>
                        <input type="number" id="inputTotalKuotaGlobal" name="total_kuota_global" class="form-control" placeholder="Masukkan total unit hadiah" value="1">
                    </div>

                    <div id="panelPerPlant" class="d-none" style="max-height: 250px; overflow-y: auto;">
                        <label class="form-label small fw-bold text-primary mb-2"><i class="fas fa-industry me-1"></i>Set Batasan Pemenang per Plant</label>
                        @foreach($plants as $pl)
                        <div class="p-2 bg-light rounded-3 border border-primary mb-2">
                            <span class="badge bg-primary mb-1">{{ $pl->nama_plant }}</span>
                            <input type="hidden" name="kuota[{{ $pl->nama_plant }}][plant]" value="{{ $pl->nama_plant }}">
                            <div class="row g-2">
                                <div class="col-8">
                                    <input type="text" name="kuota[{{ $pl->nama_plant }}][label]" class="form-control form-control-sm" value="{{ $pl->nama_plant }}" placeholder="Label Tampilan">
                                </div>
                                <div class="col-4">
                                    <input type="number" name="kuota[{{ $pl->nama_plant }}][jumlah]" class="form-control form-control-sm input-kuota-plant" placeholder="Qty" min="0" value="0">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Master Hadiah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditHadiah" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h6 class="modal-title fw-bold"><i class="fas fa-box-open text-primary me-2"></i>Update Konfigurasi Hadiah</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditHadiah" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Nama Item Hadiah</label>
                        <input type="text" id="editNamaHadiah" name="nama_hadiah" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Tipe Undian</label>

                        <select id="editTipeHadiah" class="form-select" disabled style="background-color: #e9ecef;">
                            <option value="all_plant">DOORPRIZE (Kuota Global)</option>
                            <option value="per_plant">GRANDPRIZE (Kuota Per Sub-Plant)</option>
                        </select>

                        <input type="hidden" id="editTipeHadiahHidden" name="tipe_hadiah">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Ganti Foto/Gambar (Opsional)</label>
                        <input type="file" name="foto_hadiah" class="form-control" accept="image/*">
                    </div>

                    <div id="editPanelAllPlant" class="mb-2 d-none">
                        <label class="form-label small fw-bold text-success"><i class="fas fa-layer-group me-1"></i>Total Kuota Pemenang</label>
                        <input type="number" id="editTotalKuotaGlobal" name="total_kuota_global" class="form-control" min="0">
                    </div>

                    <div id="editPanelPerPlant" class="d-none" style="max-height: 220px; overflow-y: auto;">
                        <span class="small fw-bold text-muted d-block mb-2"><i class="fas fa-industry me-1"></i>Atur Ulang Kuota Sub-Plant:</span>
                        @foreach($plants as $pl)
                        @php
                            $cleanId = str_replace(' ', '_', $pl->nama_plant);
                        @endphp
                        <div class="p-2 bg-light rounded-3 border border-primary mb-2">
                            <span class="badge bg-primary mb-1">{{ $pl->nama_plant }}</span>

                            <input type="hidden" name="kuota[{{ $pl->nama_plant }}][plant]" value="{{ $pl->nama_plant }}">

                            <div class="row g-2">
                                <div class="col-8">
                                    <input type="text" id="editLabelPlant_{{ $cleanId }}" name="kuota[{{ $pl->nama_plant }}][label]" class="form-control form-control-sm" value="{{ $pl->nama_plant }}">
                                </div>
                                <div class="col-4">
                                    <input type="number" id="editJumlahPlant_{{ $cleanId }}" name="kuota[{{ $pl->nama_plant }}][jumlah]" class="form-control form-control-sm" min="0">
                                </div>
                            </div>
                        </div>
                        @endforeach
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
$(document).ready(function() {

    // Toggle visibilitas input kuota saat memilih tipe hadiah (Modal Tambah)
    $('#selectTipeHadiah').on('change', function() {
        if ($(this).val() === 'all_plant') {
            $('#panelAllPlant').removeClass('d-none');
            $('#panelPerPlant').addClass('d-none');
            $('#inputTotalKuotaGlobal').val(1);
        } else {
            $('#panelAllPlant').addClass('d-none');
            $('#panelPerPlant').removeClass('d-none');
            $('#inputTotalKuotaGlobal').val(0); // Aman diset 0 karena min="1" sudah dihapus dari HTML
        }
    });

    // Handler klik Edit Peserta Karyawan
    $('.btn-edit-peserta').on('click', function() {
        var id = $(this).data('id');
        $('#editNpkPeserta').val($(this).data('npk'));
        $('#editNamaPeserta').val($(this).data('nama'));
        $('#editSeksiPeserta').val($(this).data('seksi'));
        $('#editPlantPeserta').val($(this).data('plant'));
        $('#formEditPeserta').attr('action', '/peserta/update/' + id);
        $('#modalEditPeserta').modal('show');
    });

    // Handler klik Edit Hadiah (Sinkronisasi payload JSON ke dalam field sub-plant)
    $('.btnUpdateHadiahModal').on('click', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var tipe = $(this).data('tipe');
        var kuotaGlobal = $(this).data('kuotaglobal');
        var kuotaPerPlant = $(this).data('kuotaperplant');

        $('#editPanelAllPlant').addClass('d-none');
        $('#editPanelPerPlant').addClass('d-none');

        $('#editNamaHadiah').val(nama);
        $('#editTipeHadiah').val(tipe);
        $('#editTipeHadiahHidden').val(tipe);
        $('#formEditHadiah').attr('action', '/hadiah/update/' + id);

        if (tipe === 'all_plant') {
            $('#editPanelAllPlant').removeClass('d-none');
            $('#editTotalKuotaGlobal').val(kuotaGlobal);
        } else {
            $('#editPanelPerPlant').removeClass('d-none');
            $('#editTotalKuotaGlobal').val(0);

            $('input[id^="editJumlahPlant_"]').val(0);

            if (typeof kuotaPerPlant === 'string') {
                try { kuotaPerPlant = JSON.parse(kuotaPerPlant); } catch (e) { console.error(e); }
            }

            if (kuotaPerPlant && kuotaPerPlant.length > 0) {
                kuotaPerPlant.forEach(function(item) {
                    var namaPlantRaw = item.target_plant;
                    if (namaPlantRaw) {
                        var plantIdClean = namaPlantRaw.replace(/ /g, '_');
                        $('#editLabelPlant_' + plantIdClean).val(item.label_tampilan || namaPlantRaw);
                        $('#editJumlahPlant_' + plantIdClean).val(item.jumlah_pemenang || 0);
                    }
                });
            }
        }
    });
});
</script>
<div class="modal fade" id="modalPilihHadiahLive" tabindex="-1" aria-labelledby="modalPilihHadiahLiveLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md"> <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">

            <div class="modal-header border-bottom-0 pt-4 px-4 pb-2">
                <h5 class="modal-title fw-bold text-dark" id="modalPilihHadiahLiveLabel" style="font-size: 1.25rem; letter-spacing: -0.3px;">
                    Pilih Kategori Hadiah
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.8rem;"></button>
            </div>

            <div class="modal-body px-4 pb-4 pt-1">
                <p class="text-muted small mb-4">Pilih salah satu hadiah untuk diundi.</p>

                <div class="d-flex flex-column gap-2">
                    @isset($hadiah)
                        @foreach($hadiah as $item)
                            @php
                                // Hitung kuota presisi untuk tampilan list
                                $kuotaItem = ($item->tipe_hadiah == 'all_plant')
                                    ? $item->total_kuota_global
                                    : $item->kuotaPerPlant->sum('jumlah_pemenang');
                            @endphp

                            <div class="card border border-light-subtle shadow-sm px-3 py-2" style="border-radius: 8px; transition: all 0.2s ease;">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">

                                    <div style="max-width: 65%;">
                                        <div class="fw-semibold text-dark text-truncate" title="{{ $item->nama_hadiah }}" style="font-size: 0.95rem;">
                                            {{ $item->nama_hadiah }}
                                        </div>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="text-muted" style="font-size: 0.8rem;">
                                                {{ $item->tipe_hadiah == 'all_plant' ? 'All Plant' : 'Per Plant' }}
                                            </span>
                                            <span class="text-secondary" style="font-size: 0.8rem;">•</span>
                                            <span class="text-dark fw-medium" style="font-size: 0.8rem;">
                                                Jumlah Hadiah: {{ $kuotaItem }} Pcs
                                            </span>
                                        </div>
                                    </div>

                                    <div>
                                        <a href="{{ route('undian.kocok', $item->id) }}" target="_blank" class="btn btn-dark btn-sm px-3 fw-medium" style="font-size: 0.8rem; border-radius: 6px;">
                                            Undi Sekarang <i class="fas fa-arrow-right ms-1" style="font-size: 0.75rem;"></i>
                                        </a>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted small py-4">Belum ada kategori hadiah tersedia.</div>
                    @endisset
                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>
