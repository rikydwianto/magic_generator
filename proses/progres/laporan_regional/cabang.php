<h1>Laporan Percabang</h1>
<hr>
<form action="" method="get">
    <input type="hidden" name="menu" value="laporan_regional/cabang">

    <div class="row gx-3">
        <div class="col">
            <div class="form-group">
                <label for="cabang">Cabang:</label>

                <select class="form-control" id="cabang" name="cabang" required>

                    <option value="">Pilih Cabang</option>
                    <?php
                    $query = "SELECT * FROM cabang where regional='$regional' order by wilayah asc";
                    $result = $pdo->query($query);

                    // Loop untuk menampilkan setiap elemen dalam array sebagai opsi
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $selcab = $row['nama_cabang'] == $_GET['cabang'] ? "selected" : "";
                        echo '<option ' . $selcab . ' value="' . $row['nama_cabang'] . '">' . $row['kode_cabang'] . " - " . $row['nama_cabang'] .  " - " . $row['wilayah'] . '</option>';
                    }

                    ?>
                </select>

            </div>

        </div>
        <div class="col">
            <label for="filterBulan">Filter Bulan:</label>
            <select class='form-control' name='bulan' required id="filterBulan">
                <option value="">Pilih Bulan</option>
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


if (isset($_GET['cabang']) && isset($_GET['tahun'])) {
    $cabang = $_GET['cabang'];
    $tahun = $_GET['tahun'];
    $bulan = $_GET['bulan'];
    $query = "
SELECT
    c.nama_cabang,
    cs.nik_staff,
    cs.nama_staff,
    SUM(CASE WHEN cs.minggu = 1 THEN dcs.nett_anggota ELSE 0 END) AS nett_minggu_1,
    SUM(CASE WHEN cs.minggu = 2 THEN dcs.nett_anggota ELSE 0 END) AS nett_minggu_2,
    SUM(CASE WHEN cs.minggu = 3 THEN dcs.nett_anggota ELSE 0 END) AS nett_minggu_3,
    SUM(CASE WHEN cs.minggu = 4 THEN dcs.nett_anggota ELSE 0 END) AS nett_minggu_4,
    SUM(CASE WHEN cs.minggu = 5 THEN dcs.nett_anggota ELSE 0 END) AS nett_minggu_5,
    SUM(CASE WHEN cs.minggu = 1 THEN dcs.nett_par ELSE 0 END) AS nett_par_minggu_1,
    SUM(CASE WHEN cs.minggu = 2 THEN dcs.nett_par ELSE 0 END) AS nett_par_minggu_2,
    SUM(CASE WHEN cs.minggu = 3 THEN dcs.nett_par ELSE 0 END) AS nett_par_minggu_3,
    SUM(CASE WHEN cs.minggu = 4 THEN dcs.nett_par ELSE 0 END) AS nett_par_minggu_4,
    SUM(CASE WHEN cs.minggu = 5 THEN dcs.nett_par ELSE 0 END) AS nett_par_minggu_5,
    SUM(CASE WHEN cs.minggu = 1 THEN dcs.pemb_lain ELSE 0 END) AS pemb_lain_minggu_1,
    SUM(CASE WHEN cs.minggu = 2 THEN dcs.pemb_lain ELSE 0 END) AS pemb_lain_minggu_2,
    SUM(CASE WHEN cs.minggu = 3 THEN dcs.pemb_lain ELSE 0 END) AS pemb_lain_minggu_3,
    SUM(CASE WHEN cs.minggu = 4 THEN dcs.pemb_lain ELSE 0 END) AS pemb_lain_minggu_4,
    SUM(CASE WHEN cs.minggu = 5 THEN dcs.pemb_lain ELSE 0 END) AS pemb_lain_minggu_5,
    SUM(CASE WHEN cs.minggu = 1 THEN dcs.agt_cuti ELSE 0 END) AS agt_cuti_minggu_1,
    SUM(CASE WHEN cs.minggu = 2 THEN dcs.agt_cuti ELSE 0 END) AS agt_cuti_minggu_2,
    SUM(CASE WHEN cs.minggu = 3 THEN dcs.agt_cuti ELSE 0 END) AS agt_cuti_minggu_3,
    SUM(CASE WHEN cs.minggu = 4 THEN dcs.agt_cuti ELSE 0 END) AS agt_cuti_minggu_4,
    SUM(CASE WHEN cs.minggu = 5 THEN dcs.agt_cuti ELSE 0 END) AS agt_cuti_minggu_5,
    SUM(CASE WHEN cs.minggu = 1 THEN dcs.agt_tpk ELSE 0 END) AS agt_tpk_minggu_1,
    SUM(CASE WHEN cs.minggu = 2 THEN dcs.agt_tpk ELSE 0 END) AS agt_tpk_minggu_2,
    SUM(CASE WHEN cs.minggu = 3 THEN dcs.agt_tpk ELSE 0 END) AS agt_tpk_minggu_3,
    SUM(CASE WHEN cs.minggu = 4 THEN dcs.agt_tpk ELSE 0 END) AS agt_tpk_minggu_4,
    SUM(CASE WHEN cs.minggu = 5 THEN dcs.agt_tpk ELSE 0 END) AS agt_tpk_minggu_5,
    cs.wilayah
FROM
    cabang c
LEFT JOIN
    capaian_staff cs ON c.nama_cabang = cs.cabang_staff
LEFT JOIN
    detail_capaian_staff dcs ON cs.id_capaian_staff = dcs.id_capaian_staff
WHERE
    c.regional=:regional AND cs.bulan=:bulan AND cs.tahun=:tahun and cs.cabang_staff=:cabang and cs.status='approve'
GROUP BY
    cs.nik_staff
    order by cs.nik_staff asc
";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':bulan', $bulan);
        $stmt->bindParam(':cabang', $cabang);
        $stmt->bindParam(':tahun', $tahun);
        $stmt->bindParam(':regional', $regional);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }


