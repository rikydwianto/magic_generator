<div class="container mt-5">
    <h2 class="mb-4">RESET PASSWORD</h2>

    <?php
    try {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
            $id = $_POST["id"];
            $password = $_POST["password"];

            // Update user data in the database
            $stmt = $pdo->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->execute([$password, $id]);

            pindah(menu_progress("users/index"));
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
                        <input type="text" class="form-control" readonly id="username" name="username" value="<?= $user['username'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="nik">NIK:</label>
                        <input type="text" class="form-control" readonly id="nik" name="nik" value="<?= $user['nik'] ?>">
                    </div>

                    <div class="form-group">
                        <label for="nama">Nama:</label>
                        <input type="text" class="form-control" readonly id="nama" name="nama" value="<?= $user['nama'] ?>" required>
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
        }
    } catch (PDOException $e) { ?>
        <p>Error: <?= $e->getMessage() ?></p>
    <?php }

    // Close the pdoection
    $pdo = null;
    ?>

</div>