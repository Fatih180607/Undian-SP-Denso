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
            max-width: 500px; /* Membatasi lebar bingkai agar proporsional di panggung */
            height: auto;
        }

    .PrizePool {
            position: relative;
            display: inline-block;
            margin: 3rem; /* Memindahkan margin kesini agar aman */

    .container-panggung {
        gap: 15rem;
    }

    /* Kotak pembungkus teks & gambar hadiah */
    .konten-hadiah-wrapper {
            position: absolute;
            top: 41%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 80%; /* Membatasi lebar teks & gambar di dalam ruang bingkai */
            gap: 15px;
        }

    /* Style teks hadiah */
    .text-hadiah {
        color: white;
        font-size: 2.2rem; /* Sedikit disesuaikan ukurannya agar proporsional */
        font-weight: bold;
        text-align: center;
        margin: 0;
        width: 100%;
        word-wrap: break-word;
    }

    /* Style gambar hadiah */
    .GambarHadiah {
        width: 350px;   /* Membatasi ukuran gambar hadiah agar tidak kebesaran */
        height: auto;     /* Menjaga rasio gambar biar tidak gepeng */
        object-fit: contain;
    }
        </style>
    </head>
    <body>

    <div class="container-panggung d-flex flex-row align-items-center" style="height: 100vh; gap: 13rem;">
    <section class="PrizePool">
                <img src="{{ asset('images/conten.png') }}" class="bingkai" alt="Logo Denso">
                <div class="konten-hadiah-wrapper">
                    <h1 id="totalHadiah" style="color: black">8 Pcs</h1>
                    <p id="namaHadiah" class="text-hadiah">
                        TV LED 40 INCH POLYTRON GOOGLE TV
                    </p>
                    <img src="{{ asset('uploads/hadiah/hadiah_1779015727_6a09a02fb9055.png') }}" id="gambarHadiah" class="GambarHadiah " alt="Gambar Hadiah">
                </div>
        </section>
        <section>
    <ul style="text-align: center" class="list-unstyled">
        <li><p class="display-4 fw-bold m-0" style="font-size: 200px" id="NPK">2150145</p></li>
        <li><p class="display-4 fw-bold m-0" style="font-size: 100px" id="namaKaryawan">DEDE SUMIYATI</p></li>
        <li><p class="display-4 fw-bold m-0" style="font-size: 80px" id="seksi">HR</p></li>
        <li><p class="display-4 fw-bold m-0" style="font-size: 60px" id="plant">BEKASI</p></li>
    </ul>
    </section>
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
