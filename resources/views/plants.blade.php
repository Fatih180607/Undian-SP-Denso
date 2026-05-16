<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setting Plant - SP Denso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f1f4f9; font-family: 'Plus Jakarta Sans', sans-serif; }
        .card { border: none; border-radius: 16px; }
    </style>
</head>
<body>
<div class="container py-5">
    <a href="{{ route('peserta.index') }}" class="btn btn-sm btn-secondary mb-4"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
    <div class="row">
        <div class="col-md-4">
            <div class="card p-4 shadow-sm">
                <h6 class="fw-bold mb-3">Tambah Master Plant</h6>
                <form action="{{ route('plant.store') }}" method="POST">
                    @csrf
                    <input type="text" name="nama_plant" class="form-control mb-3" placeholder="Contoh: Plant 1" required>
                    <button type="submit" class="btn btn-primary w-100">Simpan Plant</button>
                </form>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card p-4 shadow-sm">
                <h6 class="fw-bold mb-3">Daftar Plant Terdaftar</h6>
                <table class="table">
                    <thead><tr><th>Nama Plant</th><th class="text-center">Aksi</th></tr></thead>
                    <tbody>
                        @foreach($plants as $pl)
                        <tr>
                            <td>{{ $pl->nama_plant }}</td>
                            <td class="text-center">
                                <form action="{{ route('plant.destroy', $pl->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
