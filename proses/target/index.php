<h2>TARGET</h2>
<a href="<?= $url . "index.php?menu=index&act=target" ?>" class="btn btn-primary "><i class="fa fa-home"></i> Awal</a>
<a href="<?= $url . "index.php?menu=index&act=target&submenu=tambahtarget" ?> " class="btn btn-danger "><i class="fa fa-plus"></i> Target</a>
<a href="<?= $url . "index.php?menu=index&act=target&submenu=kegiatan" ?> " class="btn btn-warning text-white "><i class="fa fa-building"></i> Kegiatan</a>

<hr>
<?php
@$submenu = $_GET['submenu'];
$indexPath = $menuPath . $submenu . ".php";
if ($submenu == "") {
    include $menuPath . "lihattarget" . ".php";
} else {
    if (file_exists($indexPath)) {

        include $indexPath;
    } else {

        echo 'Halaman tidak ditemukan';
    }
}

?>