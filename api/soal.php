<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

header('Content-Type: application/json');
require './../vendor/autoload.php'; // Impor library Dotenv
require './../proses/global_fungsi.php';
include_once "./../config/setting.php";
include_once "./../config/koneksi.php";
include "./../vendor/phpqrcode/qrlib.php";


// Mendapatkan data dari sesi
$id_kuis = isset($_POST['id_kuis']) ? intval($_POST['id_kuis']) : 0;
$id_jawab = isset($_POST['id_jawab']) ? $_POST['id_jawab'] : '';
if (isset($_GET['data-soal'])) {
    $sql = "SELECT * FROM kuis WHERE id_kuis = :id_kuis";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_kuis', $id_kuis, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Query untuk mendapatkan informasi kuis
    $sql_kuis = "SELECT * FROM kuis WHERE id_kuis = :id_kuis";
    $stmt_kuis = $pdo->prepare($sql_kuis);
    $stmt_kuis->bindParam(':id_kuis', $id_kuis, PDO::PARAM_INT);
    $stmt_kuis->execute();
    $data_kuis = $stmt_kuis->fetch(PDO::FETCH_ASSOC);

    // Query untuk mendapatkan soal-soal kuis
    $random = $row['acak'] == 'ya' ? "ORDER BY RAND()" : "";

    $sql_soal = "SELECT s.id_soal,s.id_kuis,s.soal,s.pilihan,s.url_gambar,s.id_bank_soal FROM soal s 
            WHERE s.id_soal not in (select sj.id_soal from soal_jawab sj where sj.id_kuis=:id_kuis and sj.id_jawab=:id_jawab) 
            and s.id_kuis=:id_kuis $random limit 1";
    $stmt_soal = $pdo->prepare($sql_soal);
    $stmt_soal->bindParam(':id_kuis', $id_kuis, PDO::PARAM_INT);
    $stmt_soal->bindParam(':id_jawab', $id_jawab, PDO::PARAM_INT);
    $stmt_soal->execute();
    $data_soal = $stmt_soal->fetchAll(PDO::FETCH_ASSOC);

    $hitung_soal = $pdo->query("select count(id_soal) as total_soal from soal where id_kuis=$id_kuis");
    $hitung_soal = $hitung_soal->fetch()['total_soal'];
    $hitung_jawab_soal = $pdo->query("select count(id_jawab) as total_jawab from soal_jawab where id_kuis=$id_kuis and id_jawab='$id_jawab'");
    $hitung_jawab_soal = $hitung_jawab_soal->fetch()['total_jawab'];


    // Menghasilkan respons JSON
    $response = [
        'result' => [
            'data_kuis' => $data_kuis,
            'data_soal' => $data_soal,
            'total_soal' => $hitung_soal,
            'soal_dijawab' => $hitung_jawab_soal
        ]
    ];


    echo json_encode($response);
} elseif (isset($_GET['input-soal'])) {
    $id_jawab = isset($_POST['id_jawab']) ? $_POST['id_jawab'] : '';
    $id_kuis = isset($_POST['id_kuis']) ? $_POST['id_kuis'] : '';
    $id_soal = isset($_POST['id_soal']) ? $_POST['id_soal'] : '';
    $pilihan = isset($_POST['pilihan']) ? $_POST['pilihan'] : '';

    $checkSoalQuery = "SELECT * FROM soal WHERE id_soal = :id_soal";
    $stmtCheckSoal = $pdo->prepare($checkSoalQuery);
    $stmtCheckSoal->bindParam(':id_soal', $id_soal, PDO::PARAM_INT);
    $stmtCheckSoal->execute();
    $data_soal = $stmtCheckSoal->fetch();
    $jawaban = $data_soal['jawaban'];
    $ket =  $jawaban == $pilihan ? "BENAR" : "SALAH";

    $sqlCek = "SELECT * FROM soal_jawab WHERE id_jawab = :id_jawab AND id_kuis = :id_kuis AND id_soal = :id_soal";
    $stmtCek = $pdo->prepare($sqlCek);
    $stmtCek->bindParam(':id_jawab', $id_jawab, PDO::PARAM_INT);
    $stmtCek->bindParam(':id_kuis', $id_kuis, PDO::PARAM_INT);
    $stmtCek->bindParam(':id_soal', $id_soal, PDO::PARAM_INT);
    $stmtCek->execute();
    if ($stmtCek->rowCount() > 0) {
        // Jika data sudah ada, lakukan UPDATE
        $sqlUpdate = "UPDATE soal_jawab SET pilihan = :pilihan , keterangan=:ket WHERE id_jawab = :id_jawab AND id_kuis = :id_kuis AND id_soal = :id_soal";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':id_jawab', $id_jawab, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':id_kuis', $id_kuis, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':id_soal', $id_soal, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':pilihan', $pilihan, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':ket', $ket, PDO::PARAM_STR);

        if ($stmtUpdate->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Data berhasil diupdate.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate data.']);
        }
    } else {

        // Query untuk memasukkan data ke tabel soal_jawab
        $sql = "INSERT INTO soal_jawab (id_jawab, id_kuis, id_soal, pilihan,pilihan_benar,keterangan)
        VALUES (:id_jawab, :id_kuis, :id_soal, :pilihan,:pilihan_benar,:ket)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_jawab', $id_jawab, PDO::PARAM_INT);
        $stmt->bindParam(':id_kuis', $id_kuis, PDO::PARAM_INT);
        $stmt->bindParam(':id_soal', $id_soal, PDO::PARAM_INT);
        $stmt->bindParam(':pilihan', $pilihan, PDO::PARAM_STR);
        $stmt->bindParam(':pilihan_benar', $jawaban, PDO::PARAM_STR);
        $stmt->bindParam(':ket', $ket, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data.']);
        }
    }
} elseif (isset($_GET['update-kuis'])) {
    try {
        // Ambil data dari request POST
        $now = date('Y-m-d H:i:s');

        $sql = "SELECT * FROM kuis_jawab WHERE id_jawab = :id_jawab";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_jawab', $id_jawab, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $waktu_mulai = $row['mulai'];
        // Query untuk mengambil jumlah benar
        $hasil = $pdo->prepare("SELECT
            SUM(CASE WHEN keterangan = 'BENAR' THEN 1 ELSE 0 END) AS jumlah_benar,
            SUM(CASE WHEN keterangan = 'SALAH' THEN 1 ELSE 0 END) AS jumlah_salah
        FROM soal_jawab
        WHERE id_jawab = :id_jawab");
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

        $waktu_pengerjaan = hitungSelisihWaktu($waktu_mulai, $now);

        // Mempersiapkan pernyataan
        $stmt = $pdo->prepare($sql);
        $selesai = $now;

        // Mengikat nilai parameter
        $stmt->bindParam(':benar', $benar, PDO::PARAM_INT);
        $stmt->bindParam(':salah', $salah, PDO::PARAM_INT);
        $stmt->bindParam(':selesai', $selesai, PDO::PARAM_STR);
        $stmt->bindParam(':score', $score, PDO::PARAM_INT);
        $stmt->bindParam(':waktu_pengerjaan', $waktu_pengerjaan, PDO::PARAM_STR);
        $stmt->bindParam(':id_jawab', $id_jawab, PDO::PARAM_INT);

        // Eksekusi query UPDATE
        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'Data berhasil diupdate.']);
    } catch (PDOException $e) {
        // Tangani kesalahan koneksi atau query
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate data.' . $e->getMessage()]);
    }
} elseif (isset($_GET['belum-terjawab'])) {

    $sql_soal = "SELECT s.id_soal,s.id_kuis FROM soal s 
            WHERE s.id_soal not in (select sj.id_soal from soal_jawab sj where sj.id_kuis=:id_kuis and sj.id_jawab=:id_jawab) 
            and s.id_kuis=:id_kuis ";
    $stmt_soal = $pdo->prepare($sql_soal);
    $stmt_soal->bindParam(':id_kuis', $id_kuis, PDO::PARAM_INT);
    $stmt_soal->bindParam(':id_jawab', $id_jawab, PDO::PARAM_INT);
    $stmt_soal->execute();
    $data_soal = $stmt_soal->fetchAll(PDO::FETCH_ASSOC);


    echo json_encode(['data' => $data_soal]);
}
