<?php

use Dotenv\Dotenv;

session_start();
// Load variabel dari file .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
$sesi_ttl = 3 * 60 * 60; // 3 jam
$now_ts = time();
if (
    empty($_SESSION['sesi']) ||
    empty($_SESSION['sesi_issued_at']) ||
    ($now_ts - (int)$_SESSION['sesi_issued_at']) >= $sesi_ttl
) {
    try {
        $random = bin2hex(random_bytes(16));
    } catch (Exception $e) {
        $random = bin2hex(openssl_random_pseudo_bytes(16));
    }
    $_SESSION['sesi'] = $now_ts . '-' . $random;
    $_SESSION['sesi_issued_at'] = $now_ts;
}
$url = $_ENV['URL'];
// $url = "https://localhost/comdev/";
$url_quiz = $_ENV['URL_KUIS'];
$secretKey = $_ENV['KEY'];
$url_sl = $_ENV['URL_SL'];
$url_api = $url . 'api/';
// $url_quiz = "http://localhost:3000/";
