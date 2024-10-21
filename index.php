<?php
require 'vendor/autoload.php'; // Impor library Dotenv
require 'proses/global_fungsi.php';
include_once "./config/setting.php";
include_once "./config/koneksi.php";
require("vendor/PHPExcel/Classes/PHPExcel.php");
set_time_limit(3000);

// echo '<a href="https://wa.me/6281214657370?text=Hallo%20pak%2C%20saya%20..%20dari%20cabang%20...%0A%0Aterimakasih%20ya" class="btn btn-success m-3">Send Greater to Creator :)</a>';
// echo "<h1>Terima kasih semua</h1>";
// exit;
@$sesi = $_SESSION['sesi'];
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" /> <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.0/css/buttons.dataTables.min.css">
    <!-- //cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <link rel="icon" type="image/png" sizes="32x32" href="./assets/img/logo.png" />
    <style>
        html,
        body {
            overflow-x: hidden;
        }

        body {
            background-color: #f8f9fa;
        }

        #sidebar {
            background-color: #343a40;
            color: #ced4da;
        }

        #sidebar .nav-link {
            color: #adb5bd;
        }

        #sidebar .nav-link.active {
            color: #fff;
        }

        #content {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* HTML: <div class="loader"></div> */
        .loader {
            margin: auto;
            width: 100px;
            aspect-ratio: 1;
            display: grid;
            border: 4px solid #0000;
            border-radius: 50%;
            border-right-color: #25b09b;
            animation: l15 1s infinite linear;
        }

        .loader::before,
        .loader::after {
            content: "";
            grid-area: 1/1;
            margin: 2px;
            border: inherit;
            border-radius: 50%;
            animation: l15 2s infinite;
        }

        .loader::after {
            margin: 8px;
            animation-duration: 3s;
        }

        @keyframes l15 {
            100% {
                transform: rotate(1turn)
            }
        }
    </style>
    <script>
        let url = "<?= $url ?>";
        let url_api = url + "api/";
    </script>
    <title>COMDEV TOOL</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark justify-content-center m-3">
        <a class="navbar-brand" href="#">
            <img src="./assets/img/logo.png" style="width: 50px;" class="img p-1" alt="">
            Comdev Tool</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav p-3">
                <li class="nav-item active">
                    <a class="nav-link" href="<?= $url ?>index.php">HOME <span class="sr-only"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $url ?>index.php?menu=cek_par">CEK PAR</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= $url ?>index.php?menu=anal">ANALISA PAR</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= $url ?>index.php?menu=center_meeting">CENTER</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $url ?>index.php?menu=delin_reg">REGIONAL</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $url ?>index.php?menu=index">CONTROL ROOM</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $url ?>progress.php">PROGRESS</a>
                </li>
                <?php
                if ($sesi != '' || $sesi != null) {
                ?>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= $url ?>logout.php?menu=logout">LOGOUT</a>
                    </li>
                <?php
                }
                ?>

                <!-- Tambahkan menu lain sesuai kebutuhan -->
            </ul>
        </div>
    </nav>

    <div class="row">
        <div class="container container-fluid">

            <?php

            @$menu = $_GET['menu'];
            // session_destroy();
            if ($sesi == '' || $sesi == null) {
                if ($menu == 'index') {
                    include("./proses/index.php");
                } else {
                    include("./proses/tanya.php");
                }
            } else {

                if ($menu == 'cek_par' || $menu == 'anal' || $menu == 'delin_reg' || $menu == 'proses_delin'  || $menu == 'center_meeting') {
            ?>
                    <div class="container mt-5">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h4 class="card-title">Peringatan Hosting Akan Segera Berakhir</h4>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    Dalam beberapa waktu ke depan, hosting untuk aplikasi ini akan habis.
                                    Jika Anda ingin tetap menggunakan aplikasi ini, Anda bisa bergabung dan berpartisipasi dalam
                                    pembayaran server agar aplikasi tetap berjalan.
                                </p>
                                <p class="card-text">
                                    <strong>Catatan:</strong> Server ini adalah server pribadi dan <strong>bukan</strong> server
                                    resmi dari Komida.
                                </p>
                                <div class="text-center">
                                    <a href="https://wa.me/6281214657370?text=Assalamualaikum%20pak%2C%20saya%20...%20dari%20cabang%20...%0AMau%20ikut%20berpartisisi%0Aterimakasih"
                                        class="btn btn-success m-3">Send Greater to Creator :)</a>
                                </div>
                            </div>
                        </div>

                    <?php
                } else {
                    echo '<a href="https://wa.me/6281214657370?text=Assalamualaikum%20pak%2C%20saya%20...%20dari%20cabang%20...%0AMau%20ikut%20berpartisisi%0Aterimakasih" class="btn btn-success m-3">+62 812 1465 7370</a>';
                }



                if ($menu == "cek_par") {
                    include("./proses/cek_par.php");
                } else if ($menu == 'anal') {
                    include("./proses/analisis.php");
                } else if ($menu == 'delin_reg') {
                    include("./proses/delin_reg.php");
                } else if ($menu == 'proses_delin') {
                    include("./proses/proses_delin.php");
                } else if ($menu == 'proses_delin_reg') {
                    include("./proses/proses_delin_reg.php");
                } else if ($menu == 'index') {
                    include("./proses/index.php");
                } else if ($menu == 'center_meeting') {
                    include("./proses/center_input.php");
                } else if ($menu == 'center_proses') {
                    include("./proses/center_proses.php");
                } else {
                    ?>
                        <div class="container-fluid">
                            <div class="row">
                                <h1>Halaman Awal!</h1>
                            </div>
                        </div>

                <?php
                }
            }
                ?>
                    </div>
        </div>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
        </script>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
        <script src="<?= $url ?>assets/js/script_index.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('staticBackdrop'));
                // myModal.show();
            });
        </script>



</body>

</html>
<?php $pdo = null ?>