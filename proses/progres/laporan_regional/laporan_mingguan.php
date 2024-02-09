<h1>Laporan Mingguan Semua Staff</h1>
<hr>
<form action="" class="mb-3" method="get">
    <input type="hidden" name="menu" value="laporan_regional/laporan_mingguan">

    <div class="row gx-3">
        <div class="col">
            <label for="filterstatus">Filter Status:</label>
            <select class='form-control' name="status" required id="filterstatus">
                <option value="approve">Approve</option>
                <option value="konfirmasi">konfirmasi</option>
                <option value="pending">Pending</option>
            </select>

        </div>
        <div class="col">
            <label for="filterMinggu">Filter Minggu:</label>
            <select class='form-control' name="minggu" required id="filterMinggu">
                <option value="all">SEMUA</option>
                <?php
                for ($i = 1; $i <= 5; $i++) {
                    $selminggu  = $_GET['minggu'] == $i ? "selected" : "";
                    echo "<option $selminggu value='{$i}'>{$i}</option>";
                }
                ?>
            </select>

        </div>
        <div class="col">
            <label for="filterBulan">Filter Bulan:</label>
            <select class='form-control' name='bulan' required id="filterBulan">
                <?php

                foreach ($bulanArray as $kodeBulan => $namaBulan) {
                    $selbulan = $kodeBulan == $_GET['bulan']  ? "selected" : "";
                    echo "<option $selbulan value='{$kodeBulan}'>{$namaBulan}</option>";
                }
                ?>
            </select>
        </div>
        <div class="col">
            <label for="filterTahun">Filter Tahun:</label>
            <!-- <input type="text" id="filterTahun"> -->
            <select class='form-control' required name="tahun" id="filterTahun">
                <option value="semua">Pilih tahun</option>
                <?php
                for ($i = 2023; $i <= 2026; $i++) {
                    $seltahun = $i == date("Y") ? "selected" : "";
                    echo "<option $seltahun value='{$i}'>{$i}</option>";
                }
                ?>
            </select>
        </div>
    </div>
    <button type="submit" class="btn btn-success mt-2"><i class="fa fa-search"></i> Cari</button>
</form>
<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['bulan']) && isset($_GET['tahun']) && isset($_GET['minggu'])) {
    $bulan = $_GET['bulan'];
    $minggu = $_GET['minggu'];
    $tahun = $_GET['tahun'];
    $status = $_GET['status'];
    $tm = '';
    try {
        if ($minggu != 'all') $tm = "and minggu='$minggu'";
        $cek_laporan = $pdo->prepare("SELECT * from capaian_staff cs 
        inner join detail_capaian_staff dc on cs.id_capaian_staff=dc.id_capaian_staff 
        where  regional=:regional
        and bulan=:bulan and tahun=:tahun and status=:status $tm
        order by nett_anggota desc
        ");
        $cek_laporan->bindParam(":regional", $regional);
        $cek_laporan->bindParam(":bulan", $bulan);
        $cek_laporan->bindParam(":tahun", $tahun);
        $cek_laporan->bindParam(":status", $status);
        $cek_laporan->execute();
        $data = $cek_laporan->fetchAll(PDO::FETCH_ASSOC);
        $no = 1;
?>
<table class='table table-bordered mt-3' id='table'>
    <thead>
        <tr>
            <th>NO</th>
            <th>CABANG</th>
            <th>Minggu</th>
            <th>Bulan</th>
            <th>Tahun</th>
            <th>AM</th>
            <th>AK</th>
            <th>NETT</th>
            <th>PAR NAIK</th>
            <th>PAR TURUN</th>
            <th>NETT PAR</th>
            <th>PEMB LAIN</th>
            <th>CUTI</th>
            <th>TPK</th>
            <th>KETERANGAN</th>
        </tr>
    </thead>
    <tbody>
        <?php
                foreach ($data as $row) {
                ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $row['cabang_staff'] ?></td>
            <td><?= $row['minggu'] ?></td>
            <td><?= $bulanArray[$row['bulan']] ?></td>
            <td><?= $row['tahun'] ?></td>
            <td><?= $row['anggota_masuk'] ?></td>
            <td><?= $row['anggota_keluar'] ?></td>
            <td><?= $row['nett_anggota'] ?></td>
            <td><?= angka($row['naik_par']) ?></td>
            <td><?= angka($row['turun_par']) ?></td>
            <td><?= angka($row['nett_par']) ?></td>
            <td><?= $row['pemb_lain'] ?></td>
            <td><?= $row['agt_cuti'] ?></td>
            <td><?= $row['agt_tpk'] ?></td>
            <td><?= $row['keterangan'] ?></td>
        </tr>
        <?php
                }
                ?>
    </tbody>
</table>
<?php
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

?>