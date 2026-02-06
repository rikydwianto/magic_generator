<?php

if (isset($_GET['id'])) {
    $id_target = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM target WHERE id = ?");
    $stmt->execute([$id_target]);
    $target = $stmt->fetch(PDO::FETCH_ASSOC);


    if ($target) {

?>

<?php



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (isset($_POST['id'])) {

                $id_target = $_POST['id'];
                $aktifitas = $_POST['aktifitas'];
                list($aktifitas, $singkatan) = explode('|', $aktifitas);


                $target_baru = $_POST['target'];


                $stmt = $pdo->prepare("UPDATE target SET target = ?, aktifitas=? WHERE id = ?");
                $stmt->execute([$target_baru, $aktifitas, $id_target]);
            } else {

                echo "ID target tidak ditemukan.";
            }
        }
        ?>
<div class="container mt-5">
    <h2 class="mb-4">Form Edit Target</h2>
    <form id="editTargetForm" action="" method="post">
        <input type="hidden" name="id" value="<?php echo $target['id']; ?>">
        <div class="form-group">
            <label for="bulan">Bulan:</label>
            <input type="text" class="form-control" id="bulan" name="bulan" value="<?php echo $target['bulan']; ?>"
                readonly>
        </div>
        <div class="form-group">
            <label for="tahun">Tahun:</label>
            <input type="text" class="form-control" id="tahun" name="tahun" value="<?php echo $target['tahun']; ?>"
                readonly>
        </div>
        <div class="form-group">
            <label for="aktifitas">Aktifitas:</label>
            <select class="form-control" id="aktifitas" name="aktifitas" required>
                <option value="" disabled selected>Pilih Kegiatan</option>
                <?php
                        $stmt = $pdo->query("SELECT * FROM kegiatan");
                        $daftarKegiatan = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($daftarKegiatan as $kegiatan) {
                            if ($target['aktifitas'] == $kegiatan['kegiatan']) {
                                echo "<option selected value='{$kegiatan['kegiatan']}|{$kegiatan['singkatan']}'> $kegiatan[kegiatan] - $kegiatan[singkatan]</option>";
                            } else {
                                echo "<option  value='{$kegiatan['kegiatan']}|{$kegiatan['singkatan']}'> $kegiatan[kegiatan] - $kegiatan[singkatan]</option>";
                            }
                        }
                        ?>
            </select>
        </div>
        <div class="form-group">
            <label for="target">Target:</label>
            <input type="number" class="form-control" id="target" name="target" value="<?php echo $target['target']; ?>"
                required>
        </div>
        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        <a href="<?= $url . "index.php?menu=index&act=target&submenu=hapus_target&id=" . $target['id'] . "&aktifitas=" . urlencode($target['aktifitas']) . "&singkatan=" . urlencode($target['singkatan']) . "&tahun=" . $target['tahun'] ?>"
            class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus target ini?')">
            Hapus
        </a>
    </form>
</div>
<?php
    } else {

        echo "Data target tidak ditemukan.";
    }
} else {

    echo "ID target tidak ditemukan.";
}
?>