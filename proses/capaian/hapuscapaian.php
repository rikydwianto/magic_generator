<?php

try {

    // Ambil ID capaian dari permintaan POST
    $idCapaian = $_GET['id'];

    // Query untuk menghapus data capaian
    $query = "DELETE FROM capaian WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $idCapaian, PDO::PARAM_INT);
    $stmt->execute();

    // Respon berhasil
    echo "Data capaian berhasil dihapus.";
    pindah($url . "index.php?menu=index&act=capaian");
} catch (PDOException $e) {
    // Tangani kesalahan koneksi atau query
    echo "Error: " . $e->getMessage();
} finally {
    // Tutup koneksi
    $pdo = null;
}
