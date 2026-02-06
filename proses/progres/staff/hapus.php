<?php

try {
    if (isset($_GET['id_staff'])) {

        $id_staff = $_GET['id_staff'];


        $query = "DELETE FROM staff WHERE id_staff = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id_staff]);

        $pesan = "Data Staff berhasil dihapus.";
        pindah(menu_progress("staff/index"));
    } else {
        $pesan = "ID Staff tidak valid.";
    }
} catch (PDOException $e) {
    $pesan = "Error: " . $e->getMessage();
}


$pdo = null;
