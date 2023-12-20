<h2>TAMPIL KUIS</h2>
<table id="example" class="table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>ID Kuis</th>
            <th>Nama Kuis</th>
            <th>Pembuat</th>
            <th>Tanggal Kuis</th>
            <th>Link</th>
            <th>Status</th>
            <th>Tampil Jawaban</th>
            <th>Responden</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $query = "select * from kuis";
        $stmt = $pdo->query($query);
        foreach ($stmt->fetchAll() as $row) {
            $qtot = "SELECT COUNT(DISTINCT kj.nik) AS responden
            FROM kuis_jawab kj
            JOIN kuis k ON kj.id_kuis = k.id_kuis where k.id_kuis='$row[id_kuis]';
            ";
            $hitung = $pdo->query($qtot)->fetch()['responden'];
        ?>
        <tr>
            <td><?= $no ?></td>
            <td><?= $row['nama_kuis'] ?></td>
            <td><?= $row['nama_karyawan'] ?></td>
            <td><?= $row['tgl_kuis'] ?></td>
            <td>
                <a href="<?= $url_quiz . "quiz/" . $row['id_kuis'] ?>" target="_blank">Lihat</a>
            </td>
            <td>
                <?= $row['status'] ?>
            </td>
            <td><?= $row['tampil_jawaban'] ?></td>
            <td>
                <a href="<?= $url . "index.php?menu=quiz&act=lihat_jawaban&id_kuis=" . $row['id_kuis'] ?>"
                    class="btn btn-success"><?= $hitung ?></a>

            </td>
        </tr>
        <?php
            $no++;
        }
        ?>

        <!-- Tambahkan baris lain sesuai dengan data yang ada di tabel kuis -->
    </tbody>
</table>