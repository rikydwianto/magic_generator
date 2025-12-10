<?php
try {


    if (isset($_GET['id'])) {
        $id_cabang = $_GET['id'];


        $query = "DELETE FROM cabang WHERE id_cabang = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id_cabang]);

        echo "Cabang berhasil dihapus.";
        pindah(menu_progress("cabang/index"));
    } else {
        echo "ID Cabang tidak diberikan.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}


$pdo = null;
