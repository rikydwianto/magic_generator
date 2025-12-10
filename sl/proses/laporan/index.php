<h1>Laporan Mingguan</h1>


<?php
try {
    // Query untuk mengambil data dari tabel capaian_staff
    $query = "SELECT * FROM capaian_staff cs join detail_capaian_staff dc on cs.id_capaian_staff=dc.id_capaian_staff where nik_staff='$nik_staff' order by tahun,bulan,minggu desc";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Menampilkan data dalam bentuk tabel HTML
?>
    <table class='table table-rensponsive' id='tabelCapaianStaff'>
        <tr>
            <th>NO</th>
            <th>NIK</th>
            <th>Nama</th>
            <th>Cabang</th>
            <th>Regional</th>
            <th>Priode</th>
            <th>Minggu ke-</th>
            <th>Keterangan</th>
            <th>Status</th>
            <th>#</th>
        </tr>
        <?php
        $no = 1;
        foreach ($result as $row) {
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= $row['nik_staff'] ?></td>
                <td><?= $row['nama_staff'] ?></td>
                <td><?= $row['cabang_staff'] ?></td>
                <td>Reg.<?= $row['regional'] ?>/<?= $row['wilayah'] ?></td>
                <td><?= $bulanArray[$row['bulan']] ?> - <?= $row['tahun'] ?></td>
                <td><?= $row['minggu'] ?></td>
                <td><?= $row['keterangan'] ?></td>
                <td>
                    <?php
                    if ($row['status'] == 'pending') badge("Pending", "danger");
                    else if ($row['status'] == 'konfirmasi') badge("Konfirmasi", "warning");
                    else if ($row['status'] == 'approve') badge("Success");

                    ?>

                </td>
                <td>

                    <?php
                    if ($row['status'] == 'pending') {
                    ?>
                        <a href="<?= menu_sl("laporan/edit&id=$row[id_capaian_staff]") ?>" class="btn btn-warning btn-sm text-white">Lanjut <i class="fa fa-arrow-right"></i></a>
                        <!-- <a href="<?= menu_sl("laporan/tambah&id=$row[id_capaian_staff]") ?>" class="btn btn-warning btn-sm text-white"><i class="fa fa-pen"></i></a> -->
                        <a href="<?= menu_sl("laporan/hapus&id=$row[id_capaian_staff]") ?>" onclick="return window.confirm('yakin akan menghapus laporan ini?')" class="btn btn-danger btn-sm text-white"><i class="fa fa-times"></i></a>
                    <?php
                    } else {
                    ?>
                        <a href="<?= menu_sl("laporan/capaian&id=$row[id_capaian_staff]") ?>" class="btn btn-success btn-sm text-white"><i class="fa fa-eye"></i></a>
                    <?php
                    }

                    ?>
                </td>
            </tr>
    <?php
            $no++;
        }

        echo "</table>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Tutup koneksi ke database
    $pdo = null;
    ?>