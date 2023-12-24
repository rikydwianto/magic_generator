<a href="<?= $url . "index.php?menu=quiz&act=soal_bank&submenu=tambah_soal" ?> " class="btn btn-danger"><i
        class="fa fa-plus"></i></a>
<?php
@$menu = $_GET['submenu'];

if ($menu == "tambah_soal") {
    include("./proses/bank_soal/tambah_soal.php");
} else if ($menu == "edit") {
    include("./proses/bank_soal/edit.php");
}  else if ($menu == "del") {
    include("./proses/bank_soal/hapus.php");
} else {
    include("./proses/bank_soal/tampil_bank_soal.php");
}