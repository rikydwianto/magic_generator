<?php
require './../vendor/autoload.php'; // Impor library Dotenv
require './../proses/global_fungsi.php';
include_once "./../config/setting.php";
include_once "./../config/koneksi.php";
$id_kuis = isset($_SESSION['id_kuis']) ? intval($_SESSION['id_kuis']) : 0;
$id_jawab = $_SESSION['id_kuis_jawab'];


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


$hitung_soal = $pdo->query("select count(id_soal) as total_soal from soal where id_kuis=$id_kuis");
$hitung_soal = $hitung_soal->fetch()['total_soal'];
$hitung_jawab_soal = $pdo->query("select count(id_jawab) as total_jawab from soal_jawab where id_kuis=$id_kuis and id_jawab='$id_jawab'");
$hitung_jawab_soal = $hitung_jawab_soal->fetch()['total_jawab'];

$random = $row['acak'] == 'ya' ? "ORDER BY RAND()" : "";
$query = "SELECT * FROM soal where id_soal not in (select id_soal from soal_jawab where id_kuis=:id_kuis and id_jawab=:id_jawab) and id_kuis=:id_kuis $random limit 1";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':id_kuis', $id_kuis);
$stmt->bindParam(':id_jawab', $id_jawab);
$stmt->execute();

// Fetch hasil query
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$json = $result['pilihan'];
$pilihan = json_decode($json, true);
if ($row['acak'] == 'ya') {
    shuffle($pilihan);
}
if ($hitung_jawab_soal > ($hitung_soal - 1)) {
    pindah($url_quiz . 'lihat_hasil.php');
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

    <style>
    body {
        user-select: none;
    }

    .counter-container {
        /* margin-top: 10px; */
        font-size: 18px;
        user-select: none;

    }

    .list-group-item {
        padding: 12px;
        display: flex;
        align-items: center;
    }

    .form-check-input {
        width: 30px;
        height: 30px;
        margin-left: 5px;
    }

    .form-check-label {
        margin-left: 40px;
        user-select: none;
    }


    /* Optional: Add hover effect on list items */
    .list-group-item:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }

    #questionTitle {
        user-select: none;
    }
    </style>
</head>

