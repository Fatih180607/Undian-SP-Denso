<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Live Screen Undian SP DNIA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/Logo_SPDNIA.png') }}">
    <link href="https://cdnfonts.com" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=League+Spacer:wght=100..900&display=swap');

        html, body {
            padding: 0;
            margin: 0;
            width: 100%;
            min-height: 100%;
            overflow-x: hidden;
            height: 100vh;
        }

        body {
            background: url("{{ asset('/images/bgdr.jpg') }}") no-repeat center center fixed;
            background-size: cover;
            transition: background 0.8s ease-in-out;
            color: #fff;
            position: relative;
        }

        /* --- Header Area untuk Logo di Kanan Atas --- */
        .header-logos {
            position: absolute;
            top: 25px;
            right: 40px;
            display: flex;
            align-items: center;
            gap: 25px;
            z-index: 10;
        }

        .header-logos img {
            height: clamp(70px, 9vw, 150px);
            width: auto;
            object-fit: contain;
            filter: drop-shadow(2px 4px 12px rgba(0, 0, 0, 0.6));
        }

        /* Container panggung utama */
        .container-panggung {
            min-height: 100vh;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 6vw;
            padding: 7rem 4rem 3rem 4rem;
        }

        /* --- Sisi Kiri: Tempat Gambar Doorprize & Card Hadiah --- */
        .section-hadiah-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
            width: 100%;
            max-width: 550px;
            margin-left: 2rem;
        }

        .img-doorprize-title {
            width: 120%;
            max-width: 620px;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0px 8px 16px rgba(0, 0, 0, 0.6));
            z-index: 2;
        }

        .PrizePool {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .bingkai {
            display: block;
            width: 100%;
            height: auto;
            filter: drop-shadow(0px 10px 25px rgba(0,0,0,0.5));
        }

        .konten-hadiah-wrapper {
            position: absolute;
            top: 55%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 82%;
            gap: 15px;
        }

        .text-hadiah {
            color: white;
            font-size: clamp(1.8rem, 2.5vw, 2.6rem);
            font-weight: bold;
            text-align: center;
            margin: 0;
            width: 100%;
            word-wrap: break-word;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
        }

        .GambarHadiah {
            width: 100%;
            max-width: 320px;
            object-fit: cover;
            border-radius: 14px;
            aspect-ratio: 16 / 10;
        }

        .jumlah_pcs{
            position: absolute;
            top: 7%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* Bagian Teks Acak Gacha */
        .section-gacha {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        #NPK {
            font-size: clamp(4.5rem, 9vw, 11rem);
            font-variant-numeric: tabular-nums;
            font-weight: 900;
            margin: 0;
            line-height: 1;
        }
        #namaKaryawan { font-size: clamp(2.8rem, 5.5vw, 6rem); font-weight: 800; margin: 0; line-height: 1.1; }
        #seksi { font-size: clamp(2.2rem, 4.5vw, 5rem); font-weight: 700; margin: 0; }
        #plant { font-size: clamp(1.8rem, 3.5vw, 4rem); font-weight: 700; margin: 0; }

        .fade-in-smooth {
            opacity: 0;
            transform: translateY(15px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }

        .fade-in-smooth.show {
            opacity: 1;
            transform: translateY(0);
        }

        .slot-indicator {
            font-size: clamp(1.2rem, 2vw, 1.8rem);
            background: rgba(0,0,0,0.5);
            padding: 0.5rem 1.5rem;
            border-radius: 30px;
            border: 1px solid rgba(255,255,255,0.2);
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        /* Area Summary */
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
            background-size: cover;
        }

        /* Panel aksi download terapung agar tidak ikut kefoto saat dicapture */
        .summary-action-panel {
            position: fixed;
            bottom: 30px;
            right: 40px;
            z-index: 10000;
        }

        .summary-header-nama {
            font-size: clamp(2.5rem, 5vw, 5rem);
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
        }

        .summary-header-kuota {
            font-size: clamp(1.8rem, 3.5vw, 3.5rem);
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
            width: 100%;
            max-width: 300px;
            margin-right: 10rem;
        }

        .summary-gambar-hadiah {
            width: 80%;
            object-fit: cover;
            z-index: 2;
            margin-bottom: -30px;
            border-radius: 10px;
        }

        .summary-tatakan-bawahan {
            width: 100%;
            height: auto;
            object-fit: contain;
            z-index: 1;
        }

        #containerHasilSummary {
            margin-top: 4rem;
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

        .card-pemenang-putih p { font-weight: bold; margin-bottom: 0.3rem; }
        .card-pemenang-putih .sm-npk { font-size: 1.8rem; font-weight: 800; line-height: 1.1; }
        .card-pemenang-putih .sm-nama { font-size: 1.4rem; font-weight: 800; text-transform: uppercase; }
        .card-pemenang-putih .sm-seksi, .card-pemenang-putih .sm-plant { font-size: 1rem; color: #333; text-transform: uppercase; }

        .title-grup-plant {
            background-color: #ffffff;
            color: #000000;
            font-size: 1.6rem;
            font-weight: 800;
            text-transform: uppercase;
            text-align: center;
            padding: 0.6rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            box-shadow: 0px 5px 15px rgba(0,0,0,0.2);
        }

        /* RESPONSIVITAS DEVICE KECIL / HP */
        @media (max-width: 992px) {
            .header-logos {
                position: relative;
                top: 0;
                right: 0;
                justify-content: center;
                padding-top: 1.5rem;
                gap: 15px;
            }

            .container-panggung {
                flex-direction: column !important;
                gap: 2rem !important;
                padding: 1rem !important;
                padding-bottom: 5rem !important;
            }

            .section-hadiah-wrapper {
                max-width: 340px;
                margin-left: 0 !important;
            }

            .img-doorprize-title {
                width: 100%;
            }

            .text-center.mt-4.d-flex {
                flex-direction: column !important;
                align-items: center !important;
                gap: 0.5rem !important;
            }

            .text-center.mt-4.d-flex button,
            .text-center.mt-4.d-flex .btn {
                width: 100% !important;
                max-width: 300px;
            }

            #boxSummaryPemenang {
                padding: 1.5rem !important;
            }

            #boxSummaryPemenang .d-flex.flex-row.justify-content-between {
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
                gap: 2rem;
            }

            #containerHasilSummary .d-flex.flex-row.gap-3 {
                flex-direction: column !important;
            }

            /* Area Gacha yang dioptimalkan agar stabil */
.section-gacha {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    width: 100%;
}

/* Kontainer ini menjaga tinggi agar tidak goyang */
.text-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    max-width: 900px;
    min-height: 500px; /* Menjaga ruang tetap stabil */
    justify-content: center;
    gap: 10px;
}

/* Memastikan teks tidak bergeser dan punya batas lebar */
#NPK, #namaKaryawan, #seksi, #plant {
    width: 100%;
    white-space: nowrap; /* Teks tidak turun ke baris baru */
    overflow: hidden;    /* Sembunyikan jika terlalu panjang */
    text-overflow: ellipsis; /* Tambahkan ... jika kepanjangan */
    display: block;
    margin: 0;
    line-height: 1.1;
}

