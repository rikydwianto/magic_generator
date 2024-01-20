<h2>TAMPIL KUIS</h2>
<a class="btn btn-danger btn-lg mb-2" style="float: right;"
    href="<?= $url . "index.php?menu=index&act=quiz&sub=tambah_kuis" ?>">
    <i class="fas fa-plus"></i> Buat Kuis
</a>
<?php
$q_tambah = ($_SESSION['jenisAkun'] === 'superuser' ? "" : "where kuis.id_user='$sesi'");
?>
<table id="example" class="table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>NO</th>
            <th>Nama Kuis</th>
            <th>Pembuat</th>
            <th>Tanggal Kuis</th>
            <th>Quiz Setting</th>
            <th>Responden</th>
            <th>act</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $query = "SELECT 
        kuis.id_kuis,
        kuis.nama_kuis,
        kuis.nama_karyawan,
        kuis.tgl_kuis,
        kuis.waktu,
        kuis.status,
        kuis.acak,
        kuis.tampil_jawaban,
        COUNT(soal.id_soal) AS jumlah_soal
    FROM kuis
    LEFT JOIN soal ON kuis.id_kuis = soal.id_kuis
    $q_tambah
    GROUP BY kuis.id_kuis
    ORDER BY kuis.id_kuis desc";
        $stmt = $pdo->query($query);
        foreach ($stmt->fetchAll() as $row) {
            $qtot = "SELECT COUNT(DISTINCT kj.unique_id_2) AS responden
            FROM kuis_jawab kj
            JOIN kuis k ON kj.id_kuis = k.id_kuis where k.id_kuis='$row[id_kuis]';
            ";
            $hitung = $pdo->query($qtot)->fetch()['responden'];
        ?>
        <tr>
            <td><?= $no ?></td>
            <td><?= $row['nama_kuis'] ?></td>
            <td><?= $row['nama_karyawan'] ?></td>
            <td>
                <?= $row['tgl_kuis'] ?> <br>
                <a href="<?= $url . "api/qr_quiz.php?id=" . $row['id_kuis'] ?>" class="btn btn-success ">
                    <i class="fa fa-barcode"></i> Barcode </a>
            </td>

            <td>
                Link : <a href="<?= $url_quiz . "quiz/" . $row['id_kuis'] ?>" target="_blank">Lihat</a> <br />
                Jawaban : <?= $row['tampil_jawaban'] ?><br />
                Aktif : <?= $row['status'] ?><br>
                Soal : <?= $row['jumlah_soal'] ?><br>
                Acak : <?= $row['acak'] ?><br>
                <!-- <br /> -->

            </td>
            <td>
                <a href="<?= $url . "index.php?menu=index&act=quiz&sub=lihat_jawaban&id_kuis=" . $row['id_kuis'] ?>"
                    class="btn btn-success btn-sm">Lihat Hasil(<?= $hitung ?>)</a>
                <br>
                <a href="<?= $url . "index.php?menu=index&act=quiz&sub=lihat_prepost&id_kuis=" . $row['id_kuis'] ?>"
                    class="btn btn-sm btn-warning">Lihat Prepost</a>
                <br>
                <a href="<?= $url . "index.php?menu=index&act=quiz&sub=kosongkan&id_kuis=" . $row['id_kuis'] ?>"
                    onclick="return window.confirm('Apakah yakin akan menghapus semua responden?')"
                    class="btn btn-danger btn-sm mt-1">Kosongkan</a>

            </td>
            <td>

                <a href="<?= $url . "index.php?menu=index&act=quiz&sub=edit_kuis&id_kuis=" . $row['id_kuis'] ?>"
                    class="btn btn-sm btn-warning"><i class="fa fa-gears"></i></a>
                <a href="<?= $url . "index.php?menu=index&act=quiz&sub=copy_quis&id_kuis=" . $row['id_kuis'] ?>"
                    class="btn btn-sm btn-primary"><i class="fa fa-copy"></i></a>
                <a href="<?= $url . "index.php?menu=index&act=quiz&sub=hapus_kuis&id_kuis=" . $row['id_kuis'] ?>"
                    class="btn btn-danger btn-sm"
                    onclick="return window.confirm('Apakah yakin untuk menghapus ini?\nsemua yang berhubung dengan kuis ini akan terhapus \nNilai dan Responden akan terhapus ')">
                    <i class="fa fa-times"></i>
                </a>
                <br />
                <?php
                    if ($row['status'] != 'aktif') {
                    ?>
                <a href="<?= $url . "index.php?menu=index&act=quiz&sub=kelola_soal&id_kuis=" . $row['id_kuis'] ?>"
                    class="btn btn-primary btn-sm mt-2">Kelola soal</a>
                <br>
                <a href="<?= $url . "index.php?menu=index&act=quiz&sub=edit_aktif&ket=aktif&id_kuis=" . $row['id_kuis'] ?>"
                    class="btn btn-success btn-sm mt-2">Aktifkan Kuis</a>

                <?php
                    } else {
                        echo "<small class='text'>ketika aktif tidak <br/>dapat olah soal</small><br>";
                    ?>
                <a href="<?= $url . "index.php?menu=index&act=quiz&sub=edit_aktif&ket=tidakaktif&id_kuis=" . $row['id_kuis'] ?>"
                    class="btn btn-danger btn-sm mt-2">Non-Aktif Kuis</a>
                <?php
                    }
                    ?>

            </td>
        </tr>
        <?php
            $no++;
        }
        ?>

        <!-- Tambahkan baris lain sesuai dengan data yang ada di tabel kuis -->
    </tbody>
</table>