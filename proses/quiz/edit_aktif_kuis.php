<?php
// Ambil data dari formulir
$id_kuis = $_GET['id_kuis'];
$status_baru = $_GET['ket'];

// Update status kuis di database
$query = "UPDATE kuis SET status = '$status_baru' WHERE id_kuis = $id_kuis";

if ($pdo->query($query)) {
    pindah($url . "index.php?menu=quiz");
} else {
    echo "Error: ";
}
