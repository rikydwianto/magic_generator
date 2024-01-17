<?php
// header('Content-Type: image/png');
require './../vendor/autoload.php';
require './../proses/global_fungsi.php';
include_once "./../config/setting.php";
include_once "./../config/koneksi.php";

try {

    $stmt = $pdo->prepare("SELECT ttd FROM capaian_staff WHERE id_capaian_staff = :id");
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->execute();
    // $imageData = explode(",", $stmt->fetchColumn())[1];
    $imageData = $stmt->fetchColumn();
    if (!empty($imageData)) {
        // echo strlen($imageData);
        // header('Content-Length: ' . strlen($imageData));
        echo "<img src='data:$imageData'/>";


        // echo base64_decode($imageData);
    } else {
        echo 'Data gambar tidak ditemukan.';
    }
} catch (PDOException $e) {

    echo 'Terjadi kesalahan database';
}