?>


    <table class=' table table-bordered table-hover table-striped '>
        <thead class="table-light">
            <?php
            if ($result) {
            ?>
                <tr>
                    <th colspan="32" class="text-center">
                        LAPORAN PER STAFF BULAN <?= strtoupper($bulanArray[$bulan]) ?> TAHUN <?= $tahun ?> <br>
                        CABANG <?= $cabang ?> WILAYAH <?= $result[0]['wilayah'] ?> REGIONAL <?= $regional ?>
                    </th>
                <?php
            }
                ?>

                </tr>
                <tr>
                    <th rowspan="2" align="middle">NIK</th>
                    <th rowspan="2" align="middle">NAMA</th>
                    <th colspan="6" class='text-center'>NET AGT</th>
                    <th colspan="6" class='text-center'>AGT CUTI</th>
                    <th colspan="6" class='text-center'>PEMB LAIN</th>
                    <th colspan="6" class='text-center'>TPK</th>
                    <th colspan="6" class='text-center'>NETT PAR</th>
                </tr>
                <tr class='text-center'>
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                    <th>5</th>
                    <th>jml</th> <!-- Kolom jml untuk NET AGT -->
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                    <th>5</th>
                    <th>jml</th> <!-- Kolom jml untuk NETT PAR -->
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                    <th>5</th>
                    <th>jml</th> <!-- Kolom jml untuk AGT CUti -->
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                    <th>5</th>
                    <th>jml</th> <!-- Kolom jml untuk PEMB LAIN -->
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                    <th>5</th>
                    <th>jml</th> <!-- Kolom Total untuk TPK -->
                </tr>
        </thead>
        <tbody>
            <?php
            if ($result) {
            ?>
                <?php foreach ($result as $row) : ?>
                    <tr>

                        <td><?= $row['nik_staff'] ?></td>
                        <td><?= $row['nama_staff'] ?></td>
                        <?php
                        $total = 0;
                        for ($i = 1; $i <= 5; $i++) {
                            $nilai = $row['nett_minggu_' . $i];
                            $total += $nilai;
                        ?>
                            <th class='text-center <?= warnaPlusMinus($nilai) ?>'><?= $nilai ?></th>
                        <?php
                        }
                        ?>
                        <th class='text-center <?= warnaPlusMinus($total) ?>'><?= $total ?></th> <!-- Kolom Total NET AGT -->

                        <?php
                        $total = 0;
                        for ($i = 1; $i <= 5; $i++) {
                            $nilai = $row['agt_cuti_minggu_' . $i];
                            $total += $nilai;
                        ?>
                            <th class='text-center <?= warnaPlusMinus($nilai) ?>'><?= $nilai ?></th>
                        <?php
                        }
                        ?>
                        <th class='text-center <?= warnaPlusMinus($total) ?>'><?= $total ?></th><!-- Kolom Total AGT CUti -->
                        <?php
                        $total = 0;
                        for ($i = 1; $i <= 5; $i++) {
                            $nilai = $row['pemb_lain_minggu_' . $i];
                            $total += $nilai;
                        ?>
                            <th class='text-center <?= warnaPlusMinus($nilai) ?>'><?= $nilai ?></th>
                        <?php
                        }
                        ?>
                        <th class='text-center <?= warnaPlusMinus($total) ?>'><?= $total ?></th><!-- Kolom Total PEMB LAIN -->
                        <?php
                        $total = 0;
                        for ($i = 1; $i <= 5; $i++) {
                            $nilai = $row['agt_tpk_minggu_' . $i];
                            $total += $nilai;
                        ?>
                            <th class='text-center <?= warnaPlusMinus($nilai) ?>'><?= $nilai ?></th>
                        <?php
                        }
                        ?>
                        <th class='text-center <?= warnaPlusMinus($total) ?>'><?= $total ?></th> <!-- Kolom Total TPK -->
                        <?php
                        $total = 0;
                        for ($i = 1; $i <= 5; $i++) {
                            $nilai = $row['nett_par_minggu_' . $i];
                            $total += $nilai;
                        ?>
                            <th class=' <?= warnaPlusMinusPar($nilai) ?>'><?= angka($nilai) ?></th>
                        <?php
                        }
                        ?>
                        <th class=' <?= warnaPlusMinusPar($total) ?>'><?= angka($total) ?></th>
                        <!-- Kolom Total NETT PAR -->
                    </tr>
                <?php endforeach; ?>
            <?php
            } else {
            ?>
                <tr>

                    <th colspan="32" class='text-center'>Data tidak tersedia!</th>
                </tr>
            <?php
            }
            ?>

        </tbody>
        <tfoot>
            <tr>


                <th colspan="2">TOTAL</th>
                <?php
                $total = 0;
                for ($i = 1; $i <= 5; $i++) {
                    $nilai = getTotalMinggu('nett_anggota', $cabang, $i, $bulan, $tahun);
                    $total += $nilai;
                ?>
                    <th class='text-center <?= warnaPlusMinus($nilai) ?>'><?= $nilai ?></th>
                <?php } ?>
                <th class='text-center <?= warnaPlusMinus($total) ?>'><?= $total ?></th>

                <?php
                $total = 0;
                for ($i = 1; $i <= 5; $i++) {
                    $nilai = getTotalMinggu('agt_cuti', $cabang, $i, $bulan, $tahun);
                    $total += $nilai;
                ?>
                    <th class='text-center <?= warnaPlusMinus($nilai) ?>'><?= $nilai ?></th>
                <?php } ?>
                <th class='text-center <?= warnaPlusMinus($total) ?>'><?= $total ?></th>

                <?php
                $total = 0;
                for ($i = 1; $i <= 5; $i++) {
                    $nilai = getTotalMinggu('pemb_lain', $cabang, $i, $bulan, $tahun);
                    $total += $nilai;
                ?>
                    <th class='text-center <?= warnaPlusMinus($nilai) ?>'><?= $nilai ?></th>
                <?php } ?>
                <th class='text-center <?= warnaPlusMinus($total) ?>'><?= $total ?></th>


                <?php
                $total = 0;
                for ($i = 1; $i <= 5; $i++) {
                    $nilai = getTotalMinggu('agt_tpk', $cabang, $i, $bulan, $tahun);
                    $total += $nilai;
                ?>
                    <th class='text-center <?= warnaPlusMinus($nilai) ?>'><?= $nilai ?></th>
                <?php } ?>
                <th class='text-center <?= warnaPlusMinus($total) ?>'><?= $total ?></th>
                <?php
                $total = 0;
                for ($i = 1; $i <= 5; $i++) {
                    $nilai = getTotalMinggu('nett_par', $cabang, $i, $bulan, $tahun);
                    $total += $nilai;
                ?>
                    <th class='text-right <?= warnaPlusMinusPar($nilai) ?>'><?= angka($nilai) ?></th>
                <?php } ?>
                <th class='text-right <?= warnaPlusMinusPar($total) ?>'><?= angka($total) ?></th>
            </tr>
        </tfoot>

    </table>
<?php
}
?>