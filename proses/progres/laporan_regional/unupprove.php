<h1>Un Approve Laporan Manager/Cabang</h1>
<?php
if (isset($_GET['lakukan']) && isset($_GET['id'])) {
    try {
        $id = $_GET['id'];
        // Persiapkan dan eksekusi kueri SQL untuk mengubah status menjadi "pending"
        $stmt = $pdo->prepare("UPDATE capaian_cabang SET status = 'pending' WHERE id = ?");
        $stmt->execute([$id]);

        pesan("Status berhasil diubah menjadi pending ");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<table class='table'>
    <thead>
        <tr>
            <th>NO</th>
            <th>CABANG</th>
            <th>MANAGER</th>
            <th>MINGGU</th>
            <th>BULAN</th>
            <th>TAHUN</th>
            <th>STATUS</th>
            <th>ACTION</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $stmt = $pdo->query("SELECT * FROM capaian_cabang where regional='$regional'");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $row['nama_cabang'] ?></td>
            <td><?= $row['manager_cabang'] ?></td>
            <td><?= $row['minggu'] ?></td>
            <td><?= $bulanArray[$row['bulan']] ?></td>
            <td><?= $row['tahun'] ?></td>
            <td><?= $row['status'] ?></td>
            <td><a href="<?= menu_progress("laporan_regional/unupprove&lakukan&id=$row[id]") ?>">Set Pending</a></td>
        </tr>
        <?php
        }

        ?>
    </tbody>
</table>