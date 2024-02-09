<?php
require 'vendor/autoload.php'; // Impor library Dotenv
require 'proses/global_fungsi.php';
include_once "./config/setting.php";
include_once "./config/koneksi.php";
require("vendor/PHPExcel/Classes/PHPExcel.php");
@$sesi = $_SESSION['idLogin'];

if ($sesi == '' || $sesi == null) {
    tutupWindow();
}
$nik = $_GET['nik'];
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.0/css/buttons.dataTables.min.css">
    <!-- //cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css -->

    <title>DETAIL CAPAIAN STAFF</title>
    <style>
    body {
        background-color: #f8f9fa;
    }

    .card {
        background-color: #ffffff;
        border: 1px solid #e1e5eb;
        border-radius: -10px;
        transition: transform 0.2s;
    }

    .card:hover {
        transform: scale(0.99);
    }

    .card-title {
        color: #fff;
        font-size: 15px;
        font-weight: bold;
    }

    .card-text {
        color: #fff;
        font-size: 18px;
    }
    </style>
</head>

<body>
    <?php
    $staff = $pdo->prepare("select * from staff where nik_staff=? order by cabang,nama_staff ");
    $staff->execute([$nik]);
    $staff = $staff->fetch(PDO::FETCH_ASSOC);
    ?>

    <div class="row m-2">
        <div class="container">

            <h2>DETAIL CAPAIAN <?= strtoupper($staff['nama_staff']) ?> CABANG <?= strtoupper($staff['cabang']) ?></h2>

            <hr>

        </div>
    </div>
    <div class="row m-2">
        <h5>Kumulatif Capaian <?= strtoupper($staff['nama_staff']) ?> CABANG <?= strtoupper($staff['cabang']) ?></h5>
        <?php
        // Nik yang akan digunakan sebagai parameter dalam query
        $nik = $nik; // Ganti sesuai dengan nik yang diinginkan
        // Query SQL dengan parameter nik
        $query = "
    SELECT
        cs.nik_staff,
        cs.nama_staff,
        SUM(ds.anggota_masuk) AS total_anggota_masuk,
        SUM(ds.anggota_keluar) AS total_anggota_keluar,
        SUM(ds.nett_anggota) AS total_nett_anggota,
        SUM(ds.naik_par) AS total_naik_par,
        SUM(ds.turun_par) AS total_turun_par,
        SUM(ds.nett_par) AS total_nett_par,
        SUM(ds.agt_tpk) AS total_agt_tpk,
        SUM(ds.pemb_lain) AS total_pemb_lain
    FROM
        detail_capaian_staff ds
    JOIN
        capaian_staff cs ON ds.id_capaian_staff = cs.id_capaian_staff
    WHERE
        cs.nik_staff = :nik and status='approve'
    GROUP BY
        cs.nik_staff
";

        // Mempersiapkan dan mengeksekusi query dengan parameter
        try {
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':nik', $nik, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Menampilkan hasil query

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        ?>
        <div class="row">
            <!-- Anggota -->
            <div class="col-md-4">
                <div class="card text-center bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">AK</h5>
                        <p class="card-text"><?= $result['total_anggota_keluar'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">AM</h5>
                        <p class="card-text"><?= $result['total_anggota_masuk'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">NETT AGT</h5>
                        <p class="card-text"><?= $result['total_nett_anggota'] ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Naik Par, Turun Par, Nett Par -->
            <div class="col-md-4">
                <div class="card text-center bg-warning text-dark">
                    <div class="card-body">
                        <h5 class="card-title">Naik Par</h5>
                        <p class="card-text"><?= angka($result['total_naik_par']) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Turun Par</h5>
                        <p class="card-text"><?= angka($result['total_turun_par']) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">NETT PAR</h5>
                        <p class="card-text"><?= angka($result['total_nett_par']) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Pembiayaan Lain dan Pengajuan TPK -->
            <div class="col-md-6">
                <div class="card text-center bg-secondary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Pembiayaan Lain</h5>
                        <p class="card-text"><?= angka($result['total_pemb_lain']) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-center bg-dark text-white">
                    <div class="card-body">
                        <h5 class="card-title">Pengajuan TPK</h5>
                        <p class="card-text"><?= angka($result['total_agt_tpk']) ?></p>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row m-2 table-responsive">
        <h3>DETAIL</h3>
        <hr>
        <table class='table table-bordered '>
            <thead>
                <tr>
                    <th>NO</th>
                    <th>CABANG</th>
                    <th>Minggu</th>
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>AM</th>
                    <th>AK</th>
                    <th>NETT</th>
                    <th>PAR NAIK</th>
                    <th>PAR TURUN</th>
                    <th>NETT PAR</th>
                    <th>PEMB LAIN</th>
                    <th>CUTI</th>
                    <th>PENGAJUAN TPK</th>
                    <th>KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $qcapaian = $pdo->prepare("select * from capaian_staff cs join detail_capaian_staff dc on cs.id_capaian_staff=dc.id_capaian_staff where cs.nik_staff=? order by cs.tahun,cs.bulan,cs.minggu desc ");
                $qcapaian->execute([$nik]);
                $data = $qcapaian->fetchAll(PDO::FETCH_ASSOC);
                foreach ($data as $row) {
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['cabang_staff'] ?></td>
                    <td><?= $row['minggu'] ?></td>
                    <td><?= $bulanArray[$row['bulan']] ?></td>
                    <td><?= $row['tahun'] ?></td>
                    <td><?= $row['anggota_masuk'] ?></td>
                    <td><?= $row['anggota_keluar'] ?></td>
                    <td><?= $row['nett_anggota'] ?></td>
                    <td><?= angka($row['naik_par']) ?></td>
                    <td><?= angka($row['turun_par']) ?></td>
                    <td><?= angka($row['nett_par']) ?></td>
                    <td><?= $row['pemb_lain'] ?></td>
                    <td><?= $row['agt_cuti'] ?></td>
                    <td><?= $row['agt_tpk'] ?></td>
                    <td><?= $row['keterangan'] ?></td>
                </tr>
                <?php
                }
                ?>

            </tbody>
        </table>
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
</body>

</html>