<body>

    <div class="container-fluid mt-5">
        <form action="" method="post" id="quizForm">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Soal ke <?= $hitung_jawab_soal + 1 ?> dari <?= $hitung_soal ?>
                            </h4>

                        </div>
                        <div class="card-body">
                            <div id="questionContainer" class="question-container">
                                <input type="hidden" name="id_soal" id="" value="<?= $result['id_soal'] ?>">
                                <input type="hidden" name="id_kuis" id="" value="<?= $result['id_kuis'] ?>">
                                <div id="questionTitle" class="question-title">
                                    <h4><?= $result['soal'] ?></h4>
                                </div>
                                <div id="optionsContainer" class="options-container">
                                    <h6>Pilihan</h6>
                                    <ul class="list-group">
                                        <?php
                                        foreach ($pilihan as $pilihan) {
                                        ?>
                                        <li class="list-group-item" for="pilihan<?= $pilihan['id'] ?>">
                                            <input class="form-check-input me-1" type="radio" name="pilihan"
                                                value="<?= $pilihan['id'] ?>" id="pilihan<?= $pilihan['id'] ?>">
                                            <label class="form-check-label" for="pilihan<?= $pilihan['id'] ?>"
                                                name='pilihan'>
                                                <?= $row['acak'] == 'ya' ? "" : $pilihan['id'] . '. ' ?><?= $pilihan['teks'] ?></label>
                                        </li>
                                        <?php
                                        }
                                        ?>

                                    </ul>
                                </div>
                            </div>
                            <?php
                            if ($hitung_jawab_soal >= ($hitung_soal - 1)) {
                            ?>
                            <input type="hidden" name="kirim_jawaban" value='ya' id="">
                            <button id="nextBtn" type="button" onclick="validateForm()"
                                class="btn btn-primary mt-3">KIRIM DAN LIHAT HASIL</button>
                            <?php
                            } else {
                            ?>
                            <button id="nextBtn" type="button" onclick="validateForm()"
                                class="btn btn-primary mt-3">Jawab</button>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
    if (isset($_POST['id_soal']) && isset($_POST['pilihan'])) {
        try {
            $idSoal = $_POST['id_soal'];
            $idKuis = $_POST['id_kuis'];
            $pilihanUser = $_POST['pilihan']; // Pastikan ini benar-benar datang dari formulir Anda
            $now = date('Y-m-d H:i:s');
            // Query untuk memeriksa keberadaan soal berdasarkan id_soal
            $checkSoalQuery = "SELECT * FROM soal WHERE id_soal = :id_soal";
            $stmtCheckSoal = $pdo->prepare($checkSoalQuery);
            $stmtCheckSoal->bindParam(':id_soal', $idSoal, PDO::PARAM_INT);
            $stmtCheckSoal->execute();

            // Memeriksa apakah soal dengan id_soal tersebut ada
            if ($stmtCheckSoal->rowCount() > 0) {
                $data_soal = $stmtCheckSoal->fetch();
                $jawaban = $data_soal['jawaban'];
                $ket =  $jawaban == $pilihanUser ? "BENAR" : "SALAH";

                // Soal ditemukan, lanjutkan untuk menyimpan jawaban
                // Query untuk menyisipkan jawaban ke dalam tabel jawaban
                $insertJawabanQuery = "INSERT INTO soal_jawab (id_soal, id_kuis, pilihan,id_jawab,pilihan_benar,keterangan)
                 VALUES (:id_soal, :id_kuis, :pilihan_user,:id_jawab,:jawaban,:keterangan)";
                $stmtInsertJawaban = $pdo->prepare($insertJawabanQuery);
                $stmtInsertJawaban->bindParam(':id_soal', $idSoal, PDO::PARAM_INT);
                $stmtInsertJawaban->bindParam(':id_kuis', $idKuis, PDO::PARAM_INT);
                $stmtInsertJawaban->bindParam(':pilihan_user', $pilihanUser, PDO::PARAM_STR);
                $stmtInsertJawaban->bindParam(':id_jawab', $id_jawab, PDO::PARAM_STR);
                $stmtInsertJawaban->bindParam(':jawaban', $jawaban, PDO::PARAM_STR);
                $stmtInsertJawaban->bindParam(':keterangan', $ket, PDO::PARAM_STR);

                // Eksekusi query untuk menyisipkan jawaban
                $stmtInsertJawaban->execute();

                if (isset($_POST['kirim_jawaban'])) {



                    try {
                        // Query untuk mengambil jumlah benar
                        $hasil = $pdo->prepare("SELECT
                        SUM(CASE WHEN keterangan = 'BENAR' THEN 1 ELSE 0 END) AS jumlah_benar,
                        SUM(CASE WHEN keterangan = 'SALAH' THEN 1 ELSE 0 END) AS jumlah_salah
                      FROM soal_jawab
                      WHERE id_jawab = :id_jawab;
                      ");
                        $hasil->bindParam(':id_jawab', $id_jawab, PDO::PARAM_INT);
                        $hasil->execute();
                        $hasil = $hasil->fetch();
                        $benar =  $hasil['jumlah_benar'];
                        $salah =  $hasil['jumlah_salah'];

                        $score = $benar / ($benar + $salah) * 100;
                        // Query UPDATE untuk memperbarui tabel kuis_jawab
                        $sql = "UPDATE kuis_jawab kj
                                SET
                                benar = :benar,
                                salah = :salah,
                                selesai = :selesai,
                                keterangan = 'selesai',
                                pengerjaan = :waktu_pengerjaan,
                                total_score = :score
                                WHERE kj.id_jawab = :id_jawab";


                        $waktu_pengerjaan = hitungSelisihWaktu($_SESSION['waktu_mulai'], $now);
                        // Mempersiapkan pernyataan
                        $stmt = $pdo->prepare($sql);
                        $selesai = $now;
                        // Mengikat nilai parameter
                        $stmt->bindParam(':benar', $benar, PDO::PARAM_INT);
                        $stmt->bindParam(':salah', $salah, PDO::PARAM_INT);
                        $stmt->bindParam(':selesai', $selesai, PDO::PARAM_STR); // assuming $selesai is a string representing the date
                        $stmt->bindParam(':score', $score, PDO::PARAM_INT);
                        $stmt->bindParam(':waktu_pengerjaan', $waktu_pengerjaan, PDO::PARAM_STR);
                        $stmt->bindParam(':id_jawab', $id_jawab, PDO::PARAM_INT);

                        // Eksekusi query UPDATE
                        $stmt->execute();

                        // echo "Update berhasil dilakukan.";
                    } catch (PDOException $e) {
                        // Tangani kesalahan koneksi atau query
                        echo "Error: " . $e->getMessage();
                    }


                    pindah($url_quiz . "lihat_hasil.php");
                } else {
                    pindah($url_quiz . "soal.php");
                }
            } else {
                // Soal tidak ditemukan
                // echo "Soal dengan ID $idSoal tidak ditemukan.";
            }
        } catch (PDOException $e) {
            // Tangani kesalahan koneksi atau query
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
    <script>
    function confirmAndSubmit() {
        // Munculkan konfirmasi
        Swal.fire({
            title: 'Yakin dengan jawaban ini?',
            text: 'Anda tidak akan dapat melihat jawaban sebelumnya setelah mengirim.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, kirim jawaban',
            cancelButtonText: 'Batal',
        }).then((result) => {
            // Jika pengguna mengonfirmasi, submit formulir
            if (result.isConfirmed) {
                document.getElementById('quizForm').submit();
            } else {
                Swal.fire({
                    title: 'Informasi',
                    text: 'Silahkan Pikirkan kembali jawaban nya sebelum dikirim.',
                    icon: 'warning',
                })
            }
        });
    }

    function validateForm() {
        var selectedOption = document.querySelector('input[name="pilihan"]:checked');
        if (!selectedOption) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Silakan pilih salah satu opsi!',
            });
        } else {
            // Uncomment the line below if you want to submit the form
            confirmAndSubmit();
        }
    }
    </script>
</body>

</html>