<h2 class="mb-4">Form Input Staff Lapang Cabang <?= $detailAkun['nama_cabang'] ?></h2>
<form action="" method="post">
    <div class="form-group">
        <label for="nik_staff">NIK Staff:</label>
        <input type="text" class="form-control" id="nik_staff" name="nik_staff"
            pattern="(^\d{6}/\d{4}$)|(^\d{4}/\d{2}/\d{2}$)" required>
    </div>

    <div class="form-group">
        <label for="nama_staff">Nama Staff:</label>
        <input type="text" class="form-control" id="nama_staff" name="nama_staff" required>
    </div>

    <div class="form-group">
        <label for="cabang">Cabang:</label>
        <input type="text" class="form-control" value='<?= $detailAkun['nama_cabang'] ?>' readonly id="cabang"
            name="cabang" required>
    </div>

    <div class="form-group">
        <label for="password">Password:</label>
        <input type="text" class="form-control" value='1sampai9' id="password" name="password" required>
    </div>
    <div class="form-group">
        <label for="status">Status:</label>
        <select id="status" class="form-control" name="status">
            <option value="aktif">Aktif</option>
            <option value="tidak_aktif">Tidak Aktif</option>
        </select>
        <br><br>
    </div>

    <button type="submit" class="btn btn-primary">Simpan Data</button>
</form>

<?php
try {


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nik_staff = $_POST['nik_staff'];
        $nama_staff = $_POST['nama_staff'];
        $password = $_POST['password'];
        $status = $_POST['status'];
        $cabang = $detailAkun['nama_cabang'];

        // Query untuk mengecek apakah data sudah ada
        $cekQuery = "SELECT COUNT(*) FROM staff WHERE nik_staff = ? AND cabang = ?";
        $cekStmt = $pdo->prepare($cekQuery);
        $cekStmt->execute([$nik_staff, $cabang]);
        $jumlahData = $cekStmt->fetchColumn();
        if ($jumlahData > 0) {
?>
<div class="alert alert-danger" role="alert">
    <?php echo "Data tidak diinput karena Staff dengan NIK $nik_staff dan cabang $cabang sudah ada.";; ?>
</div>
<?php

        } else {
            // Data belum ada, lakukan INSERT
            $insertQuery = "INSERT INTO staff (nik_staff, nama_staff, cabang, password,status) VALUES (?, ?, ?, ?,?)";
            $insertStmt = $pdo->prepare($insertQuery);
            $insertStmt->execute([$nik_staff, $nama_staff, $cabang, $password,$status]);

            echo "Data Staff berhasil disimpan.";
            pindah(menu_progress("staff/index"));
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}


?>