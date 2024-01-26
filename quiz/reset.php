<?php
require './../vendor/autoload.php'; // Impor library Dotenv
require './../proses/global_fungsi.php';
include_once "./../config/setting.php";
include_once "./../config/koneksi.php";
session_destroy();
pindah($url_quiz . "index.php?id=$_GET[id]");
