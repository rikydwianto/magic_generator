<?php

if (isset($_GET['id_kuis'])) {
    // Ambil id_kuis dari formulir
    $id_kuis = $_GET['id_kuis'];

    try {
        // Mulai transaksi
        $pdo->beginTransaction();

        // Hapus jawaban kuis
        $query_hapus_jawaban_kuis = "DELETE FROM kuis_jawab WHERE id_kuis = :id_kuis";
        $stmt_hapus_jawaban_kuis = $pdo->prepare($query_hapus_jawaban_kuis);
        $stmt_hapus_jawaban_kuis->bindParam(':id_kuis', $id_kuis);
        $stmt_hapus_jawaban_kuis->execute();

        // Hapus jawaban soal
        $query_hapus_jawaban_soal = "DELETE sj FROM soal_jawab sj
                                     INNER JOIN soal s ON sj.id_soal = s.id_soal
                                     WHERE s.id_kuis = :id_kuis";
        $stmt_hapus_jawaban_soal = $pdo->prepare($query_hapus_jawaban_soal);
        $stmt_hapus_jawaban_soal->bindParam(':id_kuis', $id_kuis);
        $stmt_hapus_jawaban_soal->execute();

        // Hapus soal
        $query_hapus_soal = "DELETE FROM soal WHERE id_kuis = :id_kuis";
        $stmt_hapus_soal = $pdo->prepare($query_hapus_soal);
        $stmt_hapus_soal->bindParam(':id_kuis', $id_kuis);
        $stmt_hapus_soal->execute();

        // Hapus kuis
        $query_hapus_kuis = "DELETE FROM kuis WHERE id_kuis = :id_kuis";
        $stmt_hapus_kuis = $pdo->prepare($query_hapus_kuis);
        $stmt_hapus_kuis->bindParam(':id_kuis', $id_kuis);
        $stmt_hapus_kuis->execute();

        // Commit transaksi jika tidak ada kesalahan
        $pdo->commit();

        echo "Kuis beserta soal-soal dan jawabannya berhasil dihapus.";
        pindah($url . "index.php?menu=index&act=quiz");
    } catch (PDOException $e) {
        // Rollback transaksi jika terjadi kesalahan
        $pdo->rollBack();

        echo "Error: " . $e->getMessage();
    }
}
