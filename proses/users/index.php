<?php
if ($_SESSION['jenisAkun'] != 'superuser') {
    pindah($url . "index.php?menu=index");
}
?>
<h2>Users Control</h2>
<a href="<?= $url . "index.php?menu=index&act=users&submenu=tambahuser" ?> " class="btn btn-danger "><i class="fa fa-plus"></i> User</a>

<hr>
<?php
@$submenu = $_GET['submenu'];
$indexPath = $menuPath . $submenu . ".php";
if ($submenu == "") {
    include $menuPath . "lihat_user" . ".php";
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