<?php
require './../vendor/autoload.php'; // Impor library Dotenv
require './../proses/global_fungsi.php';
include_once "./../config/setting.php";
include_once "./../config/koneksi.php";
unset($_SESSION['unique_id']);
unset($_SESSION['id_kuis']);
unset($_SESSION['id_kuis_jawab']);
pindah($url_quiz . "index.php?id=$_GET[id]");
