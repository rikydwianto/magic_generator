<?php

$aktifitas = urldecode($_GET['aktifitas']);
$singkatan = urldecode($_GET['singkatan']);
$tahun = $_GET['tahun'];

$stmt = $pdo->prepare("SELECT * FROM target WHERE aktifitas = ? AND singkatan = ? AND tahun = ? AND id_user = ? order by bulan asc");
$stmt->execute([$aktifitas, $singkatan, $tahun, $sesi]);
$daftarTarget = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$daftarTarget) {
    pindah($url . "index.php?menu=index&act=target");
}
?>
<h3>Detail Target - <?= $aktifitas ?> (<?= $tahun ?>)</h3>
<table class='table'>
    <thead>
        <tr>
            <th>No Bulan</th>
            <th>Bulan</th>
            <th>Tahun</th>
            <th>Kegiatan</th>
            <th>Target</th>
            <th>Act</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($daftarTarget as $target) : ?>
        <tr>
            <td><?php echo $target['bulan'] ?></td>
            <td><?php echo $bulanArray[$target['bulan']]; ?></td>
            <td><?php echo $target['tahun']; ?></td>
            <td><?php echo $target['aktifitas']; ?></td>
            <td><?php echo $target['target']; ?></td>
            <td>
                <a href="<?= $url . "index.php?menu=index&act=target&submenu=edit_target&id=" . $target['id'] ?>"
                    class="btn btn-primary">
                    Edit
                </a>
                <a href="<?= $url . "index.php?menu=index&act=target&submenu=hapus_target&id=" . $target['id'] . "&aktifitas=" . urlencode($target['aktifitas']) . "&singkatan=" . urlencode($target['singkatan']) . "&tahun=" . $target['tahun'] ?>"
                    class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus target ini?')">
                    Hapus
                </a>



            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>