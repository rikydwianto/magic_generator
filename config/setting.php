<?php

use Dotenv\Dotenv;

session_start();
// Load variabel dari file .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
$url = $_ENV['URL'];
// $url = "https://localhost/comdev/";
$url_quiz = $_ENV['URL_KUIS'];
$secretKey = $_ENV['KEY'];
$url_sl = $_ENV['URL_SL'];
$url_api = $url . 'api/';
// $url_quiz = "http://localhost:3000/";