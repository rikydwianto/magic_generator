<?php
require './../vendor/autoload.php'; // Impor library Dotenv
require './../proses/global_fungsi.php';
include_once "./../config/setting.php";
include_once "./../config/koneksi.php";

// var_dump($_SESSION);
$id_kuis = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT * FROM kuis WHERE id_kuis = :id_kuis";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_kuis', $id_kuis, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);


$readonly = "";
if (isset($_GET['post-test'])) {
    $readonly = "readonly";
    $unique_id = $_GET['unik'];
    $q = $pdo->prepare("select * from kuis_jawab where unique_id=:unik and id_kuis=:id_kuis");
    $q->bindParam(":unik", $unique_id);
    $q->bindParam(":id_kuis", $id_kuis);
    $q->execute();

    $hasil = $q->fetch();
    // if (!$hasil) {
    //     pindah($url_quiz . 'reset.php?id=' . $id_kuis);
    // }
    if (!isset($_GET['unik'])) {
        pindah($url_quiz . "index.php?id=$id_kuis&unik=$unique_id");
    }
} else {
    $hasil['nama'] = null;
    $hasil['cabang'] = null;
    $hasil['nik'] = null;
    $hasil['unique_id_2'] = null;
    $hasil['nik'] = null;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HALAMAN QUIZ | COMDEV | <?= $row ? $row['nama_kuis'] :  "" ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- SweetAlert CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@9"> -->
    <script>
    let url_api = "<?php echo $url_api ?>";
    let url_quiz = "<?php echo $url_quiz ?>";
    let id_kuis = "<?= $id_kuis ?>";
    let mulai = localStorage.getItem("mulai");
    var dataLocalStorage = localStorage.getItem("unique_id");

    console.log(localStorage);
    if (mulai == 'ya') {
        let id_kuis = localStorage.getItem("id_kuis");
        let id_jawab = localStorage.getItem("id_kuis_jawab");
        window.location.href = url_quiz + "handle_soal.php?id_kuis=" + id_kuis + "&id_jawab=" + id_jawab;

    }
    </script>
    <script src="<?= $url_quiz . 'index.js?v=' . $timestamp ?>"></script>

</head>

<body>
    <?php



    $disabled = 'disabled';

    if ($stmt->rowCount() > 0) {
        if ($row['status'] == 'aktif') {
            $disabled = '';
        } else {
            $readonly = 'readonly';
        }
    ?>
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
                                <input type="text" class="form-control"
                                    value='<?= $hasil['cabang'] ? $hasil['cabang'] : "" ?>' <?= $readonly ?> id="cabang"
                                    name="cabang" placeholder="Masukkan Nama Cabang" required>
                            </div>
                            <div class="form-group">
                                <label for="nama">Nama:</label>
                                <input type="text" class="form-control"
                                    value='<?= $hasil['nama'] ? $hasil['nama'] : "" ?>' <?= $readonly ?> id="nama"
                                    name="nama" placeholder="Masukkan Nama Anda" required>
                                <input type="hidden" class="form-control"
                                    value='<?= $hasil['unique_id_2'] ? $hasil['unique_id_2'] : "" ?>' <?= $readonly ?>
                                    id="unique_2" name="unique_2">
                            </div>
                            <div class="form-group">

                                <?php
                                    if ($row['anggota'] == 'ya') {
                                    ?>
                                <label for="nik">CENTER:</label>
                                <input type="text" class="form-control"
                                    value='<?= $hasil['nik'] ? $hasil['nik'] : "" ?>' <?= $readonly ?> id="nik"
                                    name="nik" placeholder="Masukkan CENTER" required>
                                <?php
                                    } else {
                                    ?>
                                <label for="nik">NIK:</label>
                                <input type="text" class="form-control"
                                    value='<?= $hasil['nik'] ? $hasil['nik'] : "" ?>' <?= $readonly ?> id="nik"
                                    name="nik" placeholder="Masukkan NIK Anda" required>
                                <?php
                                    }
                                    ?>
                            </div>

                            <?php
                                if ($row['status'] != 'aktif') {
                                    pesan("Kuis Sedang Tidak Diaktifkan", 'danger');
                                }
                                ?>
                            <button type="button" <?= $disabled ?> class="btn btn-primary" name='input'
                                id="submitBtn">LANJUTKAN</button>

                            <?php if (isset($_GET['post-test'])) {
                                ?>
                            <a href="<?= $url_quiz . 'reset.php?id=' . $id_kuis ?>" class="btn btn-danger">ISI BARU</a>
                            <?php
                                } ?>
                            <br>
                            <br>
                            <a href="<?= $url_quiz . "lihat_hasil.php?id=" . $id_kuis ?>" class="btn btn-primary">Lihat
                                Hasil
                                Sebelumnya</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    } else {
    ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0 text-center">PILIH KUIS(AKTIF)</h3>
                    </div>
                    <div class="card-body">

                        <table class='table'>
                            <tr>
                                <th>NO</th>
                                <th>NAMA KUIS</th>
                                <th>TIPE</th>
                                <th>MASUK</th>
                            </tr>
                            <?php
                                $query = "SELECT * FROM kuis where status='aktif' order by id_kuis desc";

                                // Persiapkan dan eksekusi statement
                                $stmt = $pdo->prepare($query);
                                $stmt->execute();

                                // Fetch hasil query
                                $no = 1;
                                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($result as $key => $row) {
                                ?>
                            <tr>

                                <td><?= $no++ ?></td>
                                <td><?= $row['nama_kuis'] ?></td>
                                <td>
                                    <?= $row['anggota'] == 'ya' ? "ANGGOTA" : "STAFF" ?>
                                </td>
                                <td>
                                    <a href="<?= $url_quiz . "index.php?id=" . $row['id_kuis'] ?>"
                                        class="btn btn-lg btn-primary">MASUK</a>
                                </td>
                            </tr>
                            <?php
                                }
                                ?>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    }
    if ($hasil['nama'] != null) {
    ?>

    <?php
    }
    ?>



    <?php
    if (isset($_POST['nik']) && isset($_POST['nama'])) {
        $unique_id = uniqid() . uniqid(); // Membuat Unique ID secara acak
        $nik = $_POST['nik'];
        $nama = $_POST['nama'];
        $cabang = $_POST['cabang'];

        // Tanggal sekarang
        $now = date('Y-m-d H:i:s');
        $jenis = 'pre';
        if (isset($_GET['post-test'])) {
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
            $id_jawab = $pdo->lastInsertId();
            // echo "Data berhasil disimpan!";
            $_SESSION['unique_id'] = $unique_id;
            $_SESSION['id_kuis'] = $id_kuis;
            $_SESSION['id_kuis_jawab'] = $id_jawab;


    ?>
    <script>
    let unik = "<?= $unique_id ?>";
    localStorage.setItem('unique_id', unik);
    </script>
    <?php

            pindah($url_quiz . "mulai_quiz.php");
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }




    ?>




    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>


    <script src='<?= $url_quiz . 'script_quiz.js?v=' . $timestamp ?>'></script>
    <script>
    // console.log(localStorage)
    </script>
</body>

</html>