<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <nav id="sidebar" class="col-md-2 col-lg-2 d-md-block sidebar">
            <div class="sidebar-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= $url . "index.php?menu=quiz" ?>">
                            <i class="fas fa-home"></i> Lihat Data
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $url . "index.php?menu=quiz&act=tambah_kuis" ?>">
                            <i class="fas fa-chart-bar"></i> Buat Kuis
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $url . "index.php?menu=quiz&act=soal_bank" ?>">
                            <i class="fas fa-cogs"></i> Bank Soal
                        </a>
                    </li>
                    <!-- Tambahkan item sidebar lainnya sesuai kebutuhan -->

                </ul>
            </div>
        </nav>

        <!-- Konten -->
        <main role="main" class="col-md-10 ml-sm-auto col-lg-10 ">
            <div id="content">

                <?php
                @$sesi = $_SESSION['idLogin'];

                if ($sesi == '' || $sesi == null) {

                    include("./proses/quiz/login.php");
                } else {
                    @$menu = $_GET['act'];

                    if ($menu == "cek_par") {
                        include("./proses/cek_par.php");
                    } else if ($menu == "lihat_jawaban") {
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
                } ?>
            </div>
        </main>

    </div>
</div>