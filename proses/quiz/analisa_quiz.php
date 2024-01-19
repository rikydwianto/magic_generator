<h2>
    Analisa Jawaban
</h2>
<table class='table table-bordered' id="example">
    <thead>
        <tr>
            <th>NO</th>
            <th>SOAL</th>
            <th>JAWABAN</th>
            <th>TOTAL BENAR</th>
            <th>TOTAL SALAH</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $q = "SELECT
        *
    FROM
        soal s

    where s.id_kuis='$id_kuis' group by s.id_soal";
        $stmt = $pdo->query($q);
        foreach ($stmt->fetchAll() as $row) {
            $jsonArray = json_decode($row['pilihan'], true);
            $q_hitung = "SELECT
            id_soal,
            id_kuis,
            COUNT(CASE WHEN keterangan = 'BENAR' THEN 1 END) AS jumlah_benar,
            COUNT(CASE WHEN keterangan = 'SALAH' THEN 1 END) AS jumlah_salah
            FROM
            soal_jawab

            WHERE id_soal='$row[id_soal]' ";
            $stmt_hitung = $pdo->query($q_hitung);
            $hitung = $stmt_hitung->fetch();
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $row['soal'] ?></td>
            <td><?= getTeksById($jsonArray, $row['jawaban']); ?></td>
            <td>
                <a href="javascript:void()"
                    onclick="jawabAnalisa('<?= $row['id_kuis'] ?>','BENAR','<?= $row['id_soal'] ?>')"
                    class="btn btn-success"><?= @hitung($pdo, $id_kuis, $row['id_soal'], "BENAR") ?></a>

            </td>
            <td>
                <a href="javascript:void()"
                    onclick="jawabAnalisa('<?= $row['id_kuis'] ?>','SALAH','<?= $row['id_soal'] ?>')"
                    class="btn btn-danger"><?= @hitung($pdo, $id_kuis, $row['id_soal'], "SALAH") ?></a>
            </td>
        </tr>
        <?php
        }
        ?>
    </tbody>

</table>