<table class='table table-bordered'>
    <thead>
        <tr>
            <th>NO</th>
            <th>Aktifitas</th>
            <th>Singkatan</th>
            <th>Bulan</th>
            <th>Tahun</th>
            <th>Capaian</th>
            <th>ID User</th>
            <th>Tanggal Input</th>
            <th>#</th>
        </tr>
    </thead>
    <tbody>
        <?php
        try {

            // Query untuk mengambil data dari tabel capaian
            $query = "SELECT * FROM capaian where id_user='$sesi'";
            $stmt = $pdo->query($query);
            $no = 1;
            // Tampilkan data dalam tabel HTML
            if ($stmt->rowCount() > 0) {


                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['aktifitas'] ?></td>
                        <td><?= $row['singkatan'] ?></td>
                        <td><?= $bulanArray[$row['bulan']] ?></td>
                        <td><?= $row['tahun'] ?></td>
                        <td class='text-center'><?= $row['capaian'] ?></td>
                        <td><?= $row['id_user'] ?></td>
                        <td><?= $row['tgl_input'] ?></td>
                        <td>
                            <a href="<?= $url . "index.php?menu=index&act=capaian&submenu=hapuscapaian&id=" . $row['id'] ?>" class="btn btn-danger" onclick="return window.confirm('apakah yakin untuk menghapus ini?')"><i class="fa fa-times"></i></a>
                        </td>
                    </tr>
                <?php

                }
            } else {
                ?>
                <tr>
                    <td colspan="9" class='text-center'>Tidak ada data!</td>
                </tr>
        <?php
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        } finally {
            // Tutup koneksi
            $pdo = null;
        }

        ?>
    </tbody>
</table>