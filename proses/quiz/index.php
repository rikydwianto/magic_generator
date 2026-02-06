<?php

@$sesi = $_SESSION['idLogin'];
if ($sesi == '' || $sesi == null) {

    include("./proses/quiz/login.php");
} else {
    @$menu = $_GET['sub'];

    if ($menu == "lihat_jawaban") {
        include("./proses/quiz/lihat_jawaban.php");
    } else if ($menu == "detail_jawaban") {
        include("./proses/quiz/detail_jawaban.php");
    } else if ($menu == "edit_kuis") {
        include("./proses/quiz/edit_kuis.php");
    } else if ($menu == "tambah_kuis") {
        include("./proses/quiz/tambah_kuis.php");
    } else if ($menu == "copy_quis") {
        include("./proses/quiz/copy_kuis.php");
    } else if ($menu == "hapus_kuis") {
        include("./proses/quiz/hapus_kuis.php");
    } else if ($menu == "soal_bank") {
        include("./proses/bank_soal/index_bank.php");
    } else if ($menu == "kelola_soal") {
        include("./proses/quiz/kelola_soal.php");
    } else if ($menu == "kosongkan") {
        include("./proses/quiz/kosongkan_responden.php");
    } else if ($menu == "lihat_prepost") {
        include("./proses/quiz/lihat_prepost.php");
    } else if ($menu == "edit_aktif") {
        include("./proses/quiz/edit_aktif_kuis.php");
    } else {
        include("./proses/quiz/lihat.php");
    }
}
