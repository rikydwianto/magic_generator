<h2>Data Capaian Cabang</h2>

<div class="container overflow-hidden text-center">
    <div class="row gx-3">
        <div class="col">
            <label for="filterMinggu">Filter Minggu:</label>
            <select class='form-control' name="" id="filterMinggu">
                <option value="">Minggu</option>
                <?php
                for ($i = 1; $i <= 5; $i++) {
                    echo "<option value='{$i}'>{$i}</option>";
                }
                ?>
            </select>

        </div>
        <div class="col">
            <label for="filterBulan">Filter Bulan:</label>
            <select class='form-control' id="filterBulan">
                <option value="">Semua Bulan</option>
                <?php

                foreach ($bulanArray as $kodeBulan => $namaBulan) {
                    $selbulan = $kodeBulan == date("m") ? "selected" : "";
                    echo "<option $selbulan value='{$namaBulan}'>{$namaBulan}</option>";
                }
                ?>
            </select>
        </div>
        <div class="col">
            <label for="filterTahun">Filter Tahun:</label>
            <!-- <input type="text" id="filterTahun"> -->
            <select class='form-control' name="" id="filterTahun">
                <option value="">Pilih tahun</option>
                <?php
                for ($i = 2023; $i <= 2026; $i++) {
                    $seltahun = $i == date("Y") ? "selected" : "";
                    echo "<option $seltahun value='{$i}'>{$i}</option>";
                }
                ?>
            </select>
        </div>
    </div>
</div>




<table class="table table-bordered" id='table_capaian'>
    <thead>
        <tr>
            <th>NO</th>
            <th>Manager Cabang</th>
            <th>Nama Cabang</th>
            <th>Regional</th>
            <th>Minggu</th>
            <th>Bulan</th>
            <th>Tahun</th>
            <th>Staff</th>
            <th>AM</th>
            <th>AK</th>
            <th>NETT AGT</th>
            <th>PAR NAIK</th>
            <th>PAR TURUN</th>
            <th>NETT TURUN</th>
            <th>PEMB LAIN</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
        try {
            $no = 1;
            // Ambil data dari tabel capaian_cabang
            $stmt = $pdo->query("SELECT * FROM capaian_cabang  where nama_cabang='$detailAkun[nama_cabang]' order by minggu,bulan desc");
            $total_staff = $total_am = $total_ak = $total_nett_agt = $total_naik_par = $total_turun_par = $total_nett_par = $total_pembiayaan_lain = 0;

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $total_staff += $row['total_staff_laporan'];
                $total_am += $row['total_am'];
                $total_ak += $row['total_ak'];
                $total_nett_agt += $row['total_nett_agt'];
                $total_naik_par += $row['total_naik_par'];
                $total_turun_par += $row['total_turun_par'];
                $total_nett_par += $row['total_nett_par'];
                $total_pembiayaan_lain += $row['total_pembiayaan_lain'];
        ?>

                <tr>
                    <td><?= $no ?></td>
                    <td><?= $row['manager_cabang'] ?></td>
                    <td><?= $row['nama_cabang'] ?></td>
                    <td><?= $row['regional'] ?>/<?= $row['wilayah'] ?></td>
                    <td><?= $row['minggu'] ?></td>
                    <td><?= $bulanArray[$row['bulan']] ?></td>
                    <td><?= $row['tahun'] ?></td>
                    <td><?= $row['total_staff_laporan'] ?></td>
                    <td><?= $row['total_am'] ?></td>
                    <td><?= $row['total_ak'] ?></td>
                    <td><?= $row['total_nett_agt'] ?></td>
                    <td><?= rupiah($row['total_naik_par']) ?></td>
                    <td><?= rupiah($row['total_turun_par']) ?></td>
                    <td><?= rupiah($row['total_nett_par']) ?></td>
                    <td><?= $row['total_pembiayaan_lain'] ?></td>
                    <td><?= ($row['status']) ?></td>
                </tr>
        <?php
                $no++;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        ?>
    </tbody>
    <!-- <tfoot>
        <tr>
            <td colspan="7">Total</td>
            <td><?= $total_staff ?></td>
            <td><?= $total_am ?></td>
            <td><?= $total_ak ?></td>
            <td><?= $total_nett_agt ?></td>
            <td><?= rupiah($total_naik_par) ?></td>
            <td><?= rupiah($total_turun_par) ?></td>
            <td><?= rupiah($total_nett_par) ?></td>
            <td><?= $total_pembiayaan_lain ?></td>
            <td></td> 
    </tr>
    </tfoot> -->

</table>