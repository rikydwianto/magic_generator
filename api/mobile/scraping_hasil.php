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

            if ($persen < 1) {
                $warna = 'bg-dark text-light'; // Hitam untuk 0
            } elseif ($persen >= 1 && $persen <= 30) {
                $warna = 'bg-danger'; // Merah untuk di atas 0 dan kurang atau sama dengan 30
            } elseif ($persen > 30 && $persen < 80) {
                $warna = 'bg-warning'; // Kuning untuk di atas 30 dan kurang dari 80
            } elseif ($persen >= 80) {
                $warna = 'bg-success'; // Hijau untuk 80 atau lebih
            }

            return $warna;
        }

        try {

            $nama_cabang = strtoupper($data['cabang']);
            $cek_cabang = $pdo_lap->prepare("select * from cabang where UPPER(nama_cabang)=? ");
            $cek_cabang->execute([$nama_cabang]);
            $data_cabang = $cek_cabang->fetch(PDO::FETCH_ASSOC);
            if ($cek_cabang->rowCount() > 0) {
                $set_center = 'ya';
            } else {
                $set_center = 'tidak';
            }
        } catch (PDOException $e) {
            echo $e;
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

    <div class="container-fluid mt-3 mb-3 ">
        <?php

                if (isset($_POST['update_center'])) {
                    $id_cabang = $_POST['cabang'];
                    $ctr = $_POST['center'];
                    $bg_asli = $_POST['warna'];
                    $hadir = $_POST['hadir'];
                    $anggota = $_POST['anggota'];
                    $bayar = $_POST['bayar'];
                    for ($i = 0; $i < count($ctr); $i++) {
                        $center = $ctr[$i];
                        if ($bg_asli[$i] == 'bg-success') $bga = 'hijau';
                        else if ($bg_asli[$i] == 'bg-danger') $bga = 'merah';
                        else if ($bg_asli[$i] == 'bg-warning') $bga = 'kuning';
                        else $bga = 'hitam';
                        $q = "UPDATE center SET status_center='$bga', member_center='$anggota[$i]', center_bayar='$bayar[$i]',anggota_hadir='$hadir[$i]' 
                            WHERE id_cabang='$id_cabang' AND no_center='$center'";
                        $update_center = $pdo_lap->query($q);
                        if ($update_center->execute()) {
                            // echo "berhasil";
                        } else {
                            echo "gagal";
                        }
                    }
                    pesan("Berhasil diupdate");
                }
                if (isset($_POST['simpan'])) {
                    $nama = $data['nama'];
                    $cabang = $data['cabang'];
                    $tanggal = $data['tanggal'];
                    $nik = $data['nik'];
                    $detail = json_encode($data['detail']);
                    $q = $pdo_lap->prepare("SELECT * from temp_laporan_dtc where nik=? and cabang=? and tanggal=?");
                    $q->execute([$nik, $cabang, $tanggal]);
                    if ($q->rowCount()) {
                        try {
                            $input = $pdo_lap->prepare("UPDATE temp_laporan_dtc set json_laporan=:detail where nik=:nik and cabang=:cabang and tanggal=:tanggal ");
                            $input->bindParam("nik", $nik);
                            $input->bindParam("tanggal", $tanggal);
                            $input->bindParam("cabang", $cabang);
                            $input->bindParam("detail", $detail);
                            if ($input->execute()) {
                                pesan("Berhasil Disimpan");
                            }
                        } catch (PDOException $e) {
                            echo  $e->getMessage();
                        }
                    } else {
                        try {
                            $input = $pdo_lap->prepare("INSERT INTO temp_laporan_dtc (nik,nama_staff,cabang,tanggal,json_laporan) values(:nik,:nama,:cabang,:tanggal,:json)");
                            $input->bindParam("nik", $nik);
                            $input->bindParam("nama", $nama);
                            $input->bindParam("tanggal", $tanggal);
                            $input->bindParam("cabang", $cabang);
                            $input->bindParam("json", $detail);
                            if ($input->execute()) {
                                pesan("Berhasil Disimpan");
                            }
                        } catch (PDOException $e) {
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

                <form action="" method="post">
                    <input type="hidden" name="cabang" value='<?= $data_cabang['id_cabang'] ?>' id="">
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
                            if (is_numeric($data['detail'][$i]['bayar'])) {
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
                                    $warna_bayar = 'bg-success';
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
                            <td class=''><?= $ket ?>
                                <input type="hidden" name="center[]" value='<?= $ctr ?>' id="">
                                <input type="hidden" name="anggota[]" value='<?= $anggota ?>' id="">
                                <input type="hidden" name="hadir[]" value='<?= $hadir ?>' id="">
                                <input type="hidden" name="bayar[]" value='<?= $bayar ?>' id=""> <br>
                                <input type="hidden" name="warna[]" value='<?= $warna_bayar ?>' id="">
                            </td>
                        </tr>
                        <?php
                            }
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

            <?php
            if ($set_center == 'ya') {
            ?>
            <button type="submit" name='simpan' class='btn btn-success'>SIMPAN LAPORAN</button>
            <button type="submit" name='update_center' class='btn btn-success'>UPDATE CENTER SAJA</button>
            <?php
            }
            ?>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>