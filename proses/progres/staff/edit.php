<h2 class="mb-4">Form Edit Staff</h2>
<?php
try {

    if (isset($_GET['id_staff'])) {

        $id_staff = $_GET['id_staff'];


        $query = "SELECT * FROM staff WHERE id_staff = ? ";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id_staff]);
        $staff = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($staff) {
            $nik = $staff['nik_staff'];
            $regex = '/^\d{4}\/\d{2}\/\d{2}$/';

            if (preg_match($regex, $nik)) {
                $readonly = '';
                $edit_nik = 'ya';
            } else {
                $readonly = "readonly";
            }

?>
            <form action="" method="post">
                <input type="hidden" name="id_staff" value="<?php echo $staff['id_staff']; ?>">

                <div class="form-group">
                    <label for="nik_staff">NIK Staff:</label>
                    <input type="text" class="form-control" id="nik_staff" name="nik_staff" pattern="(^\d{6}/\d{4}$)|(^\d{4}/\d{2}/\d{2}$)" <?= $readonly ?> value="<?php echo $staff['nik_staff']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="nama_staff">Nama Staff:</label>
                    <input type="text" class="form-control" id="nama_staff" name="nama_staff" value="<?php echo $staff['nama_staff']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="cabang">Cabang:</label>
                    <select class="form-control" id="cabang" name="cabang" required>
                        <?php
                        $query = "SELECT * FROM cabang where regional='$regional' order by kode_cabang asc";
                        $result = $pdo->query($query);

                        // Loop untuk menampilkan setiap elemen dalam array sebagai opsi
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            $selcab = $row['nama_cabang'] == $staff['cabang'] ? "selected" : "";
                            echo '<option ' . $selcab . ' value="' . $row['nama_cabang'] . '">' . $row['kode_cabang'] . " - " . $row['nama_cabang'] .  " - " . $row['wilayah'] . '</option>';
                        }

                        ?>
                    </select>
                    <!-- <input type="text" class="form-control" readonly id="cabang" name="cabang"
            value="<?php echo $staff['cabang']; ?>" required> -->
                </div>
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" class="form-control" name="status">
                        <option value="aktif" <?php echo $staff['status'] === "aktif" ? "selected" : ""; ?>>Aktif</option>
                        <option value="tidak_aktif" <?php echo $staff['status'] === "tidak_aktif" ? "selected" : ""; ?>>Tidak Aktif
                        </option>
                    </select>
                    <br><br>
                </div>


                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
<?php
        } else {
            pindah(menu_progress("staff/index"));
        }
    } else {
        pindah(menu_progress("staff/index"));
        echo "ID Staff tidak valid.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari formulir
    $id_staff = $_POST['id_staff'];
    $nik_staff = $_POST['nik_staff'];
    $nama_staff = $_POST['nama_staff'];
    $cabang = $_POST['cabang'];
    $status = $_POST['status'];
    if ($edit_nik == 'ya') {
        $query = "UPDATE capaian_staff SET nik_staff = ? WHERE nik_staff = ?";
        $stmtEdit = $pdo->prepare($query);
        $stmtEdit->execute([$nik_staff, $nik_staff]);
    }

    // Query untuk memperbarui data staff berdasarkan id_staff
    $query = "UPDATE staff SET nik_staff = ?, nama_staff = ?, cabang = ?,status=? WHERE id_staff = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$nik_staff, $nama_staff, $cabang, $status, $id_staff]);
    alert("Update Berhasil");
    pindah(menu_progress("staff/index"));
} else {
    $pesan = "Permintaan tidak valid.";
}

?>