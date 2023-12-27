<?php
// Fungsi untuk menghapus data dari tabel kuis_jawab
function deleteKuisJawab($idKuis, $pdo)
{
    $deleteKuisJawabQuery = "DELETE FROM kuis_jawab WHERE id_kuis = ?";
    $deleteKuisJawabStmt = $pdo->prepare($deleteKuisJawabQuery);

    $deleteKuisJawabStmt->execute([$idKuis]);

    return $deleteKuisJawabStmt->rowCount();
}

// Fungsi untuk menghapus data dari tabel soal_jawab
function deleteSoalJawab($idKuis, $pdo)
{
    $deleteSoalJawabQuery = "DELETE FROM soal_jawab WHERE id_kuis = ?";
    $deleteSoalJawabStmt = $pdo->prepare($deleteSoalJawabQuery);

    $deleteSoalJawabStmt->execute([$idKuis]);

    return $deleteSoalJawabStmt->rowCount();
}

// Fungsi untuk menghapus kuis dan jawaban terkait
function deleteKuisDanJawaban($idKuis, $pdo)
{
    $pdo->beginTransaction(); // Mulai transaksi

    try {
        // Hapus data dari tabel soal_jawab
        $deleteSoalJawabResult = deleteSoalJawab($idKuis, $pdo);

        // Hapus data dari tabel kuis_jawab
        $deleteKuisJawabResult = deleteKuisJawab($idKuis, $pdo);

        // Commit transaksi jika kedua operasi berhasil
        $pdo->commit();
        echo "Data berhasil dihapus.";
    } catch (PDOException $e) {
        // Rollback transaksi jika ada kesalahan
        $pdo->rollBack();
        echo "Gagal menghapus data. Error: " . $e->getMessage();
    }
}

// Gunakan fungsi untuk menghapus data berdasarkan id_kuis tertentu
$idKuisToDelete = $_GET['id_kuis']; // Ganti dengan id_kuis yang sesuai
deleteKuisDanJawaban($idKuisToDelete, $pdo);
pindah($url . "index.php?menu=index&act=quiz");
