<?php


require './../../vendor/autoload.php';
require './../../proses/global_fungsi.php';
include_once "./../../config/setting.php";
include_once "./../../config/koneksi.php";
if (isset($_GET['akses']) && $_GET['akses'] == 'android') {

    var_dump($_GET);
    exit;
?>
    <h2>Submit Laporan Ke Regional</h2>
    <hr>
    <?php
    $minggu = isset($_GET['minggu']) ? $_GET['minggu'] : '';
    $bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
    $tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';
    $cabang = isset($_GET['cabang']) ? $_GET['cabang'] : $detailAkun['nama_cabang'];

    $stmtCheck = $pdo->prepare("SELECT id,status,keterangan FROM capaian_cabang WHERE minggu = ? AND bulan = ? AND tahun = ?  and nama_cabang=?");
    $stmtCheck->execute([$minggu, $bulan, $tahun, $cabang]);
    $existingData = $stmtCheck->fetch(PDO::FETCH_ASSOC);
    $edit = '';
    if ($existingData) {
        if ($existingData['status'] == 'done') {
            $pesan = urlencode("Laporan Cabang $cabang <strong>minggu-$minggu Bulan-$bulanArray[$bulan] tahun $tahun </strong>
        Sudah dibuat!, Terima Kasih ");
            // pindah(menu_progress("laporan/submit&error&minggu=$minggu&bulan=$bulan&tahun=$tahun&pesan=$pesan"));
        } else {
            $edit = 'ya';
            $id_laporan_cabang = $existingData['id'];
        }
    }
    $query = "SELECT  COUNT(*) as jumlah_staff FROM staff where cabang='$cabang' and status='aktif'";

    $hit_ = $pdo->prepare($query);
    $hit_->execute();

    // Mengambil hasil query
    $jml_staff = $hit_->fetch()['jumlah_staff'];


    $query = "
    SELECT
        cs.cabang_staff,
        cs.status,
        COUNT(cs.nik_staff) AS jumlah_staff,
        SUM(dcs.anggota_masuk) AS total_anggota_masuk,
        SUM(dcs.anggota_keluar) AS total_anggota_keluar,
        SUM(dcs.nett_anggota) AS total_nett_anggota,
        SUM(dcs.naik_par) AS total_naik_par,
        SUM(dcs.turun_par) AS total_turun_par,
        SUM(dcs.nett_par) AS total_nett_par,
        SUM(dcs.pemb_lain) AS total_pemb_lain,
        SUM(dcs.agt_tpk) AS total_agt_tpk,
        SUM(dcs.agt_cuti) AS total_agt_cuti,
        SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(dcs.json_pinjaman, '$.PMB')) AS INT)) AS total_PMB,
        SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(dcs.json_pinjaman, '$.PSA')) AS INT)) AS total_PSA,
        SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(dcs.json_pinjaman, '$.PPD')) AS INT)) AS total_PPD,
        SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(dcs.json_pinjaman, '$.PRR')) AS INT)) AS total_PRR,
        SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(dcs.json_pinjaman, '$.ARTA')) AS INT)) AS total_ARTA
    FROM
        capaian_staff cs
    JOIN
        detail_capaian_staff dcs ON cs.id_capaian_staff = dcs.id_capaian_staff
    WHERE
        cs.cabang_staff = :cabang AND cs.minggu = :minggu and cs.bulan=:bulan and cs.tahun=:tahun and cs.status='approve'
    GROUP BY
        cs.cabang_staff
