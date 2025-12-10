<?php
require './../vendor/autoload.php'; // Impor library Dotenv
require './../proses/global_fungsi.php';
include_once "./../config/setting.php";
include_once "./../config/koneksi.php";
// Mengambil data dari body permintaan POST
$data = json_decode(file_get_contents('php://input'), true);

// Mengecek apakah data yang dibutuhkan ada
if (empty($data['id_soal'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Data tidak lengkap']);
    exit;
}

// Menyambungkan ke database

// TODO: Lakukan validasi atau pengecekan izin yang sesuai dengan kebutuhan aplikasi Anda

// TODO: Lakukan operasi hapus soal dari kuis (Contoh: menghapus data dari tabel kuis_soal)
try {
    $stmt = $pdo->prepare("DELETE FROM soal WHERE id_bank_soal = :id_soal and id_kuis = :id_kuis");
    $stmt->bindParam(':id_soal', $data['id_soal']);
    $stmt->bindParam(':id_kuis', $data['id_kuis']);
    $stmt->execute();

    // Memberikan respons ke klien
    http_response_code(200);
    echo json_encode(['message' => 'Soal berhasil dihapus dari kuis']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Gagal menghapus soal dari kuis: ' . $e->getMessage()]);
}
