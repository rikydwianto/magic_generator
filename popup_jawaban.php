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
$id_jawab = $_GET['id'];
$query = "SELECT * FROM kuis k JOIN kuis_jawab kj ON kj.`id_kuis`=k.`id_kuis` WHERE kj.`id_jawab`='$id_jawab' and k.id_kuis='$id_kuis'";
$stmt = $pdo->query($query);
$kuis = $stmt->fetch(); ?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.0/css/buttons.dataTables.min.css">
    <!-- //cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css -->

    <title>DETAIL JAWABAN <?= $kuis['nama_kuis'] ?></title>
</head>

<body>


    <div class="row m-2">
        <div class="container">

            <h2>Detail Jawaban</h2>

            <table class='table table-bordered'>
                <tr>
                    <th>Nama Kuis</th>
                    <td><?= $kuis['nama_kuis'] ?></td>
                </tr>
                <tr>
                    <th>Nama</th>
                    <td><?= $kuis['nama'] ?></td>
                </tr>
                <tr>
                    <th>Benar</th>
                    <td><?= $kuis['benar'] ?></td>
                </tr>
                <tr>
                    <th>Salah</th>
                    <td><?= $kuis['salah'] ?></td>
                </tr>
                <tr>
                    <th>Nilai</th>
                    <td><?= $kuis['total_score'] ?></td>
                </tr>
            </table>
            <hr>
            <div class="row">
                <h1 class="text-center ">Hasil Kuis</h1>

                <?php
                $qsoal = "SELECT
            sj.`id_soal_jawab`,
            sj.pilihan AS pilihuser,
            sj.`pilihan_benar`,
            soal.`soal`,
            soal.`pilihan`,
            sj.`keterangan`
          FROM
            soal_jawab sj
            RIGHT JOIN soal
              ON soal.`id_soal` = sj.`id_soal`
          WHERE id_jawab ='$id_jawab'";
                $no = 1;
                $stmt = $pdo->query($qsoal);

                foreach ($stmt->fetchAll() as $row) {
                    if ($row['keterangan'] == "BENAR")
                        $bg = "";
                    else $bg = "bg-danger text-white";
                ?>

                    <li class="list-group-item kuis-item <?= $bg ?> ">
                        <h5><?= $no ?> <?= $row['soal'] ?>
                        </h5>

                        <?php
                        $pilihan = json_decode($row['pilihan'], true);
                        foreach ($pilihan as $pil) {
                            if ($pil['id'] == $row['pilihan_benar']) {
                                echo "<b>" . strtoupper($pil['id']) . ". " . ($pil['teks']) . "</b><br/>";
                            } else {
                                echo strtoupper($pil['id']) . ". " . ($pil['teks']) . "<br/>";
                            }
                        }
                        ?>
                        <p>
                            Jawab: <?= strtoupper($row['pilihuser']) ?> | <?= $row['keterangan'] ?>

                        </p>
                    </li>


                <?php
                    $no++;
                }
                ?>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
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