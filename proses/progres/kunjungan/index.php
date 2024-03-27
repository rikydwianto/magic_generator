<h2>Data kunjungan Staff</h2>

<div class="">


    <?php

    try {

        if ($jabatan != 'Regional') {
            $query = 'SELECT * FROM kunjungan where nama_cabang=?';
            $stmt = $pdo->prepare($query);
            $stmt->execute([$detailAkun['nama_cabang']]);

            $kunjungan = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $query = 'SELECT * FROM kunjungan ';
            $stmt = $pdo->prepare($query);
            $stmt->execute();

            $kunjungan = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $no = 1;

    ?>
    <table class='table table-bordered'>
        <tr>
            <th>NO</th>
            <th>NIK</th>
            <th>Tanggal</th>
            <th>Cabang</th>
            <th>Center</th>
            <th>Anggota</th>
            <th>No HP</th>
            <th>Lokasi</th>
            <th>Tipe</th>
            <th>Jenis Usaha</th>
            <th>Photo</th>
            <th>Status</th>
        </tr>
        <?php
            foreach ($kunjungan as $data) {
                $query = "select count(*) as photo from photo_kunjungan where id_kunjungan=?";
                $q = $pdo->prepare($query);
                $q->execute([$data['id']]);
                $total = $q->fetch()['photo'];
            ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $data['nik'] ?></td>
            <td><?= $data['tanggal'] ?></td>
            <td><?= $data['nama_cabang'] ?></td>
            <td><?= $data['nomor_center'] ?></td>
            <td><?= $data['nama_anggota'] ?></td>
            <td><?= $data['no_hp'] ?></td>
            <td><?= $data['lokasi'] ?></td>
            <td><?= $data['tipe_kunjungan'] ?></td>
            <td><?= $data['jenis_usaha'] ?></td>
            <td>
                <a href="<?= menu_progress("kunjungan/photo&id=" . $data['id']) ?>">
                    <?= $total ?> photo
                </a>
            </td>
            <td><?= $data['status'] ?></td>
        </tr>
        <?php
            }
            ?>
    </table>
    <?php
    } catch (PDOException $e) {
        echo 'Koneksi database gagal: ' . $e->getMessage();
    }
    ?>

</div>