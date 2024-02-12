<h1>Laporan Regional</h1>
<hr>
<form action="" method="get">
    <input type="hidden" name="menu" value="laporan_regional/regional">

    <div class="row gx-3">
        <div class="col">
            <label for="filterMinggu">Filter Minggu:</label>
            <select class='form-control' name="minggu" required id="filterMinggu">
                <option value="semua">SEMUA</option>
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
if (isset($_GET['minggu']) && isset($_GET['bulan'])) {
    $bulan = $_GET['bulan'] ? $_GET['bulan'] : date("m");
    $minggu = $_GET['minggu'] ? $_GET['minggu'] : "semua";
    $tahun = $_GET['tahun'] ? $_GET['tahun'] : date("Y");
    if ($minggu == 'semua') {
        $q = '';
    } else {

        $q = "and cc.minggu='$minggu'";
    }
    try {
        $query = "SELECT  SUM(total_staff_laporan) AS total_staff_laporan,
        SUM(total_am) AS total_am,
        SUM(total_ak) AS total_ak,
        SUM(total_nett_agt) AS total_nett_agt,
        SUM(total_naik_par) AS total_naik_par,
        SUM(total_turun_par) AS total_turun_par,
        SUM(total_nett_par) AS total_nett_par,
        SUM(total_pembiayaan_lain) AS total_pembiayaan_lain,
        SUM(total_anggota_cuti) AS total_anggota_cuti,
        SUM(total_pengajuan_tpk) AS total_pengajuan_tpk,
        c.*,
        cc.minggu,cc.bulan,cc.manager_cabang,cc.status
    FROM capaian_cabang cc
    INNER JOIN cabang c ON cc.nama_cabang = c.nama_cabang
     where cc.regional=:regional and  cc.bulan=:bulan and cc.tahun=:tahun and cc.status='done' $q group by c.nama_cabang   ";

        // Eksekusi query
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":regional", $regional);
        $stmt->bindParam(":bulan", $bulan);
        $stmt->bindParam(":tahun", $tahun);
        $stmt->execute();

        // Fetch hasil query
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Tampilkan hasil query
        if ($result) {
?>
<table border="1" class="table mt-2">
    <thead>
        <tr>
            <th colspan="14" class='text-center'>
                REGIONAL <?= $regional ?> <br>
                REKAP LAPORAN PER CABANG <br>
                <?php
                            if ($minggu == 'semua') {
                                echo "BULAN " . strtoupper($bulanArray[$bulan]);
                            } else {

                                echo "MINGGU KE-$minggu" . " BULAN " . strtoupper($bulanArray[$bulan]);
                            }
                            ?>
                TAHUN <?= $tahun ?>
            </th>
        </tr>
        <tr>
            <th>NO</th>
            <th>Manager Cabang</th>
            <th>Nama Cabang</th>
            <th>Staff</th>
            <th>AM</th>
            <th>AK</th>
            <th>NETT</th>
            <th>AGT CUTI</th>
            <th>TPK</th>
            <th>PEMB LAIN</th>
            <th>PAR NAIK</th>
            <th>PAR TURUN</th>
            <th>NETT PAR</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
                    $no = 1;
                    $total_staff = $total_pengajuan_tpk = $total_am = $total_ak = $total_nett_agt = $total_anggota_cuti = $total_pembiayaan_lain = $total_naik_par = $total_turun_par = $total_nett_par = 0;

                    foreach ($result as $row) {
                        $total_am += $row['total_am'];
                        $total_ak += $row['total_ak'];
                        $total_staff += $row['total_staff_laporan'];
                        $total_nett_agt += $row['total_nett_agt'];
                        $total_anggota_cuti += $row['total_anggota_cuti'];
                        $total_pembiayaan_lain += $row['total_pembiayaan_lain'];
                        $total_pengajuan_tpk += $row['total_pengajuan_tpk'];
                        $total_naik_par += $row['total_naik_par'];
                        $total_turun_par += $row['total_turun_par'];
                        $total_nett_par += $row['total_nett_par'];

                    ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $row['manager_cabang'] ?></td>
            <td><?= $row['nama_cabang'] ?></td>
            <td class='text-center'><?= $row['total_staff_laporan'] ?></td>
            <td class='text-center'><?= $row['total_am'] ?></td>
            <td class='text-center'><?= $row['total_ak'] ?></td>
            <td class='text-center'><?= $row['total_nett_agt'] ?></td>
            <td class='text-center'><?= $row['total_anggota_cuti'] ?></td>
            <td class='text-center'><?= $row['total_pengajuan_tpk'] ?></td>
            <td class='text-center'><?= $row['total_pembiayaan_lain'] ?></td>
            <td class=''><?= rupiah($row['total_naik_par']) ?></td>
            <td class=''><?= rupiah($row['total_turun_par']) ?></td>
            <td class=''><?= rupiah($row['total_nett_par']) ?></td>
            <td><?= $row['status'] ?></td>
        </tr>
        <?php
                    }
                    ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3">Total</th>
            <td class='text-center'><?= $total_staff ?></td>
            <td class='text-center'><?= $total_am ?></td>
            <td class='text-center'><?= $total_ak ?></td>
            <td class='text-center'><?= $total_nett_agt ?></td>
            <td class='text-center'><?= $total_anggota_cuti ?></td>
            <td class='text-center'><?= $total_pengajuan_tpk ?></td>
            <td class='text-center'><?= $total_pembiayaan_lain ?></td>
            <td class=''><?= rupiah($total_naik_par) ?></td>
            <td class=''><?= rupiah($total_turun_par) ?></td>
            <td class=''><?= rupiah($total_nett_par) ?></td>
            <td></td>
        </tr>
    </tfoot>

</table>
<?php
        } else {
            echo 'Tidak ada hasil yang ditemukan.';
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>