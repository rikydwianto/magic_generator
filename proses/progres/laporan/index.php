<h2>Data Capaian Cabang</h2>
<table class="table table-bordered" id='table_capaian'>
    <thead>
        <tr>
            <th>NO</th>
            <th>Manager Cabang</th>
            <th>Nama Cabang</th>
            <th>Regional</th>
            <th>Minggu</th>
            <th>Priode</th>
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

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        ?>

                <tr>
                    <td><?= $no ?></td>
                    <td><?= $row['manager_cabang'] ?></td>
                    <td><?= $row['nama_cabang'] ?></td>
                    <td><?= $row['regional'] ?>/<?= $row['wilayah'] ?></td>
                    <td><?= $row['minggu'] ?></td>
                    <td><?= $bulanArray[$row['bulan']] ?>-<?= $row['tahun'] ?></td>
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
</table>