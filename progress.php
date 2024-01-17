<?php
require 'vendor/autoload.php'; // Impor library Dotenv
require 'proses/global_fungsi.php';
include_once "./config/setting.php";
include_once "./config/koneksi.php";
require("vendor/PHPExcel/Classes/PHPExcel.php");
@$id_cabang = $_SESSION['id_cabang'];
@$id_login = $_SESSION['idLogin'];
@$regional = $_SESSION['regional'];
@$jabatan = $_SESSION['jabatan'];


?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" /> <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.0/css/buttons.dataTables.min.css">
    <!-- //cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <style>
        html,
        body {
            overflow-x: hidden;
            margin-bottom: 60px;
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

        .footer {
            margin-top: 100px;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #343a40;
            color: #ffffff;
            text-align: center;
            padding-top: 10px;
            padding-bottom: 10px;
        }
    </style>
    <title>PROGRESS TOOL</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark justify-content-center m-3">
        <a class="navbar-brand" href="#"> &nbsp; Comdev Tool</a>
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
                    <a class="nav-link" href="<?= $url ?>progress.php">PROGRESS</a>
                </li>

            </ul>
        </div>
    </nav>

    <div class="row">
        <div class="container container-fluid">
            <div class="container-fluid table-responsive ">
                <div class="row">
                    <?php

                    if ($regional != "" && $id_login != "") {
                        include "./proses/layout/navbar_progress.php";
                    } else {
                    ?>
                        <nav id="sidebar" class="col-md-2 col-lg-2 d-md-block sidebar">
                            <div class="sidebar-sticky"></div>
                        </nav>
                    <?php
                    }
                    ?>

                    <!-- Konten -->
                    <main role="main" class="col-md-10 ml-sm-auto col-lg-10 ">
                        <div id="content">

                            <?php
                            if ($regional == "" && $id_login == "") {
                                include "./proses/view/login_progres.php";
                            } else {
                                $stmt = $pdo->prepare("SELECT users.*, cabang.*
                                FROM users
                                JOIN cabang ON users.id_cabang = cabang.id_cabang
                                WHERE users.id = ?;
                                ");
                                $stmt->execute([$id_login]);
                                $detailAkun = $stmt->fetch(PDO::FETCH_ASSOC);
                                if ($detailAkun) {
                                } else {
                                    pindah($url);
                                }
                                $menuPath = "./proses/progres/";
                                @$menu = $_GET['menu'];
                                $indexPath = $menuPath . $menu . ".php";
                                $jabatan = $detailAkun['jabatan'];
                                if ($menu == "" || $menu == "index") {
                                    include $menuPath . "index" . ".php";
                                } else {
                                    if (file_exists($indexPath)) {
                                        // File index.php ditemukan, lakukan inclusion
                                        include $indexPath;
                                    } else {
                                        // File index.php tidak ditemukan, tampilkan pesan 404
                                        echo 'Halaman tidak ditemukan';
                                    }
                                }
                            }
                            ?>

                        </div>
                    </main>

                </div>
            </div>


        </div>
    </div>
    <div class="footer mt-5">
        &copy; <?= date("Y") ?> Community Development | Riky Dwianto
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="<?= $url ?>assets/js/script_progres.js"></script>


</body>

</html>
<?php $pdo = null ?>