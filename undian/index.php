<?php
require './../vendor/autoload.php'; // Impor library Dotenv
require './../proses/global_fungsi.php';
include_once "./../config/setting.php";
include_once "./../config/koneksi.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNDIAN DOORPRIZE KOMIDA REGIONAL H</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@900&family=Julius+Sans+One&family=Kanit:wght@200&family=Kodchasan:ital,wght@0,500;0,600;1,400&family=Montserrat+Alternates:wght@600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
    body {
        font-family: 'Montserrat Alternates', sans-serif;

        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        margin: 0;
        background-image: url('uang.jpg');
        background-size: cover;

    }

    .undian-container {
        text-align: center;
        background-color: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }


    #result {
        font-size: 70px;
        font-weight: bold;
        color: green;
        margin-top: 20px;
    }

    .btn-primary,
    .btn-danger {
        font-size: 18px;
        padding: 10px 20px;
        margin-top: 20px;
    }
    </style>
</head>

<body>
    <div class="undian-container">
        <h1>UNDIAN DOORPRIZE</h1>
        <h1>KOMIDA REGIONAL H</h1>
        <hr>
        <h1 id="result" class="mt-4"></h1>
        <hr class="mt-5">
        <button class="btn btn-primary btn-lg" id='mulai' onclick="shuffleNumbers()">MULAI UNDIAN</button>
        <button class="btn btn-danger btn-lg" id='stop' onclick="stopShuffling()">STOP</button>
    </div>



    <script>
    var url = "<?= $url . 'undian/' ?>";
    var interval;
    var winner; // Menyimpan pemenang

    var interval;
    var winner;
    var numbers = [];

    function shuffleNumbers() {
        // Menghentikan interval sebelumnya jika ada
        clearInterval(interval);

        // Mengambil data dari API
        fetch(url + 'api_undian.php?ambil_data') // Gantilah URL dengan URL API yang sesuai
            .then(response => response.json())
            .then(data => {
                // Membuat array dari NIK dan nama
                numbers = data.map(entry => entry.nik);

                // Memulai pengacakan nomor
                startShuffling();
            })
            .catch(error => console.error('Error fetching data:', error));
    }

    function startShuffling() {
        // Pengacakan nomor dengan efek visual
        var i = 0;

        interval = setInterval(function() {
            // Jika sudah mencapai akhir daftar nomor, kocok kembali
            if (i >= numbers.length) {
                shuffleArray(numbers);
                i = 0; // Reset indeks
            }

            // Mengacak index nomor
            var randomIndex = Math.floor(Math.random() * (numbers.length - i)) + i;
            // Menampilkan nomor yang diacak
            document.getElementById("result").innerHTML = numbers[randomIndex];
            winner = numbers[randomIndex];
            i++;

        }, 50); // Mengatur interval 50 ms antara setiap langkah pengacakan
    }

    // Fungsi untuk mengacak array
    function shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
    }

    // Menghentikan pengacakan jika tombol "Henti Pengacakan" ditekan
    function stopShuffling() {
        clearInterval(interval);
    }

    function updateUndian(id) {

        $.ajax({
            type: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
            url: url + 'api_undian.php?update_dapat', // Sesuaikan dengan lokasi file update_api.php
            data: {
                nik: id,
            },
            success: function(response) {


                window.location.href = url
            },

        });
    }

    function stopShuffling() {
        // Menghentikan pengacakan jika tombol "Henti Pengacakan" ditekan
        clearInterval(interval);
        let pemenang = winner

        // https://www.komida.co.id/apphris/muka/007068-2019.jpg
        setTimeout(() => {
            const channel = new BroadcastChannel('myChannel');

            // Mengirim data ke channel
            channel.postMessage(winner);
            Swal.fire({
                icon: 'success',
                title: `SELAMAT ${winner}`,
                allowOutsideClick: false,
                text: `KEPADA NIK : ${winner} BERHAK MENDAPAT DOORPRIZE`,
                width: 800,
                height: 500,


            }).then((result) => {
                if (result.isConfirmed) {
                    updateUndian(winner);

                }
            });
        }, 500);

    }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>

</body>

</html>