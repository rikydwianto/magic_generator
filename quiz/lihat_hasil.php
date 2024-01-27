<?php
require './../vendor/autoload.php'; // Impor library Dotenv
require './../proses/global_fungsi.php';
include_once "./../config/setting.php";
include_once "./../config/koneksi.php";
$id_kuis = isset($_SESSION['id_kuis']) ? intval($_SESSION['id_kuis']) : 0;
$id_jawab = $_SESSION['id_kuis_jawab'];

$url_api = $url . "api/";


$hitung_soal = $pdo->prepare("SELECT COUNT(*) AS total_soal FROM soal WHERE id_kuis=:id_kuis");
$hitung_soal->bindParam(":id_kuis", $id_kuis);
$hitung_soal->execute();
$hitung_soal = $hitung_soal->fetch()['total_soal'];
$total_soal =  $hitung_soal;

// Query untuk mengecek apakah kuis dengan ID tertentu ada atau tidak
$sql = "SELECT * FROM kuis WHERE id_kuis = :id_kuis";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_kuis', $id_kuis, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($stmt->rowCount() === 0 || $row['status'] == 'tidakaktif') {
    pindah($url_quiz . "404.php"); // Gantilah 404.php dengan halaman 404 yang sesuai
    exit();
}
$query = "SELECT * FROM kuis k JOIN kuis_jawab kj ON kj.`id_kuis`=k.`id_kuis` WHERE kj.`id_jawab`='$id_jawab' and k.id_kuis='$id_kuis'";
$stmt = $pdo->query($query);
$kuis = $stmt->fetch();
$unik2 = $kuis['unique_id_2'];

$hitungTest = $pdo->prepare("select count(*) as total_test from kuis_jawab where jenis_kuis='post' and unique_id_2=?");
$hitungTest->execute([$unik2]);
$hitungTest = $hitungTest->fetch();
$hitungTest = ($hitungTest['total_test']);
$tampil_jawaban = $row['tampil_jawaban'];
if ($hitungTest >= 1) {
    $tampil_jawaban = 'ya';
}
if ($kuis['benar'] + $kuis['salah'] == $total_soal) {
    // echo "sama";
} else {
    // echo "update";
    // Data yang akan dikirim ke server
    $data = array(
        'id_kuis' => $id_kuis,
        'id_jawab' => $id_jawab,
    );

    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
        ),
    );

    $context = stream_context_create($options);

    $result = file_get_contents($url_api . "soal.php?update-kuis", false, $context);
    pindah($url_quiz . "lihat_hasil.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $row['nama_kuis'] ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@9">
    <script>
    localStorage.clear()
    </script>
    <style>
    body {
        user-select: none;
    }

    /* Style untuk menyembunyikan titik pada elemen <li> */
    .custom-list-item {
        list-style-type: none;
        padding-left: 10px;
        padding-top: 5px;
    }

    /* Style tambahan sesuai kebutuhan desain */
    .custom-list-item h5 {
        margin-bottom: 0;
        /* Menghilangkan margin bawah pada elemen h5 */
    }

    .custom-list-item p {
        margin-top: 0;
        /* Menghilangkan margin atas pada elemen p */
    }
    </style>

</head>

<body>

    <div class="container-fluid mt-5">
        <form action="" method="post" id="quizForm">
            <div class="row gx-5 justify-content-center">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0 text-center">HASIL KUIS</h4>
                        </div>
                        <div class="card-body">


                            <div id="questionContainer" class="question-container">


                                <div class="row">
                                    <div class="col">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Total Soal</h5>
                                                <h2 class="card-text text-center">
                                                    <?= $total_soal ?>
                                                </h2>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kolom Total Benar -->
                                    <div class="col">
                                        <div class="card bg-success text-white">
                                            <div class="card-body">
                                                <h5 class="card-title">Total Benar</h5>
                                                <h2 class="card-text text-center">
                                                    <?= $kuis['benar'] ? $kuis['benar'] : 0 ?></h2>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kolom Total Salah -->
                                    <div class="col">
                                        <div class="card bg-danger text-white">
                                            <div class="card-body">
                                                <h5 class="card-title">Total Salah</h5>
                                                <h2 class="card-text text-center">
                                                    <?= $kuis['salah'] ? $kuis['salah'] : 0 ?></h2>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kolom Total Score -->
                                    <div class="col">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">Total Score</h5>
                                                <h2 class="card-text text-center">
                                                    <?= $kuis['total_score'] ? $kuis['total_score'] : 0 ?></h2>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
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
                                                <th>Cabang</th>
                                                <td><?= $kuis['cabang'] ?></td>
                                            </tr>
                                            <tr>
                                                <th>Lama Pengerjaan</th>
                                                <td><?= $kuis['pengerjaan'] ?></td>
                                            </tr>
                                            <tr>
                                                <th>Jenis Test</th>
                                                <td><?= $kuis['jenis_kuis'] ?>-<?= $hitungTest ?> <br>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <td>
                                                    <?php
                                                    if ($hitungTest >= 3) {
                                                        echo "sudah tidak bisa post test";
                                                    } else {
                                                    ?>
                                                    <a href="<?= $url_quiz . "index.php?id=$id_kuis&post-test&unik=$kuis[unique_id_2]" ?>"
                                                        class="btn btn-success">LAKUKAN POST TEST</a>
                                                    <?php
                                                    }
                                                    ?>
                                                </td>
                                            </tr>

                                        </table>
                                    </div>
                                </div>



                                <h1 class="text-center mt-3 ">Hasil Kuis</h1>
                                <hr>

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

                                <li class="<?= $bg ?> custom-list-item">
                                    <h5><?= $no ?>. <?= $row['soal'] ?></h5>
                                    <div id="pilihan" style='padding-left:20px'>

                                        <?php
                                            $pilihan = json_decode($row['pilihan'], true);
                                            if ($tampil_jawaban == 'ya') {
                                                foreach ($pilihan as $pil) {
                                                    if ($pil['id'] == $row['pilihan_benar']) {
                                                        echo "<b>" . strtoupper($pil['id']) . ". " . ($pil['teks']) . "</b><br/>";
                                                    } else {
                                                        echo strtoupper($pil['id']) . ". " . ($pil['teks']) . "<br/>";
                                                    }
                                                }
                                            } else {
                                                foreach ($pilihan as $pil) {
                                                    echo strtoupper($pil['id']) . ". " . ($pil['teks']) . "<br/>";
                                                }
                                            }
                                            ?>
                                        <p>
                                            <?php
                                                if ($tampil_jawaban == 'ya') {
                                                ?>
                                            Kamu menjawab: <?= strtoupper($row['pilihuser']) ?> |
                                            <?= $row['keterangan'] ?>
                                            <?php
                                                }
                                                ?>
                                        </p>

                                    </div>
                                    <hr>
                                </li>



                                <?php
                                    $no++;
                                }
                                ?>
                                <a href="<?= $url_quiz . "reset.php?id=$id_kuis" ?>"
                                    class="btn btn-danger mb-3">Reset</a>

                            </div>
                        </div>
                    </div>
                </div>
        </form>
    </div>


    <!-- Bootstrap JS and Popper.js -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <script src='<?= $url_quiz . 'script_quiz.js' ?>'></script>

</body>

</html>