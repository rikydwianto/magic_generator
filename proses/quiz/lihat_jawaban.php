<?php
$id_kuis = $_GET['id_kuis'];
$query = "select * from kuis where id_kuis='$id_kuis'";
$stmt = $pdo->query($query);
$kuis = $stmt->fetch()
?>
<h2>TAMPIL KUIS</h2>
<h3>Judul : <?= $kuis['nama_kuis'] ?></h3>
<?php
if (isset($_GET['del'])) {
    $id_jawab = $_GET['id_jawab'];
    $qdel_kuis_jawab = "DELETE FROM kuis_jawab WHERE id_jawab = :id_jawab AND id_kuis = :id_kuis";
    $stmt_kuis_jawab = $pdo->prepare($qdel_kuis_jawab);
    $stmt_kuis_jawab->bindParam(':id_jawab', $id_jawab, PDO::PARAM_INT);
    $stmt_kuis_jawab->bindParam(':id_kuis', $id_kuis, PDO::PARAM_INT);
    $stmt_kuis_jawab->execute();

    // Hapus dari tabel soal_jawab
    $qdel_soal_jawab = "DELETE FROM soal_jawab WHERE id_jawab = :id_jawab AND id_kuis = :id_kuis";
    $stmt_soal_jawab = $pdo->prepare($qdel_soal_jawab);
    $stmt_soal_jawab->bindParam(':id_jawab', $id_jawab, PDO::PARAM_INT);
    $stmt_soal_jawab->bindParam(':id_kuis', $id_kuis, PDO::PARAM_INT);
    $stmt_soal_jawab->execute();

    if ($stmt_kuis_jawab && $stmt_soal_jawab) {
        alert("berhasil dihapus");
        pindah($url . "index.php?menu=index&act=quiz&sub=lihat_jawaban&id_kuis=$id_kuis");
    }
}
?>
<a href="<?= $url . "index.php?menu=index&act=quiz&sub=lihat_prepost&id_kuis=$id_kuis" ?>"
    class="btn btn-primary mb-3">Lihat
    jawaban pre post test</a>

<table id="example1" class="table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>NO</th>
            <th>Cabang</th>
            <th><?= ($kuis['anggota'] == 'ya' ? "CENTER" : "NIK") ?></th>
            <th>Nama</th>
            <th>Pengerjaan</th>
            <th>Keterangan</th>
            <th>Benar</th>
            <th>Salah</th>
            <th>Total Score</th>
            <th>Jawaban</th>
            <th>act</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $query = "select * from kuis_jawab where id_kuis='$id_kuis'  order by total_score desc, pengerjaan asc";
        $stmt = $pdo->query($query);
        foreach ($stmt->fetchAll() as $row) {
        ?>
        <tr>
            <td><?= $no ?></td>
            <td><?= $row['cabang'] ?></td>
            <td><?= $row['nik'] ?></td>
            <td><?= proper($row['nama']) ?></td>
            <td><?= $row['pengerjaan'] ?></td>
            <td><?= $row['jenis_kuis'] ?></td>
            <td><?= $row['benar'] ?></td>
            <td><?= $row['salah'] ?></td>
            <td><?= $row['total_score'] ?></td>
            <td>
                <button onclick="openNewTab('<?= $row['id_jawab'] ?>','<?= $row['id_kuis'] ?>')" class='btn'>Lihat
                    Jawaban</button>
            </td>
            <td>
                <?php if ($row['keterangan'] == 'selesai' || true) {
                    ?>
                <a href="<?= $url . "index.php?menu=index&act=quiz&sub=lihat_jawaban&id_kuis=$row[id_kuis]&del&id_jawab=" . $row['id_jawab'] ?>"
                    class="btn btn-danger">x</a>
                <?php
                    } ?>

            </td>
        </tr>
        <?php
            $no++;
        }
        ?>

        <!-- Tambahkan baris lain sesuai dengan data yang ada di tabel kuis -->
    </tbody>
</table>
<?php
include "./proses/quiz/analisa_quiz.php";
?>
<!-- <script>
    
</script> -->