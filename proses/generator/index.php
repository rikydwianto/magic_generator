<h2>GENERATOR</h2>
<a href="<?= $url . "index.php?menu=index&act=generator" ?> " class="btn btn-primary "><i class="fa fa-home"></i>
    Awal</a>
<a href="<?= $url . "index.php?menu=index&act=generator&submenu=runquery" ?> " class="btn btn-danger "><i
        class="fa fa-play"></i> Query</a>
<a href="<?= $url . "index.php?menu=index&act=generator&submenu=backup" ?> " class="btn btn-success "><i
        class="fa fa-download"></i> Backup All</a>

<?php
@$submenu = $_GET['submenu'];
$indexPath = $menuPath . $submenu . ".php";
if ($submenu == "") {
    include $menuPath . "showtable" . ".php";
} else {
    if (file_exists($indexPath)) {
        // File index.php ditemukan, lakukan inclusion
        include $indexPath;
    } else {
        // File index.php tidak ditemukan, tampilkan pesan 404
        echo 'Halaman tidak ditemukan';
    }
}

?>