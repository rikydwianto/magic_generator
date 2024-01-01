<div class="container-fluid table-responsive ">
    <div class="row">

        <!-- Sidebar -->
        <?php
        @$sesi = $_SESSION['idLogin'];
        @$superuser = $_SESSION['jenisAkun'];
        if (!$sesi == '' || !$sesi == null) {

            include_once("./proses/layout/navbar.php");
        }
        ?>

        <!-- Konten -->
        <main role="main" class="col-md-10 ml-sm-auto col-lg-10 ">
            <div id="content">

                <?php


                if ($sesi == '' || $sesi == null) {

                    include("./proses/view/login.php");
                } else {

                    @$menu = $_GET['act'];

                    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                    $stmt->execute([$sesi]);
                    $detailAkun = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($detailAkun) {
                    } else {
                        pindah($url);
                    }
                    if ($menu == "") {
                        include "./proses/view/awal.php";
                    } else {
                        // Path menu
                        $menuPath = "./proses/$menu/";
                        // Memeriksa apakah folder ada
                        if (is_dir($menuPath)) {
                            // Memeriksa keberadaan file index.php
                            $indexPath = $menuPath . 'index.php';
                            if (file_exists($indexPath)) {
                                // File index.php ditemukan, lakukan inclusion
                                include $indexPath;
                            } else {
                                // File index.php tidak ditemukan, tampilkan pesan 404
                                echo 'Halaman tidak ditemukan';
                            }
                        } else {
                            // Folder tidak ditemukan, tampilkan pesan 404
                            // http_response_code(404);
                            include "./proses/view/awal.php";
                        }
                    }
                } ?>
            </div>
        </main>

    </div>
</div>