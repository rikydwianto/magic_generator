<?php
if (isset($_GET['edit'])) {

    if (isset($_POST['edit'])) {

        $idKegiatan = $_POST['id_kegiatan'];
        $namaKegiatanBaru = $_POST['kegiatan_baru'];
        $singkatanBaru = $_POST['singkatan_baru'];


        $sql = "UPDATE kegiatan SET kegiatan = :kegiatan_baru, singkatan = :singkatan_baru WHERE id_kegiatan = :id_kegiatan";


        $stmt = $pdo->prepare($sql);


        $stmt->bindParam(':id_kegiatan', $idKegiatan, PDO::PARAM_INT);
        $stmt->bindParam(':kegiatan_baru', $namaKegiatanBaru, PDO::PARAM_STR);
        $stmt->bindParam(':singkatan_baru', $singkatanBaru, PDO::PARAM_STR);


        if ($stmt->execute()) {

            alert("berhasil di-ubah");
            pindah($url . "index.php?menu=index&act=target&submenu=kegiatan");
        } else {

            echo "Error: " . $stmt->errorInfo()[2];
        }
    }

    $idKegiatan = $_GET['id'];


    $sql = "SELECT * FROM kegiatan WHERE id_kegiatan = :id_kegiatan";


    $stmt = $pdo->prepare($sql);


    $stmt->bindParam(':id_kegiatan', $idKegiatan, PDO::PARAM_INT);


    $stmt->execute();


    $kegiatan = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$kegiatan) {

        exit();
    } else {
?>
        <div class="container mt-5">
            <h2>Edit Kegiatan</h2>
            <form action="" method="post">
                <input type="hidden" name="id_kegiatan" value="<?php echo $kegiatan['id_kegiatan']; ?>">
                <div class="form-group">
                    <label for="kegiatan_baru">Nama Kegiatan Baru:</label>
                    <input type="text" class="form-control" id="kegiatan_baru" name="kegiatan_baru" value="<?php echo $kegiatan['kegiatan']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="singkatan_baru">Singkatan Baru:</label>
                    <input type="text" class="form-control" id="singkatan_baru" name="singkatan_baru" value="<?php echo $kegiatan['singkatan']; ?>" required>
                </div>
                <button type="submit" name="edit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>

    <?php
    }
} else {
    ?>
    <h2 class="mb-4">Form Tambah Kegiatan</h2>
    <div class="col-lg-5">

        <form action="" method="post">
            <div class="form-group">
                <label for="kegiatan">Nama Kegiatan:</label>
                <input type="text" class="form-control" id="kegiatan" name="kegiatan" required>
            </div>
            <div class="form-group">
                <label for="singkatan">Singkatan:</label>
                <input type="text" class="form-control" id="singkatan" name="singkatan" required>
            </div>
            <button type="submit" name='tambah' class="btn btn-primary">Tambah Kegiatan</button>
        </form>
    </div>
<?php
} ?>


<?php
if (isset($_GET['del'])) {

    $idKegiatan = $_GET['id'];


    $sql = "DELETE FROM kegiatan WHERE id_kegiatan = :id_kegiatan";


    $stmt = $pdo->prepare($sql);


    $stmt->bindParam(':id_kegiatan', $idKegiatan, PDO::PARAM_INT);


    if ($stmt->execute()) {

        echo "Berhasil dihapus";
        pindah($url . "index.php?menu=index&act=target&submenu=kegiatan");
    } else {

        echo "Error: " . $stmt->errorInfo()[2];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {

    $namaKegiatan = $_POST['kegiatan'];
    $singkatan = $_POST['singkatan'];


    $sql = "INSERT INTO kegiatan (kegiatan, singkatan) VALUES (:kegiatan, :singkatan)";


    $stmt = $pdo->prepare($sql);


    $stmt->bindParam(':kegiatan', $namaKegiatan, PDO::PARAM_STR);
    $stmt->bindParam(':singkatan', $singkatan, PDO::PARAM_STR);


    if ($stmt->execute()) {

        alert("Berhasil ditambahkan");
        pindah($url . "index.php?menu=index&act=target&submenu=kegiatan");
    } else {

        echo "Error: " . $stmt->errorInfo()[2];
    }
}

?>
<h2 class="mb-4">Daftar Kegiatan</h2>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama Kegiatan</th>
            <th>Singkatan</th>
            <th>Act</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $stmt = $pdo->query("SELECT * FROM kegiatan");
        $daftarKegiatan = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($daftarKegiatan as $kegiatan) {
        ?><tr>
                <td><?= $kegiatan['id_kegiatan'] ?></td>
                <td><?= $kegiatan['kegiatan'] ?></td>
                <td><?= $kegiatan['singkatan'] ?></td>
                <td>
                    <?php
                    if ($kegiatan['wajib'] != 'ya') {
                    ?>
                        <a href="<?= $url . "index.php?menu=index&act=target&submenu=kegiatan&edit&id=" . $kegiatan['id_kegiatan'] ?>" class="btn btn-warning">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a href="<?= $url . "index.php?menu=index&act=target&submenu=kegiatan&del&id=" . $kegiatan['id_kegiatan'] ?>" onclick="return window.confirm('Apakah yakin dengan tindakan ini?')" class="btn btn-danger">
                            <i class="fa fa-times"></i>
                        </a>
                    <?php } ?>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>