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
$id_kuis = $_GET['id_kuis'];
$id_soal = $_GET['id_soal'];
$ket = $_GET['ket'];
$query = "SELECT * FROM kuis k  join soal s on s.id_kuis=k.id_kuis  WHERE  k.id_kuis='$id_kuis' and s.id_soal='$id_soal'";
$stmt = $pdo->query($query);
$kuis = $stmt->fetch();
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

    <title>ANALISA JAWABAN</title>
</head>

<body>


    <div class="row m-2">
        <div class="container">

            <h2>ANALISA JAWABAN</h2>

            <table class='table table-bordered'>
                <tr>
                    <th>Nama Kuis</th>
                    <td><?= $kuis['nama_kuis'] ?></td>
                </tr>
                <tr>
                    <th>SOAL</th>
                    <td><?= $kuis['soal'] ?></td>
                </tr>
            </table>
            <hr>
            <div class="row">
                <h1 class="text-center ">RESPONDEN YANG MENJAWAB <?= $ket ?> </h1>

                <?php
                try {


                    $stmt = $pdo->prepare("SELECT * FROM soal_jawab sj JOIN kuis_jawab kj ON sj.id_jawab=kj.id_jawab WHERE sj.id_kuis=:id_kuis AND sj.id_soal=:id_soal AND sj.keterangan=:keterangan");
                    $stmt->bindParam(':id_kuis', $id_kuis, PDO::PARAM_INT);
                    $stmt->bindParam(':id_soal', $id_soal, PDO::PARAM_INT);
                    $stmt->bindParam(':keterangan', $ket, PDO::PARAM_STR);
                    $stmt->execute();

                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($result) > 0) {
                ?>
                <table class="table">
                    <thead>
                        <tr>

                            <th>NO</th>
                            <th>CABANG</th>
                            <th>NAMA</th>
                            <th>MENJAWAB</th>
                            <th>JENIS TEST</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                                $no = 1;
                                foreach ($result as $row) {
                                ?>
                        <tr>

                            <td><?= $no++ ?></td>
                            <td><?= $row['cabang'] ?></td>
                            <td><?= $row['nama'] ?></td>
                            <td><?= strtoupper($row['pilihan']) . " . " . getTeksById(json_decode($kuis['pilihan'], true), $row['pilihan']) ?>
                            </td>
                            <td><?= $row['jenis_kuis'] ?></td>
                        </tr>
                        <?php
                                }
                                ?>
                    </tbody>
                </table>
                <?php
                    } else {
                        echo 'Data tidak ditemukan.';
                    }
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
                ?>

            </div>
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
    });
    </script>



</body>

</html>