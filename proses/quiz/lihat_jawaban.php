<?php
$id_kuis = $_GET['id_kuis'];
$query = "select * from kuis where id_kuis='$id_kuis'";
$stmt = $pdo->query($query);
$kuis = $stmt->fetch()
?>
<h2>TAMPIL KUIS</h2>
<h3>Judul : <?= $kuis['nama_kuis'] ?></h3>
<table id="example1" class="table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>NO</th>
            <th>Cabang</th>
            <th>NIK</th>
            <th>Nama</th>
            <th>Pengerjaan</th>
            <th>Keterangan</th>
            <th>Benar</th>
            <th>Salah</th>
            <th>Total Score</th>
            <th>Jawaban</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $query = "select * from kuis_jawab where id_kuis='$id_kuis' and keterangan='selesai' order by total_score desc, pengerjaan asc";
        $stmt = $pdo->query($query);
        foreach ($stmt->fetchAll() as $row) {
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= $row['cabang'] ?></td>
                <td><?= $row['nik'] ?></td>
                <td><?= $row['nama'] ?></td>
                <td><?= $row['pengerjaan'] ?></td>
                <td><?= $row['keterangan'] ?></td>
                <td><?= $row['benar'] ?></td>
                <td><?= $row['salah'] ?></td>
                <td><?= $row['total_score'] ?></td>
                <td>
                    <button onclick="openNewTab('<?= $row['id_jawab'] ?>','<?= $row['id_kuis'] ?>')" class='btn'>Lihat
                        Jawaban</button>
                </td>
            </tr>
        <?php
            $no++;
        }
        ?>

        <!-- Tambahkan baris lain sesuai dengan data yang ada di tabel kuis -->
    </tbody>
</table>
<script>
    let popupIsOpen = false;

    function openNewTab(id, id_kuis) {
        // Membuka tab baru
        window.open('popup_jawaban.php?id=' + id + '&id_kuis=' + id_kuis, '_blank', 'width=800,height=600');
    }
</script>