";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':cabang', $cabang, PDO::PARAM_STR);
    $stmt->bindParam(':minggu', $minggu, PDO::PARAM_INT);
    $stmt->bindParam(':bulan', $bulan, PDO::PARAM_INT);
    $stmt->bindParam(':tahun', $tahun, PDO::PARAM_INT);
    $stmt->execute();


    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result && $result['status'] != 'approve') {

        $pesan = urlencode("Tidak ada data laporan Cabang $cabang <strong>minggu-$minggu Bulan-$bulanArray[$bulan] tahun
$tahun </strong>
Silahkan Buat Laporan Staff lapang
terlebih dahulu lalu approve!");
        // pindah(menu_progress("laporan/submit&error&minggu=$minggu&bulan=$bulan&tahun=$tahun&pesan=$pesan"));
    } else {
        // Query untuk mengambil data dari kedua tabel
        $query = "
SELECT
dcs.*,
cs.nik_staff,
cs.nama_staff,
cs.cabang_staff,
cs.regional,
cs.wilayah,
cs.tahun,
cs.bulan,
cs.minggu,
cs.created_at,
cs.status,
cs.priode_dari,
cs.priode_sampai
FROM
detail_capaian_staff dcs
JOIN
capaian_staff cs ON dcs.id_capaian_staff = cs.id_capaian_staff
where cabang_staff= :cabang and minggu= :minggu and bulan=:bulan and tahun=:tahun and status='approve'
";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":cabang", $cabang);
        $stmt->bindParam(":tahun", $tahun);
        $stmt->bindParam(":bulan", $bulan);
        $stmt->bindParam(":minggu", $minggu);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    ?>
        <form action="" method="post">
            <div class="row">
                <input type="hidden" name="manager_cabang" value="<?= $detailAkun['nama'] ?>">
                <input type="hidden" name="nama_cabang" value="<?= $detailAkun['nama_cabang'] ?>">
                <input type="hidden" name="regional" value="<?= $detailAkun['regional'] ?>">
                <input type="hidden" name="wilayah" value="<?= $detailAkun['wilayah'] ?>">
                <input type="hidden" name="minggu" value="<?= $minggu ?>">
                <input type="hidden" name="bulan" value="<?= $bulan ?>">
                <input type="hidden" name="tahun" value="<?= $tahun ?>">
                <input type="hidden" name="total_staff_laporan" value="<?= $result['jumlah_staff'] ?>">
                <input type="hidden" name="total_am" value="<?= $result['total_anggota_masuk'] ?>">
                <input type="hidden" name="total_ak" value="<?= $result['total_anggota_keluar'] ?>">
                <input type="hidden" name="total_nett_agt" value="<?= $result['total_nett_anggota'] ?>">
                <input type="hidden" name="total_naik_par" value="<?= ($result['total_naik_par']) ?>">
                <input type="hidden" name="total_turun_par" value="<?= ($result['total_turun_par']) ?>">
                <input type="hidden" name="total_nett_par" value="<?= ($result['total_nett_par']) ?>">
                <input type="hidden" name="total_pembiayaan_lain" value="<?= $result['total_pemb_lain'] ?>">
                <input type="hidden" name="total_anggota_cuti" value="<?= $result['total_agt_cuti'] ?>">
                <input type="hidden" name="total_pengajuan_tpk" value="<?= $result['total_agt_tpk'] ?>">


                <div class="col-lg-4 col-sm-12">
                    <h4>INFORMASI</h4>
                    <table class="table table-bordered">
                        <tbody>

                            <tr>
                                <th scope="row" style="width: 40%;">Manager Cabang</th>
                                <td><?= $detailAkun['nama'] ?></td>
                            </tr>
                            <tr>
                                <th scope="row" style="width: 30%;">Nama Cabang</th>
                                <td><?= $detailAkun['kode_cabang'] ?> - <?= $detailAkun['nama_cabang'] ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Regional</th>
                                <td>Reg. <?= $detailAkun['regional'] ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Wilayah</th>
                                <td>wilayah <?= $detailAkun['wilayah'] ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Minggu</th>
                                <td>ke <?= $minggu ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Bulan</th>
                                <td><?= $bulanArray[$bulan] ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Tahun</th>
                                <td><?= $tahun ?></td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <div class="col-lg-8 col-sm-12">
                    <h4>Progress Laporan - Sudah Approve</h4>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th scope="row" style="width: 30%;">Total Staff Laporan </th>
                                <td><?= $result['jumlah_staff'] ?> Staff Laporan/<?= $jml_staff ?> Total Staff</td>
                            </tr>
                            <tr>
                                <th scope="row" style="width: 30%;">Total Anggota </th>
                                <td>
                                    <h5 class=' text-lg text-<?= $result['total_nett_anggota'] > 0 ? "success" : "danger" ?>'>
                                        AM : <?= $result['total_anggota_masuk'] ?> |
                                        AK : <?= $result['total_anggota_keluar'] ?> |
                                        NETT : <?= $result['total_nett_anggota'] ?>

                                    </h5>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" style="width: 30%;">Total Outstanding PAR </th>
                                <td>
                                    <h6 class=' text-lg text-<?= $result['total_nett_par'] < 0 ? "success" : "danger" ?>'>
                                        PAR NAIK : <?= rupiah($result['total_naik_par']) ?> |
                                        PAR TURUN : <?= rupiah($result['total_turun_par']) ?> <br />
                                        NETT PAR : <?= rupiah($result['total_nett_par']) ?>

                                    </h6>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" style="width: 30%;">Total Pembiayaan Lain </th>
                                <td>
                                    <h6>
                                        <?php
                                        foreach ($pinjamanArray as $kode => $teks) {
                                            $kd = 'total_' . $kode;
                                            // echo $kd;
                                            echo $kode . " : " . ($result["$kd"] ? $result["$kd"] : 0) . " | ";
                                        }
                                        ?>
                                        TOTAL : <?= $result['total_pemb_lain'] ?>
                                    </h6>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" style="width: 30%;">Total Anggota Cuti </th>
                                <td>

                                    <?= $result['total_agt_cuti'] ?>

                                </td>
                            </tr>
                            <tr>
                                <th scope="row" style="width: 30%;">Total Pengajuan TPK </th>
                                <td>

                                    <?= $result['total_agt_tpk'] ?>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="col-lg-12 mt-2">
                    <h3>DETAIL LAPORAN</h3>
                    <div class="scroll-box">
                        <table class='table table-bordered table-responsive'>
                            <tr>
                                <th>NO</th>
                                <th>NIK</th>
                                <th>NAMA</th>
                                <th>AM</th>
                                <th>AK</th>
                                <th>NETT AGT</th>
                                <th>PAR NAIK</th>
                                <th>PAR TURUN</th>
                                <th>NETT PAR</th>
                                <th>PEMB LAIN</th>
                                <th>KETERANGAN</th>
                            </tr>

                            <?php
                            $no = 1;
                            foreach ($data as $row) : ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row['nik_staff'] ?></td>
                                    <td><?= $row['nama_staff'] ?></td>
                                    <td><?= $row['anggota_masuk'] ?></td>
                                    <td><?= $row['anggota_keluar'] ?></td>
                                    <td><?= $row['nett_anggota'] ?></td>
                                    <td><?= rupiah($row['naik_par']) ?></td>
                                    <td><?= rupiah($row['turun_par']) ?></td>
                                    <td><?= rupiah($row['nett_par']) ?></td>
                                    <td><?= $row['pemb_lain'] ?></td>
                                    <td><?= $row['keterangan'] ?></td>
                                </tr>

                            <?php endforeach; ?>
                        </table>
                    </div>

                </div>
                <div class="col-lg-12 mt-2">

                    <?php

                    $q_belum = $pdo->prepare("select * from staff where cabang='$cabang' 
                and nik_staff not in(select nik_staff from capaian_staff where cabang_staff= :cabang and minggu= :minggu and bulan=:bulan and tahun=:tahun and status='approve' ) ");
                    $q_belum->bindParam(":cabang", $cabang);
                    $q_belum->bindParam(":tahun", $tahun);
                    $q_belum->bindParam(":bulan", $bulan);
                    $q_belum->bindParam(":minggu", $minggu);
                    $q_belum->execute();
                    $data = $q_belum->fetchAll(PDO::FETCH_ASSOC);

                    if ($data) {
                    ?>
                        <h3>BELUM LAPORAN</h3>
                        <div class="scroll-box">
                            <table class='table table-bordered table-responsive'>
                                <tr>
                                    <th>NO</th>
                                    <th>NIK</th>
                                    <th>NAMA</th>
                                    <th>KETERANGAN</th>
                                </tr>

                                <?php
                                $no = 1;
                                foreach ($data as $row) : ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $row['nik_staff'] ?></td>
                                        <td><?= $row['nama_staff'] ?></td>
                                        <td>belum</td>
                                    </tr>

                                <?php endforeach; ?>
                            </table>
                        </div>
                    <?php
                    }
                    ?>


                </div>
                <div class="col">
                    <h2>Submit Laporan</h2>

                    <div class="form-floating">
                        <textarea class="form-control" name="keterangan" style="height: 100px" placeholder="Isi Keterangan" id="floatingTextarea"><?= @$existingData['keterangan'] ?></textarea>
                        <label for="floatingTextarea">Keterangan</label>
                    </div>



                </div>
                <?php
                $disabled = $result['jumlah_staff'] == $jml_staff ? "" : "disabled";
                ?>
                <button type="submit" <?= $disabled ?> name='kirim' class="btn btn-success mt-3">Submit Laporan</button>
                <a href="<?= menu_progress("laporan/submit") ?>" class="btn btn-danger mt-3"><i class="fa fa-arrow-left"></i>
                    Kembali</a>
            </div>
        <?php
    }
        ?>
        </form>
        <style>
            .scroll-box {
                max-height: 300px;
                overflow-y: scroll;
                border: 1px solid #ccc;
                padding: 10px;
            }
        </style>
    <?php
    if (isset($_POST['kirim'])) {
        // Tangkap data dari formulir
        $manager_cabang = $_POST['manager_cabang'];
        $nama_cabang = $_POST['nama_cabang'];
        $regional = $_POST['regional'];
        $wilayah = $_POST['wilayah'];
        $minggu = $_POST['minggu'];
        $bulan = $_POST['bulan'];
        $tahun = $_POST['tahun'];
        $total_staff_laporan = $_POST['total_staff_laporan'];
        $total_am = $_POST['total_am'];
        $total_ak = $_POST['total_ak'];
        $total_nett_agt = $_POST['total_nett_agt'];
        $total_naik_par = $_POST['total_naik_par'];
        $total_turun_par = $_POST['total_turun_par'];
        $total_nett_par = $_POST['total_nett_par'];
        $total_pembiayaan_lain = $_POST['total_pembiayaan_lain'];
        $total_anggota_cuti = $_POST['total_anggota_cuti'];
        $total_pengajuan_tpk = $_POST['total_pengajuan_tpk'];
        $keterangan = $_POST['keterangan'];

        if ($edit == 'ya') {
            $stmt = $pdo->prepare("
            UPDATE capaian_cabang 
            SET manager_cabang = ?, total_staff_laporan = ?, total_am = ?, total_ak = ?, 
            total_nett_agt = ?, total_naik_par = ?, total_turun_par = ?, total_nett_par = ?, 
            total_pembiayaan_lain = ?, total_anggota_cuti = ?, total_pengajuan_tpk = ?, keterangan = ?, status = 'done'
            WHERE id=?
        ");

            $stmt->execute([
                $manager_cabang, $total_staff_laporan, $total_am, $total_ak, $total_nett_agt, $total_naik_par,
                $total_turun_par, $total_nett_par, $total_pembiayaan_lain, $total_anggota_cuti,
                $total_pengajuan_tpk, $keterangan, $id_laporan_cabang
            ]);
        } else {
            // Persiapkan dan eksekusi kueri SQL untuk menyimpan data
            $stmt = $pdo->prepare("INSERT INTO capaian_cabang 
            (manager_cabang, nama_cabang, regional, wilayah, minggu, bulan, tahun, total_staff_laporan, 
            total_am, total_ak, total_nett_agt, total_naik_par, total_turun_par, total_nett_par, 
            total_pembiayaan_lain, total_anggota_cuti, total_pengajuan_tpk,keterangan,status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,'done')");

            $stmt->execute([
                $manager_cabang, $nama_cabang, $regional, $wilayah, $minggu, $bulan, $tahun,
                $total_staff_laporan, $total_am, $total_ak, $total_nett_agt, $total_naik_par, $total_turun_par,
                $total_nett_par, $total_pembiayaan_lain, $total_anggota_cuti, $total_pengajuan_tpk, $keterangan
            ]);

            // Persiapkan dan eksekusi kueri SQL untuk menyimpan data
        }


        alert("Berhasil disimpan!");
        // $pesan = urlencode("Laporan <strong>minggu-$minggu Bulan-$bulanArray[$bulan] tahun $tahun </strong>
        // Sudah dibuat!, Terima Kasih ");
        // pindah(menu_progress("laporan/submit"));
    }
}
    ?>