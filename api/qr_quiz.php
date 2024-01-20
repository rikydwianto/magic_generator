<?php
require './../vendor/autoload.php'; // Impor library Dotenv
require './../proses/global_fungsi.php';
include_once "./../config/setting.php";
include_once "./../config/koneksi.php";
include "./../vendor/phpqrcode/qrlib.php";


try {

    $stmt = $pdo->prepare("SELECT * FROM kuis WHERE id_kuis = :id");
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->execute();
    // $imageData = explode(",", $stmt->fetchColumn())[1];
    $id = $stmt->fetch();
    if (!empty($id)) {

        $isi = $url_quiz . 'quiz/' . $id['id_kuis'];
        // $isi = 'https://www.malasngoding.com';

        // nama folder tempat penyimpanan file qrcode
        $penyimpanan = "temp/";

        // membuat folder dengan nama "temp"
        if (!file_exists($penyimpanan))
            mkdir($penyimpanan);

        // isi qrcode yang ingin dibuat. akan muncul saat di scan


        // perintah untuk membuat qrcode dan menyimpannya dalam folder temp
        QRcode::png($isi, $penyimpanan . $id['id_kuis'] . ".png", QR_ECLEVEL_L, 13, 5);

        echo '<h1>' . $id['nama_kuis'] . '</h1>';

        // menampilkan qrcode 
        echo '<img src="' . $penyimpanan . $id['id_kuis'] . '.png" >';
    } else {
        echo 'Data gambar tidak ditemukan.';
    }
} catch (PDOException $e) {

    echo 'Terjadi kesalahan database';
}
