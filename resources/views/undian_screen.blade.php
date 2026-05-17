<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Live Screen Undian SP DNIA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        body {
            background: url("{{ asset('images/Bg_Undian_Dipilih.jpg') }}") no-repeat center center fixed;
            background-size: 100% 100%;
            transition: background 0.8s ease-in-out;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container-panggung {
            height: 100vh;
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            box-sizing: border-box;
        }

        /* PANEL KONTROL ATAS - SIMPEL & BERSIH */
        .control-panel {
            background: rgba(0, 0, 0, 0.5);
            padding: 15px 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            gap: 15px;
            align-items: center;
            backdrop-filter: blur(5px);
        }

        .btn-custom-pilih {
            background-color: #0d6efd;
            color: white;
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 5px;
            transition: all 0.2s;
        }

        .btn-custom-pilih:hover {
            background-color: #0b5ed7;
        }

        .btn-custom-undi {
            background-color: #198754;
            color: white;
            border: none;
            padding: 10px 25px;
            font-weight: 600;
            border-radius: 5px;
            transition: all 0.2s;
        }

        .btn-custom-undi:hover {
            background-color: #157347;
        }

        /* BOX INFO HADIAH SIMPEL */
        .box-info-hadiah {
            background: rgba(0, 26, 51, 0.8);
            border: 1px solid #00f0ff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            display: none;
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        /* BOX DISPLAY ACAK NYAMAN DI MATA */
        .box-gacha-display {
            background: rgba(0, 0, 0, 0.85);
            border: 2px solid #ff007c;
            border-radius: 15px;
            padding: 35px;
            text-align: center;
            width: 100%;
            max-width: 800px;
            display: none;
        }

        .running-text {
            font-size: 5rem;
            font-weight: bold;
            letter-spacing: 5px;
            color: #ffffff;
            text-shadow: 0 0 10px rgba(255,255,255,0.5);
            min-height: 120px;
        }

        .detail-pemenang {
            font-size: 2.3rem;
            color: #00ffcc;
            margin-top: 10px;
            font-weight: 600;
        }

        .list-pemenang-borongan {
            max-height: 200px;
            overflow-y: auto;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 12px;
        }
    </style>
</head>
<body>

<div class="container-panggung">

    <div class="control-panel">
        <button id="btnPilihHadiah" class="btn-custom-pilih">Pilih Hadiah</button>

        <button id="btnUndiSekarang" class="btn-custom-undi">Undi Sekarang</button>

        <div id="wrapperDropdown" style="display: none;">
            <select id="selectHadiah" class="form-select" style="min-width: 300px;">
                <option value="">-- Pilih Item Hadiah --</option>
                @foreach($hadiah as $h)
                    <option value="{{ $h->id }}">{{ $h->nama_hadiah }} ({{ strtoupper($h->tipe_hadiah) }})</option>
                @endforeach
            </select>
        </div>
    </div>

    <div id="boxHadiah" class="box-info-hadiah">
        <h3 id="txtNamaHadiah" class="text-warning fw-bold mb-2">Nama Hadiah</h3>
        <img id="imgFotoHadiah" src="" alt="Foto Hadiah" class="img-fluid rounded mb-2 shadow-sm" style="max-height: 150px; object-fit: contain;">
        <p class="text-white mb-0">Total Kuota Undian: <span id="txtJumlahKuota" class="badge bg-danger fs-6">0</span></p>
    </div>

    <div id="boxGacha" class="box-gacha-display">
        <div class="text-muted small text-uppercase fw-bold mb-2" style="letter-spacing: 2px;">Sistem Mengacak Peserta...</div>
        <div id="displayNpk" class="running-text">000000</div>
        <div id="displayDetail" class="detail-pemenang"></div>

        <div id="boxListPemenang" class="mt-4 text-start d-none">
            <h6 class="text-warning fw-bold border-bottom pb-2">🎉 DAFTAR PEMENANG SAH:</h6>
            <div id="wrapperListPemenang" class="list-pemenang-borongan row g-2"></div>
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let intervalNpk;
    let daftarPemenangFix = [];
    let currentHadiahId = null;
    let poolNpkDaftar = [];

    // Ambil Data NPK asli dari database untuk bahan acakan text
    $.ajax({
        url: "/api/kocok-proses",
        type: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            action: 'init_loop'
        },
        success: function(response) {
            if(response.success && response.data.length > 0) {
                poolNpkDaftar = response.data.map(item => item.npk);
            }
        }
    });

    // 1. Klik Pilih Hadiah -> Toggle Dropdown
    $('#btnPilihHadiah').click(function() {
        $('#wrapperDropdown').toggle();
    });

    // 2. Saat Hadiah Dipilih
    $('#selectHadiah').change(function() {
        let id = $(this).val();
        currentHadiahId = id;

        if(!id) {
            $('body').removeClass('hadiah-terpilih');
            $('#boxHadiah').hide();
            $('#boxGacha').hide();
            return;
        }

        $('body').addClass('hadiah-terpilih');

        $.ajax({
            url: '/api/hadiah/detail/' + id,
            type: 'GET',
            success: function(response) {
                if(response.success) {
                    $('#txtNamaHadiah').text(response.hadiah.nama_hadiah);
                    $('#txtJumlahKuota').text(response.total_kuota);

                    let fotoUrl = response.hadiah.foto ? '/storage/' + response.hadiah.foto : 'https://placehold.co/400x300?text=No+Image';
                    $('#imgFotoHadiah').attr('src', fotoUrl);

                    $('#boxGacha').hide();
                    $('#boxListPemenang').addClass('d-none');
                    $('#boxHadiah').fadeIn(400);
                }
            }
        });
    });

    // 3. Eksekusi Tombol Undi Sekarang
    $('#btnUndiSekarang').click(function() {
        if(!currentHadiahId) {
            alert("Silakan pilih hadiah terlebih dahulu!");
            return;
        }

        $('#boxGacha').fadeIn(400);
        $('#displayDetail').text("");
        $('#wrapperListPemenang').empty();
        $('#boxListPemenang').addClass('d-none');

        // Mulai Putaran Acak Teks NPK
        clearInterval(intervalNpk);
        intervalNpk = setInterval(function() {
            if(poolNpkDaftar.length > 0) {
                let randomIdx = Math.floor(Math.random() * poolNpkDaftar.length);
                $('#displayNpk').text(poolNpkDaftar[randomIdx]);
            } else {
                $('#displayNpk').text(Math.floor(100000 + Math.random() * 900000));
            }
        }, 45);

        // Ambil Data Pemenang Resmi dari Database
        $.ajax({
            url: "{{ route('api.undian.kocok_kuota') }}",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                hadiah_id: currentHadiahId
            },
            success: function(response) {
                setTimeout(function() {
                    clearInterval(intervalNpk);

                    if(response.success) {
                        daftarPemenangFix = response.data_pemenang;

                        // Tampilkan Gong Utama (Pemenang Pertama)
                        let gongUtama = daftarPemenangFix[0];
                        $('#displayNpk').text(gongUtama.npk);

                        // Menyusul Nama, Seksi, Plant
                        $('#displayDetail').html(
                            `<span class="fw-bold text-white">${gongUtama.nama_karyawan}</span><br>` +
                            `<small class="fs-4 text-info">${gongUtama.seksi} - ${gongUtama.plant}</small>`
                        );

                        // Munculkan daftar borongan ke bawah jika kuota banyak
                        $('#boxListPemenang').removeClass('d-none');
                        daftarPemenangFix.forEach(function(item, index) {
                            let cardHtml = `
                                <div class="col-md-6">
                                    <div class="p-2 border border-secondary rounded bg-dark mb-1" style="font-size:0.9rem;">
                                        <b class="text-warning">#${index + 1} - ${item.npk}</b> | ${item.nama_karyawan}
                                        <br><small class="text-muted text-uppercase">${item.seksi} (${item.plant})</small>
                                    </div>
                                </div>`;
                            $('#wrapperListPemenang').append(cardHtml);
                        });

                    } else {
                        alert(response.message);
                        $('#displayNpk').text("KOSONG");
                    }
                }, 2500); // Jeda animasi mengocok 2.5 detik
            },
            error: function() {
                clearInterval(intervalNpk);
                alert("Gagal memproses undian.");
                $('#displayNpk').text("FAILED");
            }
        });
    });
});
</script>
</body>
</html>
