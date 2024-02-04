<?php
require './../vendor/autoload.php'; // Impor library Dotenv
require './../proses/global_fungsi.php';
include_once "./../config/setting.php";
include_once "./../config/koneksi.php";
$url_asli = $url;
$url = $url_sl; // . 'sl/';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progres Mingguan Staff</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="<?= $url . 'style.css' ?>" />


</head>

<body>
    <?php
    // Periksa apakah session ID sudah ditetapkan
    if (!isset($_SESSION['id_staff']) || $_SESSION['id_staff'] == "") {
        // Jika tidak, redirect ke halaman login
        pindah($url . "login_staff.php");
    }

    $menuItems = [
        ['text' => 'Dashboard', 'icon' => 'fa fa-home', 'url' => menu_sl("index"), 'active' => true],
        ['text' => 'Laporan Mingguan', 'icon' => 'fa fa-file-excel', 'url' => menu_sl("laporan/index"), 'active' => true],
        ['text' => 'Laporan Mingguan', 'icon' => 'fa fa-plus', 'url' => menu_sl("laporan/tambah"), 'active' => true],
        ['text' => 'Logout', 'icon' => 'fa fa-arrow-left', 'url' => $url . 'logout.php', 'active' => false],
        // Tambahkan item menu lain jika diperlukan
    ];


    ?>
    <?php include "./layout/navbar.php" ?>
    <div class="sidebar offcanvas  offcanvas-end" tabindex="-1" id="sidebar">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasExampleLabel">MENU</h5>
            <button type="button" class="btn-close " data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body">
            <ul class="nav flex-column">
                <?php foreach ($menuItems as $menuItem) : ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $menuItem['url'] === menu_sl(@$_GET['menu']) ? 'active' : ''; ?>"
                        href="<?php echo $menuItem['url']; ?>">
                        <?php if (!empty($menuItem['icon'])) : ?>
                        <i class="<?php echo $menuItem['icon']; ?>"></i>
                        <?php endif; ?>
                        <?php echo $menuItem['text']; ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Main content -->

            <div class="col-md-4 col-lg-2  ms-sm-auto   px-md-4 main-content" id='samping'>
                <h1>Menu</h1>
                <div class="wrapper">

                    <nav id="sidebar">
                        <ul class="nav flex-column">
                            <?php foreach ($menuItems as $menuItem) : ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $menuItem['url'] === menu_sl(@$_GET['menu']) ? 'active' : ''; ?>"
                                    href="<?php echo $menuItem['url']; ?>">
                                    <?php if (!empty($menuItem['icon'])) : ?>
                                    <i class="<?php echo $menuItem['icon']; ?>"></i>
                                    <?php endif; ?>
                                    <?php echo $menuItem['text']; ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </nav>

                </div>

            </div>
            <div class="col-md-8 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Your main content goes here -->
                <?php
                $id_staff =  $_SESSION['id_staff'];
                $nik_staff =  $_SESSION['nik_staff'];
                $stmt = $pdo->prepare("SELECT *
                 FROM staff
                 join cabang on cabang.nama_cabang=staff.cabang
                 WHERE id_staff = ?;
                 ");
                $stmt->execute([$id_staff]);
                $detailAkun = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($detailAkun) {
                    $cabang = $detailAkun['cabang'];
                } else {
                    pindah($url . "login_staff.php");
                }
                $menuPath = "./proses/";
                @$menu = $_GET['menu'];
                $indexPath = $menuPath . $menu . ".php";
                $pass = $detailAkun['password'];
                if ($pass == '123456' || $pass == '1sampai9') {
                    include "./../sl/proses/user/ganti_password.php";
                } else {
                    if ($menu == "" || $menu == "index") {
                        include $menuPath . "index" . ".php";
                    } else {
                        if (file_exists($indexPath)) {
                            // File index.php ditemukan, lakukan inclusion
                            include $indexPath;
                        } else {
                            // File index.php tidak ditemukan, tampilkan pesan 404
                            echo 'Halaman tidak ditemukan';
                        }
                    }
                }


                ?>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer mt-5">
        &copy; <?= date("Y") ?> Community Development | Riky Dwianto
    </div>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js">
    </script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <!-- Include JSignature library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jSignature/2.1.2/jSignature.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>

    <script src="<?= $url . "script.js" ?>"></script>
    <script>
    var url = "<?= $url ?>";
    // Tambahkan script berikut untuk menangani toggle sidebar pada mode mobile
    $(document).ready(function() {
        $('[data-bs-toggle="collapse"]').on('click', function() {
            $('#sidebar').toggle();
        });
    });


    $(function() {
        $("#start_date, #end_date").datepicker({
            dateFormat: "yy-mm-dd",
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true,
            onClose: function(selectedDate) {
                var option = this.id == "start_date" ? "minDate" : "maxDate",
                    instance = $(this).data("datepicker"),
                    date = $.datepicker.parseDate(
                        instance.settings.dateFormat ||
                        $.datepicker._defaults.dateFormat,
                        selectedDate, instance.settings);

                $("#start_date, #end_date").not(this).datepicker("option", option, date);
            }
        });
    });
    </script>

</body>

</html>
<?php $pdo = null ?>