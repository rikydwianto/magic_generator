<h2 class="mb-4">GANTI PASSWORD</h2>
<h3>Password kamu default 1sampai9 atau 123456, harus diganti, setelah diganti harus diingat ya</h3>

<?php
try {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
        $id = $_POST["id"];
        $password = $_POST["password"];

        // Update user data in the database
        $stmt = $pdo->prepare("UPDATE staff SET password=? WHERE id_staff=?");
        $stmt->execute([$password, $id]);
        pindah(menu_sl("index"));
    } else {
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

try {
    // Create a PDO pdoection
    $id = $detailAkun["id_staff"];

    // Fetch user details based on the ID
    $stmt = $pdo->prepare("SELECT * FROM staff WHERE id_staff = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) : ?>
<form action="" method="post">
    <input type="hidden" name="id" value="<?= $user['id_staff'] ?>">

    <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" class="form-control" readonly id="username" name="username" value="<?= $user['nik_staff'] ?>"
            required>
    </div>

    <div class="form-group">
        <label for="nik">NIK:</label>
        <input type="text" class="form-control" readonly id="nik" name="nik" value="<?= $user['nik_staff'] ?>">
    </div>

    <div class="form-group">
        <label for="nama">Nama:</label>
        <input type="text" class="form-control" readonly id="nama" name="nama" value="<?= $user['nama_staff'] ?>"
            required>
    </div>


    <div class="form-group">
        <label for="nama">PASSWORD BARU:</label>
        <input type="text" class="form-control" id="nama" name="password" value="" required>
    </div>


    <button type="submit" class="btn btn-primary mt-3">Update</button>
</form>
<?php else : ?>
<p>User not found.</p>
<?php endif;
} catch (PDOException $e) { ?>
<p>Error: <?= $e->getMessage() ?></p>
<?php }

// Close the pdoection
$pdo = null;
?>