<?php
require './../vendor/autoload.php'; // Impor library Dotenv
require './../proses/global_fungsi.php';
include_once "./../config/setting.php";
include_once "./../config/koneksi.php";
$id_kuis_jawab = isset($_SESSION['id_kuis_jawab']) ? intval($_SESSION['id_kuis_jawab']) : 0;
$id_kuis = isset($_SESSION['id_kuis']) ? intval($_SESSION['id_kuis']) : 0;

// Query untuk mengecek apakah kuis dengan ID tertentu ada atau tidak
$sql = "SELECT * FROM kuis WHERE id_kuis = :id_kuis";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_kuis', $id_kuis, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!isset($_SESSION['unique_id']) && !isset($_SESSION['nik'])) {
    pindah($url_quiz . "404.php");
}

$keterangan = 'selesai';  // Ganti dengan nilai yang sesuai

$sql = "SELECT * FROM kuis_jawab WHERE id_jawab = :id_jawab AND keterangan = :keterangan";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_jawab', $id_kuis_jawab, PDO::PARAM_INT);
$stmt->bindParam(':keterangan', $keterangan, PDO::PARAM_STR);
$stmt->execute();
$kuis_jawab = $stmt->fetch(PDO::FETCH_ASSOC);

// Cek apakah data ditemukan
if ($kuis_jawab) {
    pindah($url_quiz . "lihat_hasil.php");
    exit();
} else {
    // Data tidak ditemukan atau belum selesai, lakukan tindakan lain
    // echo "Data tidak ditemukan atau belum selesai.";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HALAMAN QUIZ | COMDEV | <?= $row['nama_kuis'] ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- SweetAlert CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@9"> -->
    <script>
        let waktu = "<?= $row['waktu']; ?>";
        localStorage.setItem('waktu', waktu)
    </script>
</head>

<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">Peringatan Kuis</h2>
                    </div>
                    <div class="card-body">
                        <div>
                            <table class="table">
                                <tr>
                                    <td>Kuis </td>
                                    <td>:</td>
                                    <td><?= $row['nama_kuis'] ?></td>
                                </tr>
                                <tr>
                                    <td>Waktu </td>
                                    <td>:</td>
                                    <td><?= $row['waktu'] ?> Menit</td>
                                </tr>
                                <tr>
                                    <td>Pertanyaan </td>
                                    <td>:</td>
                                    <td><?= $row['acak'] == 'ya' ? "Diacak" : "Tidak diacak" ?> </td>
                                </tr>
                                <tr>
                                    <td>Pembuat Kuis </td>
                                    <td>:</td>
                                    <td><?= $row['nama_karyawan'] ?></td>
                                </tr>
                            </table>
                        </div>
                        <p class="text-info">Quiz akan dimulai sebentar lagi. Silakan baca instruksi dengan cermat.</p>

                        <div class="alert alert-warning" role="alert">
                            <strong>Peringatan:</strong> Setelah memulai kuis, soal tidak dapat diulang. Pastikan Anda
                            siap untuk memulai.
                        </div>


                        <form action="" id='startQuizForm' method="post">
                            <!-- Tambahkan input hidden untuk menyimpan data yang diperlukan -->
                            <input type="hidden" name="id_kuis"> <!-- Gantilah dengan ID kuis yang sesuai -->

                            <button type="button" id="startQuizBtn" class="btn btn-success">Mulai Kuis</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {


        // Tanggal dan waktu sekarang
        $now = date('Y-m-d H:i:s');
        // Update kolom 'mulai' di tabel 'kuis_jawab'
        $sql = "UPDATE kuis_jawab SET mulai = :mulai WHERE id_jawab = :id_kuis";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':mulai', $now, PDO::PARAM_STR);
        $stmt->bindParam(':id_kuis', $id_kuis_jawab, PDO::PARAM_INT);

        // Eksekusi pernyataan UPDATE
        $stmt->execute();
        $_SESSION['mengerjakan'] = 'ya';
        pindah($url_quiz . "handle_soal.php");
    }
    ?>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <script src='<?= $url_quiz . 'script_quiz.js' ?>'></script>

    <script>
    </script>
</body>

</html>