#NPK { font-size: clamp(4.5rem, 9vw, 11rem); font-weight: 900; font-variant-numeric: tabular-nums; }
#namaKaryawan { font-size: clamp(2.8rem, 5.5vw, 6rem); font-weight: 800; }
#seksi { font-size: clamp(2.2rem, 4.5vw, 5rem); font-weight: 700; }
#plant { font-size: clamp(1.8rem, 3.5vw, 4rem); font-weight: 700; }
        }
    </style>
</head>
<body>

<div class="header-logos">
    <img src="{{ asset('images/Logo_SPDNIA.png') }}" alt="Logo SP DNIA">
    <img src="{{ asset('images/Logo_23th.png') }}" alt="Logo 23th">
</div>

<div class="container-panggung">
    <div class="section-hadiah-wrapper">
        <img src="{{ asset('images/doorprize_teks.png') }}" class="img-doorprize-title" alt="DOORPRIZE">

        <section class="PrizePool">
            <img src="{{ asset('images/conten.png') }}" class="bingkai" alt="Logo Denso">
            <div class="jumlah_pcs">
                <h1 id="totalHadiah" style="color: black; font-size: 2.2rem; font-weight: bold;">{{ $totalKuota }} Pcs</h1>
            </div>
            <div class="konten-hadiah-wrapper">
                <p id="namaHadiah" class="text-hadiah">
                    {{ $hadiahAktif ? $hadiahAktif->nama_hadiah : 'BELUM PILIH HADIAH' }}
                </p>
                <img src="{{ $hadiahAktif && $hadiahAktif->foto_hadiah ? asset($hadiahAktif->foto_hadiah) : asset('images/default.png') }}" id="gambarHadiah" class="GambarHadiah" alt="Gambar Hadiah" crossorigin="anonymous">
            </div>
        </section>
    </div>

