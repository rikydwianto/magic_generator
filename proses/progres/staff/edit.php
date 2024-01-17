<h2 class="mb-4">Form Edit Staff</h2>
<?php
try {

    if (isset($_GET['id_staff'])) {

        $id_staff = $_GET['id_staff'];


        $query = "SELECT * FROM staff WHERE id_staff = ? and cabang='$detailAkun[nama_cabang]'";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id_staff]);
        $staff = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($staff) {
?>
<form action="" method="post">
    <input type="hidden" name="id_staff" value="<?php echo $staff['id_staff']; ?>">

    <div class="form-group">
        <label for="nik_staff">NIK Staff:</label>
        <input type="text" class="form-control" id="nik_staff" name="nik_staff"
            pattern="(^\d{6}/\d{4}$)|(^\d{4}/\d{2}/\d{2}$)" value="<?php echo $staff['nik_staff']; ?>" required>
    </div>

    <div class="form-group">
        <label for="nama_staff">Nama Staff:</label>
        <input type="text" class="form-control" id="nama_staff" name="nama_staff"
            value="<?php echo $staff['nama_staff']; ?>" required>
    </div>

    <div class="form-group">
        <label for="cabang">Cabang:</label>
        <input type="text" class="form-control" readonly id="cabang" name="cabang"
            value="<?php echo $staff['cabang']; ?>" required>
    </div>
    <div class="form-group">
        <label for="status">Status:</label>
        <select id="status" class="form-control" name="status">
            <option value="aktif">Aktif</option>
            <option value="tidak_aktif">Tidak Aktif</option>
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

    // Query untuk memperbarui data staff berdasarkan id_staff
    $query = "UPDATE staff SET nik_staff = ?, nama_staff = ?, cabang = ?,status=? WHERE id_staff = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$nik_staff, $nama_staff, $cabang,$status, $id_staff]);
    alert("Update Berhasil");
    pindah(menu_progress("staff/index"));
} else {
    $pesan = "Permintaan tidak valid.";
}

?>