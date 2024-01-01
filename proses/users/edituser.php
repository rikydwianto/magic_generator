<div class="container mt-5">
    <h2 class="mb-4">Edit User</h2>

    <?php
    try {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
            $id = $_POST["id"];
            $username = $_POST["username"];
            $nik = $_POST["nik"];
            $nama = $_POST["nama"];
            $email = $_POST["email"];
            $jenis_akun = $_POST["jenis_akun"];

            // Update user data in the database
            $stmt = $pdo->prepare("UPDATE users SET username=?, nik=?, nama=?, email=?, jenis_akun=? WHERE id=?");
            $stmt->execute([$username, $nik, $nama, $email, $jenis_akun, $id]);

            pindah($url . "index.php?menu=index&act=users&submenu=lihat_user");
        } else {
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    try {
        // Create a PDO pdoection
        if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
            $id = $_GET["id"];

            // Fetch user details based on the ID
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) : ?>
                <form action="" method="post">
                    <input type="hidden" name="id" value="<?= $user['id'] ?>">

                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?= $user['username'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="nik">NIK:</label>
                        <input type="text" class="form-control" id="nik" name="nik" value="<?= $user['nik'] ?>">
                    </div>

                    <div class="form-group">
                        <label for="nama">Nama:</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?= $user['nama'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= $user['email'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="jenis_akun">Jenis Akun:</label>
                        <select class="form-control" id="jenis_akun" name="jenis_akun">
                            <option value="superuser" <?= ($user['jenis_akun'] === 'superuser') ? 'selected' : '' ?>>Admin</option>
                            <option value="biasa" <?= ($user['jenis_akun'] === 'biasa') ? 'selected' : '' ?>>User</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            <?php else : ?>
                <p>User not found.</p>
        <?php endif;
        }
    } catch (PDOException $e) { ?>
        <p>Error: <?= $e->getMessage() ?></p>
    <?php }

    // Close the pdoection
    $pdo = null;
    ?>

</div>