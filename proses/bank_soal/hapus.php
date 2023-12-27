<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id_soal'])) {
    $soalId = $_GET['id_soal'];

    try {
        $query = "DELETE FROM soal_bank WHERE id_soal = ?";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(1, $soalId);
        $stmt->execute();

        alert("berhasil dihapus");
        pindah($url . "index.php?menu=index&act=bank_soal");
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    echo "Invalid Request.";
}
