<?php
date_default_timezone_set('Asia/Jakarta');
header("X-XSS-Protection: 1; mode=block");
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

$timestamp = time();


// Mengakses variabel konfigurasi
$dbHost = $_ENV['DB_HOST'];
$dbName = $_ENV['DB_NAME'];
$dbLaporan = $_ENV['DB_NAME_LAPORAN'];
$dbUser = $_ENV['DB_USER'];
$dbPass = $_ENV['DB_PASS'];
$apiKey = $_ENV['API_KEY'];
$port = $_ENV['PORT'];


// Sekarang Anda dapat menggunakan variabel-variabel ini dalam pengaturan Anda

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $pdo_lap = new PDO("mysql:host=$dbHost;dbname=$dbLaporan", $dbUser, $dbPass);
    // Atur mode error untuk menampilkan exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Koneksi berhasil!";
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e);
}