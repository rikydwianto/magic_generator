<?php
require './../vendor/autoload.php'; // Impor library Dotenv
require './../proses/global_fungsi.php';
include_once "./../config/setting.php";
include_once "./../config/koneksi.php";
$url = $url . 'sl/login_staff.php';
session_destroy();
pindah("$url");
