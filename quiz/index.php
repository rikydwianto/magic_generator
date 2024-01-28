<?php
require './../vendor/autoload.php'; // Impor library Dotenv
require './../proses/global_fungsi.php';
include_once "./../config/setting.php";
include_once "./../config/koneksi.php";

// var_dump($_SESSION);
$id_kuis = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query untuk mengecek apakah kuis dengan ID tertentu ada atau tidak
$sql = "SELECT * FROM kuis WHERE id_kuis = :id_kuis";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_kuis', $id_kuis, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
// ... (tampilkan halaman kuis)
// Jika kuis tidak ditemukan, redirect ke halaman 404
if ($stmt->rowCount() === 0 || $row['status'] == 'tidakaktif') {
    pindah($url_quiz . "404.php"); // Gantilah 404.php dengan halaman 404 yang sesuai
    exit();
}

$readonly = "";
if (isset($_GET['post-test']) && isset($_SESSION['unique_id'])) {
    $readonly = "readonly";
    $unique_id = $_SESSION['unique_id'];
    $q = $pdo->prepare("select * from kuis_jawab where unique_id=:unik ");
    $q->bindParam(":unik", $unique_id);
    $q->execute();
    $hasil = $q->fetch();
} else {
    $hasil['nama'] = null;
    $hasil['cabang'] = null;
    $hasil['nik'] = null;

    if (isset($_SESSION['unique_id']) && isset($_SESSION['nik'])) {
        if ($_SESSION['id_kuis'] == $id_kuis) {
            pindah($url_quiz . "mulai_quiz.php");
        }
    }
} ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HALAMAN QUIZ | COMDEV | <?= $row['nama_kuis'] ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@9">
</head>

<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0 text-center"><?= $row['nama_kuis'] ?></h3>
                    </div>
                    <div class="card-body">
                        <form id="karyawanForm" method="post">
                            <div class="form-group">
                                <label for="cabang">Cabang:</label>
                                <input type="text" class="form-control" value='<?= $hasil['cabang'] ? $hasil['cabang'] : "" ?>' <?= $readonly ?> id="cabang" name="cabang" placeholder="Masukkan Nama Cabang" required>
                            </div>
                            <div class="form-group">
                                <label for="nama">Nama:</label>
                                <input type="text" class="form-control" value='<?= $hasil['nama'] ? $hasil['nama'] : "" ?>' <?= $readonly ?> id="nama" name="nama" placeholder="Masukkan Nama Anda" required>
                            </div>
                            <div class="form-group">

                                <?php
                                if ($row['anggota'] == 'ya') {
                                ?>
                                    <label for="nik">CENTER:</label>
                                    <input type="text" class="form-control" value='<?= $hasil['nik'] ? $hasil['nik'] : "" ?>' <?= $readonly ?> id="nik" name="nik" placeholder="Masukkan CENTER" required>
                                <?php
                                } else {
                                ?>
                                    <label for="nik">NIK:</label>
                                    <input type="text" class="form-control" value='<?= $hasil['nik'] ? $hasil['nik'] : "" ?>' <?= $readonly ?> id="nik" name="nik" placeholder="Masukkan NIK Anda" required>
                                <?php
                                }
                                ?>
                            </div>
                            <button type="button" class="btn btn-primary" name='input' id="submitBtn">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    if (isset($_POST['nik']) && isset($_POST['nama'])) {
        $unique_id = uniqid() . uniqid(); // Membuat Unique ID secara acak
        $nik = $_POST['nik'];
        $nama = $_POST['nama'];
        $cabang = $_POST['cabang'];

        // Tanggal sekarang
        $now = date('Y-m-d H:i:s');
        $jenis = 'pre';
        if (isset($_GET['post-test']) && isset($_SESSION['unique_id'])) {
            $jenis = 'post';
            $unique_id_2 = $_GET['unik'];
        } else $unique_id_2 = $unique_id;

        // Query untuk menyimpan data ke dalam tabel kuis_jawab
        $sql = "INSERT INTO kuis_jawab (id_kuis, unique_id, nik, nama, cabang, created,unique_id_2,jenis_kuis)
        VALUES (:id_kuis, :unique_id, :nik, :nama, :cabang, :created,:unique_id_2,:jenis)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_kuis', $id_kuis, PDO::PARAM_INT);
        $stmt->bindParam(':unique_id', $unique_id, PDO::PARAM_STR);
        $stmt->bindParam(':unique_id_2', $unique_id_2, PDO::PARAM_STR);
        $stmt->bindParam(':nik', $nik, PDO::PARAM_STR);
        $stmt->bindParam(':nama', $nama, PDO::PARAM_STR);
        $stmt->bindParam(':cabang', $cabang, PDO::PARAM_STR);
        $stmt->bindParam(':created', $now, PDO::PARAM_STR);
        $stmt->bindParam(':jenis', $jenis, PDO::PARAM_STR);

        // Eksekusi query
        try {
            $stmt->execute();
            // echo "Data berhasil disimpan!";
            $_SESSION['unique_id'] = $unique_id;
            $_SESSION['id_kuis'] = $id_kuis;
            $_SESSION['id_kuis_jawab'] = $pdo->lastInsertId();
            pindah($url_quiz . "mulai_quiz.php");
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }



    ?>



    <!-- Bootstrap JS and Popper.js -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <script src='<?= $url_quiz . 'script_quiz.js' ?>'></script>

</body>

</html>