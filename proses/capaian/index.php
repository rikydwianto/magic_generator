<h2>TARGET</h2>
<a href="<?= $url . "index.php?menu=index&act=capaian" ?>" class="btn btn-primary "><i class="fa fa-home"></i> Awal</a>
<a href="<?= $url . "index.php?menu=index&act=capaian&submenu=tambah_capaian" ?> " class="btn btn-danger "><i class="fa fa-plus"></i> capaian</a>
<a href="<?= $url . "index.php?menu=index&act=capaian&submenu=rekapan" ?> " class="btn btn-success "><i class="fa fa-file-excel"></i> Rekapan</a>

<hr>

<?php
@$submenu = $_GET['submenu'];
$indexPath = $menuPath . $submenu . ".php";
if ($submenu == "") {
    include $menuPath . "lihat_capaian" . ".php";
} else {
    if (file_exists($indexPath)) {

        include $indexPath;
    } else {

        echo 'Halaman tidak ditemukan';
    }
}

?>