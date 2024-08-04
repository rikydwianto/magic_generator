<?php
require './../../vendor/autoload.php';
require './../../proses/global_fungsi.php';
include_once "./../../config/setting.php";
include_once "./../../config/koneksi.php";
require_once __DIR__ . '/proses/proses.php';
if (isset($_GET['akses']) && $_GET['akses'] == 'android') {
    if (isset($_GET['key']) && $_GET['key'] == $secretKey) {
        $url_scan = $_GET['url'];
        $json = json_decode(scrappingBarcode($pdo, $url_scan), true);


        if ($json['status'] != 'error') {
        } else {
            echo "<h2>Data tidak ditemukan!</h2>";
            exit;
        }
        $data = $json['data'];

        function warna($persen)
        {
            if ($persen > 0 && $persen < 30) {
                $warna = 'bg-danger';
            } elseif ($persen >= 30 && $persen < 80) {
                $warna = 'bg-warning';
            } elseif ($persen >= 80) {
                $warna = 'bg-success';
            } else {
                $warna = 'bg-dark text-light'; // Ganti 'warna-default' dengan warna default yang Anda inginkan
            }
            return $warna;
        }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DTC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .scroll-box {
        max-height: 500px;
        overflow-y: scroll;
        border: 1px solid #ccc;
        padding: 10px;
    }
    </style>
</head>

<body>

    <div class="container-fluid mt-3 ">
        <h1 class='text-center'>DATA TRANSAKSI CENTER</h1>
        <p></p>
        <hr>
        <div class="col">
            <table class="table table-bordered">
                <tr>
                    <th>CABANG</th>
                    <th><?= $data['cabang'] ?></th>
                </tr>
                <tr>
                    <th>TGL TRANSAKSI</th>
                    <th><?= haritanggal($data['tanggal']) ?></th>
                </tr>
                <tr>
                    <th>NIK</th>
                    <th><?= $data['nik'] ?></th>
                </tr>
                <tr>
                    <th>NAMA</th>
                    <th><?= $data['nama'] ?></th>
                </tr>

            </table>
            <div class="scroll-box">
                <table class="table table-hovered table-bordered">
                    <tr>
                        <th>NO</th>
                        <th>CTR</th>
                        <th>AGT</th>
                        <th>BAYAR</th>
                        <th>TIDAK BAYAR</th>
                        <th>HADIR</th>
                        <th>TIDAK HADIR</th>
                        <th>KETERANGAN</th>
                    </tr>
                    <?php
                            $no = 1;
                            $total_bayar = 0;
                            $total_center = 0;
                            $total_anggota = 0;
                            $total_hadir = 0;
                            $total_bayar = 0;
                            $total_tidak_bayar = 0;
                            $total_pencairan = 0;
                            $total_dnr = 0;
                            $total_drop_masuk = 0;
                            $total_drop_keluar = 0;
                            $total_angsuran = 0;
                            $total_simpanan_masuk = 0;
                            $total_simpanan_keluar = 0;
                            $total_jumlah_pengambil_simpanan = 0;
                            $total_jumlah_anggota_keluar = 0;
                            $total_total_pendapatan = 0;

                            $total_center_lancar = 0;
                            for ($i = 0; $i < count($data['detail']); $i++) {
                                $detail = $data['detail'][$i];
                                $ctr = $detail['center'];
                                $anggota = $detail['anggota'];
                                $hadir = $detail['hadir'];
                                $bayar = $detail['bayar'];
                                $tidak_hadir =   $anggota - $hadir;
                                $tidak_bayar = $detail['tidak_bayar'];
                                $pencairan = $detail['pencairan'];


                                //init
                                $total_center += $detail['center'];
                                $total_anggota += $detail['anggota'];
                                $total_hadir += $detail['hadir'];
                                $total_bayar += $detail['bayar'];
                                $total_tidak_bayar += $detail['tidak_bayar'];


                                //end init

                                $persen_bayar = ($bayar / $anggota) * 100;
                                $persen_hadir = ($hadir / $anggota) * 100;

                                $ket = '';
                                $bg = '';
                                if ($anggota == $bayar && $anggota == $hadir) {
                                    $ket = "100% Lancar";
                                    $bg = 'bg-success';
                                    $total_center_lancar++;
                                } else {



                                    $warna_bayar = warna($persen_bayar);

                                    $warna_hadir = warna($persen_hadir);

                                    // $ket = "$persen_bayar% $persen_hadir%";
                                    if ($bayar == 0) {
                                        $ket = "tidak ada angsuran masuk";
                                        if ($persen_hadir > 50 && $pencairan > 0) {
                                            $ket = "  Kemungkinan Ctr Baru";
                                        } else {
                                            $ket .= " namun ada absen";
                                        }
                                    }
                                }

                            ?>
                    <tr class='<?= $bg ?>'>
                        <td><?= $no++ ?></td>
                        <td><?= $ctr ?></td>
                        <td class='text-center'><?= $anggota ?></td>
                        <td class='text-center <?= $warna_bayar ?>'><?= $bayar ?></td>
                        <td class='text-center <?= $warna_bayar ?>'><?= $tidak_bayar ?></td>
                        <td class='text-center <?= $warna_hadir ?>'><?= $hadir ?></td>
                        <td class='text-center <?= $warna_hadir ?>'><?= $tidak_hadir ?></td>
                        <td class=''><?= $ket ?></td>
                    </tr>
                    <?php
                            }
                            ?>
                    <tr class=''>
                        <td colspan="2">TOTAL</td>

                        <td class='text-center'><?= $total_anggota ?></td>
                        <td class='text-center '><?= $total_bayar ?></td>
                        <td class='text-center '><?= $total_tidak_bayar ?></td>
                        <td class='text-center '><?= $total_hadir ?></td>
                        <td class='text-center '><?= $total_anggota - $total_hadir ?></td>
                        <td class=''></td>
                    </tr>
                </table>
            </div>
            kesimpulan
            <table class="table">
                <tr>
                    <th>TOTAL CENTER</th>
                    <th><?= $total_center = $no - 1 ?></th>
                    <th></th>
                </tr>
                <tr>
                    <th>TOTAL ANGGOTA</th>
                    <th><?= $total_anggota ?></th>
                    <th></th>
                </tr>
                <tr>
                    <th>TOTAL ANGSURAN MASUK</th>
                    <th><?= $total_bayar ?></th>
                    <th><?= round(($total_bayar / $total_anggota) * 100, 1) ?>%</th>
                </tr>
                <tr>
                    <th>TOTAL CENTER 100%</th>
                    <th><?= $total_center_lancar ?></th>
                    <th><?= round(($total_center_lancar / $total_center) * 100, 2) ?>%</th>
                </tr>
                <tr>
                    <th>TOTAL KEHADIRAN</th>
                    <th><?= $total_hadir ?></th>
                    <th><?= round(($total_hadir / $total_anggota) * 100, 2) ?>%</th>
                </tr>

            </table>
            <form action="" method="post">
                <button type="submit" name='simpan' class='btn btn-success'>SIMPAN LAPORAN</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
if(isset($_POST['simpan'])){
    $nama = $data['nama'];
    $cabang = $data['cabang'];
    $tanggal = $data['tanggal'];
    $nik = $data['nik'];
    $detail =json_encode($data['detail']);
   $q = $pdo_lap->prepare("SELECT * from temp_laporan_dtc where nik=? and cabang=? and tanggal=?");
   $q->execute([$nik,$cabang,$tanggal]);
   if($q->rowCount()){
    try{
        $input = $pdo_lap->prepare("UPDATE temp_laporan_dtc set json_laporan=:detail where nik=:nik and cabang=:cabang and tanggal=:tanggal ");
        $input->bindParam("nik",$nik);
        $input->bindParam("tanggal",$tanggal);
        $input->bindParam("cabang",$cabang);
        $input->bindParam("detail",$detail);
        if($input->execute()){
            alert(  "berhasil disimpan");
        }
       

    }
    catch(PDOExeption $e){
        echo  $e->getMessage();
    }
   }
   else{
    try{
        $input = $pdo_lap->prepare("INSERT INTO temp_laporan_dtc (nik,nama_staff,cabang,tanggal,json_laporan) values(:nik,:nama,:cabang,:tanggal,:json)");
        $input->bindParam("nik",$nik);
        $input->bindParam("nama",$nama);
        $input->bindParam("tanggal",$tanggal);
        $input->bindParam("cabang",$cabang);
        $input->bindParam("json",$detail);
        if($input->execute()){
            alert(  "berhasil disimpan");
        }
       

    }
    catch(PDOExeption $e){
        echo  $e->getMessage();
    }
   
   }
}
?>
<?php
    } else {
        exit;
    }
}
?>