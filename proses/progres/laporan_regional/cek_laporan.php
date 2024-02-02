<h1>Cek Laporan Cabang</h1>
<hr>
<form action="" method="get">
    <input type="hidden" name="menu" value="laporan_regional/cek_laporan">

    <div class="row gx-3">
        <div class="col">
            <label for="filterMinggu">Filter Minggu:</label>
            <select class='form-control' name="minggu" required id="filterMinggu">
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
                    $selbulan = $kodeBulan == date("m") ? "selected" : "";
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
$q = $pdo->prepare("select * from cabang where regional=:regional  and kode_cabang < 500 order by kode_cabang asc");
$q->bindParam(":regional", $regional);
$q->execute();
$list_cabang = $q->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['bulan']) && isset($_GET['tahun']) && isset($_GET['minggu'])) {
    $bulan = $_GET['bulan'];
    $minggu = $_GET['minggu'];
    $tahun = $_GET['tahun'];
?>
<table class=" table-bordered mt-2">
    <thead>
        <tr class="text-center">
            <th colspan=8>
                CEK LAPORAN STAFF LAPANG DAN MANAGER <br>
                MINGGU KE-<?= $minggu ?> BULAN-<?= strtoupper($bulanArray[$bulan]) ?> TAHUN <?= $tahun ?> <br>
                REGIONAL <?= $regional ?>
            </th>
        </tr>
        <tr>
            <th>NO</th>
            <th>KODE</th>
            <th>CABANG</th>
            <th>WILAYAH</th>
            <th>LAPORAN</th>
            <th>WAKTU</th>
            <th>KETERANGAN</th>
            <!-- <th>KET MANAGER</th> -->
        </tr>
    </thead>
    <tbody>
        <?php
            $no = 1;
            foreach ($list_cabang as $cab) {
                $cek_laporan = $pdo->prepare("SELECT * from capaian_cabang 
                where nama_cabang=:cabang and regional=:regional and minggu=:minggu 
                and bulan=:bulan and tahun=:tahun and status='done'
                ");
                $cek_laporan->bindParam(":cabang", $cab['nama_cabang']);
                $cek_laporan->bindParam(":regional", $cab['regional']);
                $cek_laporan->bindParam(":minggu", $minggu);
                $cek_laporan->bindParam(":bulan", $bulan);
                $cek_laporan->bindParam(":tahun", $tahun);
                $cek_laporan->execute();
                $cek_laporan = $cek_laporan->fetch();
                // var_dump($cek_laporan)
                if ($cek_laporan) {
                    $ket = 'SUDAH LAPORAN';
                    $laporan = 'DONE';
                    $waktu = $cek_laporan['created_at'];
                    $ket_laporan = $cek_laporan['keterangan'];
                } else {
                    $laporan = '';
                    $ket = '';
                    $waktu = '';
                    $ket_laporan = '';

                    $cek_sl = $pdo->prepare("SELECT count(*) as total_staff_laporan from capaian_staff 
                where cabang_staff=:cabang and regional=:regional and minggu=:minggu 
                and bulan=:bulan and tahun=:tahun and status='approve'
                ");
                    $cek_sl->bindParam(":cabang", $cab['nama_cabang']);
                    $cek_sl->bindParam(":regional", $cab['regional']);
                    $cek_sl->bindParam(":minggu", $minggu);
                    $cek_sl->bindParam(":bulan", $bulan);
                    $cek_sl->bindParam(":tahun", $tahun);
                    $cek_sl->execute();
                    $cek_sl = $cek_sl->fetch();
                    $staff_laporan = $cek_sl['total_staff_laporan'];
                    if ($staff_laporan > 0) {
                        $ket = "$staff_laporan SL sudah input laporan ";
                    } else {
                        $ket = 'Manager dan SL belum input laporan';
                    }
                }
            ?>
        <tr>
            <td><?= $no ?></td>
            <td class='text-center'><?= $cab['kode_cabang'] ?></td>
            <td><?= $cab['nama_cabang'] ?></td>
            <td class='text-center'><?= $cab['wilayah'] ?></td>
            <td class='text-center'><?= $laporan ?></td>
            <td><?= $waktu ?></td>
            <td><?= $ket ?></td>
            <!-- <td><?= $ket_laporan ?></td> -->
        </tr>
        <?php
                $no++;
            }
            ?>
    </tbody>
</table>
<?php
}
?>