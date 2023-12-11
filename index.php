<?php
require 'vendor/autoload.php'; // Impor library Dotenv
require 'proses/global_fungsi.php';
include_once "./config/setting.php";
include_once "./config/koneksi.php";
require("vendor/PHPExcel/Classes/PHPExcel.php");



?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>TOOL GENERATOR</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark justify-content-center m-3">
        <a class="navbar-brand" href="#"> &nbsp; Tool Generator</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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
                    <a class="nav-link" href="<?= $url ?>index.php?menu=logout">LOGOUT</a>
                </li>
                <!-- Tambahkan menu lain sesuai kebutuhan -->
            </ul>
        </div>
    </nav>

    <div class="row m-2">
        <div class="container">

            <?php
            @$sesi = $_SESSION['sesi'];

            // session_destroy();
            if ($sesi == '' || $sesi == null) {

                include("./proses/tanya.php");
            } else {

                @$menu = $_GET['menu'];

                if ($menu == "cek_par") {
                    include("./proses/cek_par.php");
                } else if ($menu == 'anal') {
                    include("./proses/analisis.php");
                } else if ($menu == 'proses_delin') {
                    include("./proses/proses_delin.php");
                } else if ($menu == 'logout') {
                    include("./proses/logout.php");
                } else {
            ?>
                    <h1>Halaman Awal</h1>
            <?php
                }
            }
            ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>



</body>

</html>