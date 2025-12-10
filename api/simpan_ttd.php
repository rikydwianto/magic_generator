<?php
require './../vendor/autoload.php'; // Impor library Dotenv
require './../proses/global_fungsi.php';
include_once "./../config/setting.php";
include_once "./../config/koneksi.php";
header('Content-Type: application/json');

// Tentukan lokasi folder untuk menyimpan gambar
$uploadFolder = '../assets/img/ttd/';

// Pastikan folder eksis, jika tidak, buat
if (!file_exists($uploadFolder)) {
    mkdir($uploadFolder, 0777, true);
}

// Tangkap data gambar dari POST
$imageData = $_POST['image'];

// Decode base64 dan simpan sebagai file gambar
$data = explode(',', $imageData);
if (count($data) > 1) {
    $encodedData = $data[1];
    $decodedData = base64_decode($encodedData);

    // Tentukan nama unik untuk file
    $uniqueName = uniqid() . '.png';

    // Tentukan path lengkap untuk menyimpan file
    $filePath = $uploadFolder . $uniqueName;

    // Simpan file
    file_put_contents($filePath, $decodedData);

    // Respon JSON
    $response = ['success' => true, 'message' => 'File berhasil diunggah', 'filePath' => $filePath];
} else {
    // Jika data tidak sesuai format yang diharapkan
    $response = ['success' => false, 'message' => 'Data gambar tidak sesuai format yang diharapkan'];
}

echo json_encode($response);
