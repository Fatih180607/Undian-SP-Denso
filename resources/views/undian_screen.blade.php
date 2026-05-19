<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Live Screen Undian SP DNIA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap');

        html, body {
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            margin: 3rem
        }

        body {
            background: url("{{ asset('/images/bgdr.jpg') }}") no-repeat center center fixed;
            background-size: 100% 100%;
            transition: background 0.8s ease-in-out;
            color: #fff;
            font-family: "League Spartan", sans-serif;
        }

        .bingkai {
            display: block;
            width: 100%;
            max-width: 500px;
            height: auto;
        }

        .PrizePool {
            position: relative;
            display: flex;
            flex-direction: column;
            margin: 3rem;
        }

        .container-panggung {
            gap: 15rem;
        }

        .konten-hadiah-wrapper {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 80%;
            gap: 15px;
        }

        .text-hadiah {
            color: white;
            font-size: 2.2rem;
            font-weight: bold;
            text-align: center;
            margin: 0;
            width: 100%;
            word-wrap: break-word;
        }

        .GambarHadiah {
            width: 280px;
            object-fit: cover;
            border-radius: 12px;
            aspect-ratio: 16 / 10;
        }

        #NPK {
            font-variant-numeric: tabular-nums;
            display: inline-block;
            min-width: 700px;
            text-align: center;
        }

        .fade-in-smooth {
            opacity: 0;
            transform: translateY(15px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }

        .fade-in-smooth.show {
            opacity: 1;
            transform: translateY(0);
        }

        #boxSummaryPemenang {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            overflow-y: auto;
            padding: 3rem 5rem;
            box-sizing: border-box;
            background-repeat: no-repeat;
            background-position: center center;
            background-attachment: fixed;
            background-size: 100% 100%;
            background-color: transparent;
        }

        .summary-header-nama {
            font-size: 5rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
            line-height: 1;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
        }

        .summary-header-kuota {
            font-size: 3.5rem;
            font-weight: 800;
            text-transform: uppercase;
            margin-top: 0.5rem;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
        }

        .summary-podium-wrapper {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            margin-right: 14rem;
            width: 260px;
        }

        .summary-gambar-hadiah {
            width: 300px;
            object-fit: cover;
            z-index: 2;
            margin-bottom: -50px;
            border-radius: 10px;
        }

        .summary-tatakan-bawahan {
            width: 370px;
            height: auto;
            object-fit: contain;
            z-index: 1;
        }

        #containerHasilSummary {
            margin-top: 6rem;
            width: 100%;
        }

        .card-pemenang-putih {
            background-color: #ffffff;
            color: #000000;
            border-radius: 18px;
            padding: 1.2rem 1rem;
            text-align: center;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3);
        }

        .card-pemenang-putih .sm-npk {
            font-size: 2rem;
            font-weight: 800;
            margin: 0;
            line-height: 1.1;
        }

        .card-pemenang-putih .sm-nama {
            font-size: 1.6rem;
            font-weight: 800;
            text-transform: uppercase;
            margin: 0.3rem 0;
            line-height: 1.2;
        }

        .card-pemenang-putih .sm-seksi,
        .card-pemenang-putih .sm-plant {
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #333;
            margin: 0;
        }

        .title-grup-plant {
            background-color: #ffffff;
            color: #000000;
            font-size: 1.8rem;
            font-weight: 800;
            text-transform: uppercase;
            text-align: center;
            padding: 0.5rem 2rem;
            border-radius: 12px;
            display: inline-block;
            margin-bottom: 1.5rem;
            box-shadow: 0px 5px 15px rgba(0,0,0,0.2);
            width: 100%;
        }

        .kolom-grup-plant {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: center;
        }

        .jumlah_pcs{
            position: absolute;
            top: 7%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .card-pemenang-putih p{
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container-panggung d-flex flex-row align-items-center" style="height: 100vh; gap: 13rem;">
    <section class="PrizePool">
        <img src="{{ asset('images/conten.png') }}" class="bingkai" alt="Logo Denso">
        <div class="jumlah_pcs">
            <h1 id="totalHadiah" style="color: black">{{ $totalKuota }} Pcs</h1>
        </div>
        <div class="konten-hadiah-wrapper">
            <p id="namaHadiah" class="text-hadiah">
                {{ $hadiahAktif ? $hadiahAktif->nama_hadiah : 'BELUM PILIH HADIAH' }}
            </p>
            <img src="{{ $hadiahAktif && $hadiahAktif->foto_hadiah ? asset($hadiahAktif->foto_hadiah) : asset('images/default.png') }}" id="gambarHadiah" class="GambarHadiah" alt="Gambar Hadiah">
        </div>
    </section>

    <section>
        <ul style="text-align: center" class="list-unstyled">
            <li><p class="display-4 fw-bold m-0 fade-in-smooth show" style="font-size: 200px" id="NPK">NPK</p></li>
            <li><p class="display-4 fw-bold m-0 fade-in-smooth show" style="font-size: 100px" id="namaKaryawan">NAMA</p></li>
            <li><p class="display-4 fw-bold m-0 fade-in-smooth show" style="font-size: 80px" id="seksi">SEKSI</p></li>
            <li><p class="display-4 fw-bold m-0 fade-in-smooth show" style="font-size: 60px" id="plant">PLANT</p></li>
        </ul>
        <div class="text-center mt-4">
            <button id="btnUndiSekarang" class="btn btn-danger btn-lg fw-bold px-5 py-2 fs-3 shadow">UNDI SEKARANG</button>
        </div>
    </section>
</div>

<div id="boxSummaryPemenang" class="d-none">
    <div class="d-flex flex-row justify-content-between mt-5">
        <div class="">
            <h1 id="smJudulHadiah" class="summary-header-nama">NAMA HADIAH</h1>
            <h2 id="smKuotaHadiah" class="summary-header-kuota">0 PCS</h2>
        </div>
        <div class="d-flex">
            <div class="summary-podium-wrapper">
                <img src="" id="smFotoHadiah" class="summary-gambar-hadiah" alt="Hadiah">
                <img src="{{ asset('/images/bawahan.png') }}" class="summary-tatakan-bawahan" alt="Tatakan">
            </div>
        </div>
    </div>

    <div id="containerHasilSummary"></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let intervalNpk;
    let poolNpkDaftar = [];
    let currentHadiahId = "{{ $hadiahAktif ? $hadiahAktif->id : '' }}";

    const audioSpin = new Audio("{{ asset('audio/spin.mp3') }}");
    audioSpin.loop = true;

    const audioSelesai = new Audio("{{ asset('audio/spin_selesai.mp3') }}");

    $.ajax({
        url: "{{ route('undian.proses_kocok_proses') }}",
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

    $('#btnUndiSekarang').click(function() {
        if (!currentHadiahId) {
            alert("ID Hadiah tidak terdeteksi!");
            return;
        }

        $(this).prop('disabled', true).text('PROCESSING...');
        $('#containerHasilSummary').empty();
        $('#namaKaryawan, #seksi, #plant').removeClass('show');

        audioSpin.currentTime = 0;
        audioSpin.play().catch(e => console.log(e));

        clearInterval(intervalNpk);
        intervalNpk = setInterval(function() {
            if(poolNpkDaftar.length > 0) {
                let randomIdx = Math.floor(Math.random() * poolNpkDaftar.length);
                $('#NPK').text(poolNpkDaftar[randomIdx]);
            } else {
                $('#NPK').text(Math.floor(100000 + Math.random() * 900000));
            }
        }, 50);

        $.ajax({
            url: "{{ route('api.undian.kocok_kuota') }}",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                hadiah_id: currentHadiahId
            },
            success: function(response) {
                if(response.success) {
                    let daftarPemenang = response.data_pemenang;
                    let jenisKategori = response.kategori_undian || 'all_plant';

                    let currentIndex = 0;

                    $('#smJudulHadiah').text($('#namaHadiah').text().trim());
                    $('#smKuotaHadiah').text($('#totalHadiah').text().trim());
                    $('#smFotoHadiah').attr('src', $('#gambarHadiah').attr('src'));

                    function tampilkanUrutanPemenang() {
                        if (currentIndex < daftarPemenang.length) {
                            let pemenangSekarang = daftarPemenang[currentIndex];

                            audioSpin.pause();
                            clearInterval(intervalNpk);
                            $('#NPK').text(pemenangSekarang.npk).addClass('show');

                            setTimeout(function() {
                                $('#namaKaryawan').text(pemenangSekarang.nama_karyawan.toUpperCase()).addClass('show');

                                setTimeout(function() {
                                    $('#seksi').text(pemenangSekarang.seksi.toUpperCase()).addClass('show');

                                    setTimeout(function() {
                                        $('#plant').text(pemenangSekarang.plant.toUpperCase()).addClass('show');

                                        currentIndex++;

                                        if (currentIndex < daftarPemenang.length) {
                                            setTimeout(function() {
                                                $('#namaKaryawan, #seksi, #plant').removeClass('show');
                                                audioSpin.currentTime = 0;
                                                audioSpin.play().catch(e => console.log(e));

                                                intervalNpk = setInterval(function() {
                                                    let randomIdx = Math.floor(Math.random() * poolNpkDaftar.length);
                                                    $('#NPK').text(poolNpkDaftar[randomIdx]);
                                                }, 50);

                                                setTimeout(tampilkanUrutanPemenang, 4000);
                                            }, 3500);
                                        } else {
                                            setTimeout(function() {
                                                audioSelesai.currentTime = 0;
                                                audioSelesai.play().catch(e => console.log(e));

                                                $('#boxSummaryPemenang').css('background-image', 'url("{{ asset('/images/bg_menang.png') }}")');

                                                if (jenisKategori === 'per_plant') {
                                                    // LAYOUT PER PLANT (DENGAN HEADER NAMA PLANT DI ATAS GRUP KOLOM)
                                                    let grupPlant = {};
                                                    daftarPemenang.forEach(p => {
                                                        let namaPlant = p.plant.toUpperCase();
                                                        if (!grupPlant[namaPlant]) grupPlant[namaPlant] = [];
                                                        grupPlant[namaPlant].push(p);
                                                    });

                                                    let htmlPerPlant = `<div class="row g-4 justify-content-center align-items-start">`;
                                                    Object.keys(grupPlant).forEach(plantName => {
                                                        htmlPerPlant += `
                                                            <div class="col-md-4 kolom-grup-plant text-center">
                                                                <div class="title-grup-plant">${plantName}</div>
                                                        `;
                                                        grupPlant[plantName].forEach(pemenang => {
                                                            htmlPerPlant += `
                                                                <div class="card-pemenang-putih mb-3 w-100">
                                                                    <p class="sm-npk">${pemenang.npk}</p>
                                                                    <p class="sm-nama">${pemenang.nama_karyawan.toUpperCase()}</p>
                                                                    <p class="sm-seksi">${pemenang.seksi.toUpperCase()}</p>
                                                                    <p class="sm-plant">${pemenang.plant.toUpperCase()}</p>
                                                                </div>
                                                            `;
                                                        });
                                                        htmlPerPlant += `</div>`;
                                                    });
                                                    htmlPerPlant += `</div>`;
                                                    $('#containerHasilSummary').html(htmlPerPlant);

                                                } else {
                                                    // LAYOUT ALL PLANT (GRID STANDAR TANPA HEADER PLANT)
                                                    let htmlAllPlant = `<div class="row row-cols-1 row-cols-md-4 g-4 justify-content-center">`;
                                                    daftarPemenang.forEach(pemenang => {
                                                        htmlAllPlant += `
                                                            <div class="col">
                                                                <div class="card-pemenang-putih">
                                                                    <p class="sm-npk">${pemenang.npk}</p>
                                                                    <p class="sm-nama">${pemenang.nama_karyawan.toUpperCase()}</p>
                                                                    <p class="sm-seksi">${pemenang.seksi.toUpperCase()}</p>
                                                                    <p class="sm-plant">${pemenang.plant.toUpperCase()}</p>
                                                                </div>
                                                            </div>`;
                                                    });
                                                    htmlAllPlant += `</div>`;
                                                    $('#containerHasilSummary').html(htmlAllPlant);
                                                }

                                                $('#boxSummaryPemenang').removeClass('d-none').hide().fadeIn(600);
                                            }, 4000);
                                        }

                                    }, 1500);

                                }, 1500);

                            }, 3500);
                        }
                    }

                    setTimeout(tampilkanUrutanPemenang, 5000);

                } else {
                    audioSpin.pause();
                    clearInterval(intervalNpk);
                    alert(response.message);
                    $('#btnUndiSekarang').prop('disabled', false).text('UNDI SEKARANG');
                }
            },
            error: function() {
                audioSpin.pause();
                clearInterval(intervalNpk);
                alert("Gagal memproses pengundian dengan server.");
                $('#btnUndiSekarang').prop('disabled', false).text('UNDI SEKARANG');
            }
        });
    });
});
</script>
</body>
</html>