<section class="section-gacha">
    <div>
        <div id="infoSlot" class="slot-indicator text-warning fw-bold d-none">Slot Pemenang Ke-1</div>
    </div>

    <div class="text-container">
        <p class="fade-in-smooth show" id="NPK">NPK</p>
        <p class="fade-in-smooth show" id="namaKaryawan">NAMA</p>
        <p class="fade-in-smooth show" id="seksi">SEKSI</p>
        <p class="fade-in-smooth show" id="plant">PLANT</p>
    </div>

    <div class="text-center mt-4 d-flex justify-content-center gap-3 flex-wrap">
        <button id="btnUndiSekarang" class="btn btn-danger btn-lg fw-bold px-4 py-2 fs-4 shadow">UNDI SEKARANG</button>
        <button id="btnUndiUlang" class="btn btn-warning btn-lg fw-bold px-4 py-2 fs-4 shadow d-none text-white">UNDI ULANG</button>
        <button id="btnLanjutUndi" class="btn btn-success btn-lg fw-bold px-4 py-2 fs-4 shadow d-none">LANJUT UNDI</button>
        <button id="btnLihatSummary" class="btn btn-primary btn-lg fw-bold px-4 py-2 fs-4 shadow d-none">SELESAI</button>
    </div>
</section>
</div>

