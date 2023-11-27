
<?php 
require 'vendor/autoload.php'; // Impor library Dotenv
require 'proses/global_fungsi.php'; 
include_once "./config/setting.php";
include_once "./config/koneksi.php";
require("vendor/PHPExcel/Classes/PHPExcel.php");



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAGIC GENERATOR</title>
</head>
<body>
    <ul>
        <li><a href="<?=$url?>index.php">HOME</a></li>
        <li><a href="<?=$url?>index.php?menu=cek_par">CEK PAR</a></li>
        <li><a href="<?=$url?>index.php?menu=anal">ANALISA PAR</a></li>
    </ul>
    <?php 
    $sesi = $_SESSION['sesi'];
    if($sesi=='' || $sesi==null){
        $session = time() . "-" . rand(111, 999);
            $session = base64_encode($session);
            $_SESSION['sesi'] = $session;
    }
    @$menu = $_GET['menu'];
    
    if($menu == "cek_par"){
        include("./proses/cek_par.php");
    }
    else if($menu == 'anal'){
       include("./proses/analisis.php");
    }
    else if($menu == 'proses_delin'){
       include("./proses/proses_delin.php");
    }
    else{
        ?>
        <h1>Halaman Awal</h1>
        <?php
    }
    ?>
</body>
</html>