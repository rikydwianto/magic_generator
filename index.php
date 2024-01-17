<?php
require 'vendor/autoload.php'; // Impor library Dotenv
require 'proses/global_fungsi.php';
include_once "./config/setting.php";
include_once "./config/koneksi.php";
require("vendor/PHPExcel/Classes/PHPExcel.php");


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
    </style>
    <title>COMDEV TOOL</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark justify-content-center m-3">
        <a class="navbar-brand" href="#"> &nbsp; Comdev Tool</a>
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
                    <a class="nav-link" href="<?= $url ?>index.php?menu=index">CONTROL ROOM</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $url ?>progress.php">PROGRESS</a>
                </li>
                <?php
                if ($sesi != '' || $sesi != null ) {
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
                if ($menu == "cek_par") {
                    include("./proses/cek_par.php");
                } else if ($menu == 'anal') {
                    include("./proses/analisis.php");
                } else if ($menu == 'proses_delin') {
                    include("./proses/proses_delin.php");
                } else if ($menu == 'index') {
                    include("./proses/index.php");
                } else {
            ?>
            <h1>Halaman Awal</h1>
            <?php
                }
            }
            ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <script>
    $(document).ready(function() {
        $('#example').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ]
        });
        $('#example2').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ]
        });
    });

    $(document).ready(function() {
        var table = $('#soalTable').DataTable();

        // Tambahkan filter berdasarkan kategori dan subkategori
        $('#kategoriFilter, #subkategoriFilter').change(function() {
            var kategori = $('#kategoriFilter').val();
            var subkategori = $('#subkategoriFilter').val();

            table.column(4).search(kategori).draw();
            table.column(5).search(subkategori).draw();
        });
    });
    $(document).ready(function() {
        // Tambahkan pilihan
        $(".tambah-pilihan").click(function() {
            var hurufTerakhir = $("#pilihan-container .input-group").length + 1;
            var huruf = String.fromCharCode(64 +
                hurufTerakhir); // Mengubah angka menjadi huruf (A, B, C, ...)

            var html = '<div class="input-group mb-2">' +
                '<div class="input-group-prepend">' +
                '<span class="input-group-text">' + huruf + '</span>' +
                '</div>' +
                '<input type="text" class="form-control" name="pilihan[]" placeholder="Teks Pilihan" required>' +
                '<div class="input-group-append">' +
                '<button class="btn btn-danger hapus-pilihan" type="button">-</button>' +
                '</div>' +
                '</div>';
            $("#pilihan-container").append(html);
        });

        // Hapus pilihan
        $("#pilihan-container").on("click", ".hapus-pilihan", function() {
            $(this).closest('.input-group').remove();
            // Update huruf setelah menghapus pilihan
            $("#pilihan-container .input-group").each(function(index) {
                var huruf = String.fromCharCode(65 +
                    index); // Mengubah angka menjadi huruf (A, B, C, ...)
                $(this).find(".input-group-prepend .input-group-text").text(huruf);
            });
        });
        // Validasi pilihan tidak boleh kosong

        var quill = new Quill('#editor', {
            theme: 'snow', // Tema 'snow' cocok untuk tampilan editor yang bersih
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'], // Format teks
                    ['image', 'link'], // Sisipkan gambar dan tautan
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }], // Daftar terurut dan tidak terurut
                    ['clean'] // Bersihkan semua format
                ]
            }
        });
        quill.root.style.fontSize = '1.5rem';

        document.getElementById('myForm').addEventListener('submit', function() {
            document.getElementById('soal').value = quill.root.innerHTML;
        });


    });
    </script>



</body>

</html>
<?php $pdo = null ?>