<div id="boxSummaryPemenang" class="d-none">
    <div class="d-flex flex-row justify-content-between pt-4">
        <div>
            <h1 id="smJudulHadiah" class="summary-header-nama">NAMA HADIAH</h1>
            <h2 id="smKuotaHadiah" class="summary-header-kuota">0 PCS</h2>
        </div>
        <div class="d-flex justify-content-center">
            <div class="summary-podium-wrapper">
                <img src="" id="smFotoHadiah" class="summary-gambar-hadiah" alt="Hadiah" crossorigin="anonymous">
                <img src="{{ asset('/images/bawahan.png') }}" class="summary-tatakan-bawahan" alt="Tatakan">
            </div>
        </div>
    </div>

    <div id="containerHasilSummary"></div>

    <div class="summary-action-panel">
        <button id="btnDownloadSummary" class="btn btn-light btn-lg fw-bold px-5 py-3 fs-4 shadow-lg ">
            💾 DOWNLOAD GAMBAR
        </button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let intervalNpk;
    let poolNpkDaftar = [];

    // AMAN & DINAMIS: Mengambil ID Hadiah langsung dari URL panggung jika passing route, atau fallback ke blade template
    let urlSegments = window.location.pathname.split('/');
    let lastSegment = urlSegments[urlSegments.length - 1];
    let currentHadiahId = (!isNaN(lastSegment) && lastSegment !== "") ? lastSegment : "{{ $hadiahAktif ? $hadiahAktif->id : '' }}";

    let listPemenangSah = [];
    let currentPemenangData = null;
    let totalKuotaUndian = parseInt("{{ $totalKuota }}") || 0;
    let jenisKategori = 'all_plant';

    const audioSpin = new Audio("{{ asset('audio/spin.mp3') }}");
    audioSpin.loop = true;
    const audioSelesai = new Audio("{{ asset('audio/spin_selesai.mp3') }}");

    // Load pool acakan awal
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

    function startSpinningProcess() {
        // Validasi ekstra sebelum AJAX ditembakkan
        if (!currentHadiahId || currentHadiahId === "") {
            stopSpinOnError("ID Hadiah kosong atau tidak terdeteksi di panggung undian!");
            return;
        }

        $('#btnUndiSekarang').prop('disabled', true).addClass('d-none');
        $('#btnLanjutUndi').addClass('d-none');
        $('#btnUndiUlang').addClass('d-none');
        $('#btnLihatSummary').addClass('d-none');

        let slotBerjalan = listPemenangSah.length + 1;

        $('#namaKaryawan, #seksi, #plant').removeClass('show');
        $('#infoSlot').text(`Mengundi Pemenang Ke-${slotBerjalan} dari ${totalKuotaUndian}`).removeClass('d-none');

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
            url: "/api/undian/kocok-satu-slot",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                hadiah_id: currentHadiahId,
                current_slot: slotBerjalan
            },
            success: function(response) {
                if(response.success) {
                    let pemenangRaw = response.data_pemenang;
                    currentPemenangData = Array.isArray(pemenangRaw) ? pemenangRaw[0] : pemenangRaw;
                    jenisKategori = response.kategori_undian || 'all_plant';

                    setTimeout(function() {
                        audioSpin.pause();
                        clearInterval(intervalNpk);

                        $('#NPK').text(currentPemenangData.npk).addClass('show');

                        setTimeout(function() {
                            $('#namaKaryawan').text(currentPemenangData.nama_karyawan.toUpperCase()).addClass('show');

                            setTimeout(function() {
                                $('#seksi').text(currentPemenangData.seksi.toUpperCase()).addClass('show');

                                setTimeout(function() {
                                    $('#plant').text(currentPemenangData.plant.toUpperCase()).addClass('show');
                                    $('#btnUndiUlang').removeClass('d-none');

                                    if ((listPemenangSah.length + 1) >= totalKuotaUndian) {
                                        $('#btnLihatSummary').removeClass('d-none');
                                    } else {
                                        $('#btnLanjutUndi').removeClass('d-none');
                                    }
                                }, 1500);
                            }, 1500);
                        }, 1500);
                    }, 4000);
                } else {
                    stopSpinOnError(response.message);
                }
            },
            error: function(xhr) {
                // FIX: Jika backend melempar validasi kuota habis (422), baca response JSON aslinya, jangan langsung dibilang putus server!
                if (xhr.status === 422 && xhr.responseJSON) {
                    stopSpinOnError(xhr.responseJSON.message);
                } else {
                    stopSpinOnError("Gagal terhubung dengan server undian. Silakan periksa koneksi atau ID Hadiah.");
                }
            }
        });
    }

    function stopSpinOnError(msg) {
        audioSpin.pause();
        clearInterval(intervalNpk);
        alert(msg);
        if (listPemenangSah.length >= totalKuotaUndian && totalKuotaUndian > 0) {
            $('#btnLihatSummary').removeClass('d-none');
        } else if (listPemenangSah.length > 0) {
            $('#btnLanjutUndi').removeClass('d-none');
        } else {
            $('#btnUndiSekarang').prop('disabled', false).removeClass('d-none');
        }
    }

    $('#btnUndiSekarang').click(function() {
        startSpinningProcess();
    });

    $('#btnLanjutUndi').click(function() {
        if (currentPemenangData) {
            listPemenangSah.push(currentPemenangData);
            currentPemenangData = null;
        }
        startSpinningProcess();
    });

    $('#btnUndiUlang').click(function() {
        if(!confirm("Apakah Anda yakin ingin MENGGUGURKAN peserta ini? Statusnya akan menjadi HANGUS dan tidak akan bisa mendapatkan hadiah lain.")) {
            return;
        }

        if (currentPemenangData) {
            $.ajax({
                url: "/api/undian/gugurkan-peserta",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    npk: currentPemenangData.npk,
                    status: 'Gugur'
                },
                success: function() {
                    currentPemenangData = null;
                    startSpinningProcess();
                },
                error: function() {
                    alert("Gagal memperbarui status pembatalan di server.");
                }
            });
        }
    });

    $('#btnLihatSummary').click(function() {
        if (currentPemenangData) {
            listPemenangSah.push(currentPemenangData);
            currentPemenangData = null;
        }

        audioSelesai.currentTime = 0;
        audioSelesai.play().catch(e => console.log(e));

        $('#smJudulHadiah').text($('#namaHadiah').text().trim());
        $('#smKuotaHadiah').text($('#totalHadiah').text().trim());
        $('#smFotoHadiah').attr('src', $('#gambarHadiah').attr('src'));
        $('#boxSummaryPemenang').css('background-image', 'url("{{ asset('/images/bg_menang.png') }}")');

        if (jenisKategori.trim().toLowerCase() === 'per_plant') {
            let grupPlant = {};

            listPemenangSah.forEach(p => {
                let namaPlant = p.plant.toUpperCase().trim();
                if (namaPlant === 'BEKASI' || namaPlant === 'SUNTER') {
                    namaPlant = 'BEKASI & SUNTER';
                }
                if (!grupPlant[namaPlant]) grupPlant[namaPlant] = [];
                grupPlant[namaPlant].push(p);
            });

            let htmlPerPlant = `<div class="row g-4 justify-content-center align-items-start">`;
            Object.keys(grupPlant).forEach(plantName => {
                if (plantName === 'BEKASI & SUNTER') {
                    htmlPerPlant += `
                        <div class="col-md-6 kolom-grup-plant text-center">
                            <div class="title-grup-plant">${plantName}</div>
                            <div class="d-flex flex-row gap-3 justify-content-center">
                    `;
                    grupPlant[plantName].forEach(pemenang => {
                        htmlPerPlant += `
                            <div class="card-pemenang-putih w-100">
                                <p class="sm-npk">${pemenang.npk}</p>
                                <p class="sm-nama">${pemenang.nama_karyawan.toUpperCase()}</p>
                                <p class="sm-seksi">${pemenang.seksi.toUpperCase()}</p>
                                <p class="sm-plant">${pemenang.plant.toUpperCase()}</p>
                            </div>
                        `;
                    });
                    htmlPerPlant += `</div></div>`;
                } else {
                    htmlPerPlant += `
                        <div class="col-md-3 kolom-grup-plant text-center">
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
                }
            });
            htmlPerPlant += `</div>`;
            $('#containerHasilSummary').html(htmlPerPlant);

        } else {
            let htmlAllPlant = `<div class="row row-cols-1 row-cols-md-4 g-4 justify-content-center">`;
            listPemenangSah.forEach(pemenang => {
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
    });

    $('#btnDownloadSummary').click(function() {
        const targetElement = document.getElementById('boxSummaryPemenang');
        const namaHadiahFile = $('#smJudulHadiah').text().trim().replace(/[^a-z0-9]/gi, '_').toLowerCase();

        $('.summary-action-panel').hide();

        html2canvas(targetElement, {
            useCORS: true,
            allowTaint: false,
            scale: 2
        }).then(canvas => {
            const imageContainer = canvas.toDataURL("image/png");

            const triggerDownload = document.createElement('a');
            triggerDownload.href = imageContainer;
            triggerDownload.download = `pemenang_doorprize_${namaHadiahFile}.png`;

            document.body.appendChild(triggerDownload);
            triggerDownload.click();
            document.body.removeChild(triggerDownload);

            $('.summary-action-panel').show();
        }).catch(err => {
            console.error("Gagal men-download gambar:", err);
            $('.summary-action-panel').show();
            alert("Terjadi kendala saat memproses download gambar.");
        });
    });
});
</script>
</body>
</html>
