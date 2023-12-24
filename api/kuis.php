<?php
require './../vendor/autoload.php'; // Impor library Dotenv
require './../proses/global_fungsi.php';
include_once "./../config/setting.php";
include_once "./../config/koneksi.php";
// Mengambil data dari body permintaan POST
$data = json_decode(file_get_contents('php://input'), true);

// Mengecek apakah data yang dibutuhkan ada
if (empty($data['id_kuis']) || empty($data['id_soal'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Data tidak lengkap']);
    exit;
}
try {
    $stmt = $pdo->prepare("INSERT INTO soal (soal, pilihan, jawaban, id_kuis, id_bank_soal)
    SELECT soal, pilihan, jawaban, :id_kuis, id_soal
    FROM soal_bank
    WHERE id_soal = :id_soal;
    ");
    $stmt->bindParam(':id_kuis', $data['id_kuis']);
    $stmt->bindParam(':id_soal', $data['id_soal']);
    $stmt->execute();

    // Memberikan respons ke klien
    http_response_code(200);
    echo json_encode(['message' => 'Soal berhasil ditambahkan ke kuis']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Gagal menambahkan soal ke kuis: ' . $e->getMessage()]);
}
