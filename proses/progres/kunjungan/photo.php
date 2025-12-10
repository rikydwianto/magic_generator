<h2>Photo kunjungan Staff</h2>

<div class="">


    <?php

    try {
        $id_kunj = $_GET['id'];


        $query = 'SELECT * FROM photo_kunjungan pk join kunjungan k on k.id=pk.id_kunjungan  where id_kunjungan=?';
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id_kunj]);

        $kunjungan = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $no = 1;

    ?>
    <table class='table table-bordered'>
        <tr>
            <th>NO</th>
            <th>Tanggal</th>
            <th>Cabang</th>
            <th>NIK</th>
            <th>Center</th>
            <th>Anggota</th>
            <th>Photo</th>
        </tr>
        <?php
            foreach ($kunjungan as $data) {
            ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $data['tanggal'] ?></td>
            <td><?= $data['nama_cabang'] ?></td>
            <td><?= $data['nik'] ?></td>
            <td><?= $data['nomor_center'] ?></td>
            <td><?= $data['nama_anggota'] ?></td>
            <td>
                <a href="<?= $data['url_photo'] ?>">
                    <img src="<?= $data['url_photo'] ?>" alt="" width='200px' class="img">
                </a>
            </td>
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