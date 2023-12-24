<?php

use Dotenv\Dotenv;

session_start();
// Load variabel dari file .env
$dotenv = Dotenv::createImmutable(__DIR__ . "'/../");
$dotenv->load();
$url = $_ENV['URL'];
// $url = "https://localhost/comdev/";
// $url_quiz = "https://comdev.my.id/";
$url_quiz = "http://localhost:3000/";
