<h2 class="mb-4">Form Reset Password Staff</h2>
<?php
try {

    if (isset($_GET['id_staff'])) {

        $id_staff = $_GET['id_staff'];


        $query = "SELECT * FROM staff WHERE id_staff = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id_staff]);
        $staff = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($staff) {
?>
<form action="" method="post">
    <input type="hidden" name="id_staff" value="<?php echo $staff['id_staff']; ?>">

    <div class="form-group">
        <label for="nik_staff">NIK Staff:</label>
        <input type="text" readonly class="form-control" id="nik_staff" name="nik_staff"
            value="<?php echo $staff['nik_staff']; ?>-<?php echo $staff['nama_staff']; ?>" required>
    </div>
    <div class="form-group">
        <label for="password">Password Baru:</label>
        <input type="text" class="form-control" id="password" name="password" value="" required>
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
    $password = $_POST['password'];

    // Query untuk memperbarui data staff berdasarkan id_staff
    $query = "UPDATE staff SET password = ? WHERE id_staff = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$password, $id_staff]);
    alert("Update Berhasil");
    pindah(menu_progress("staff/index"));
} else {
    $pesan = "Permintaan tidak valid.";
}

?>