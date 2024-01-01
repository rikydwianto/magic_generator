<div class="container mt-5">
    <h2 class="mb-4">Tambah User</h2>

    <?php
    if (isset($_POST['tmb_user'])) {

        try {
            // Get data from the form
            $username = $_POST['username'];
            $password = $_POST['password'];
            $nik = $_POST['nik'];
            $nama = $_POST['nama'];
            $email = $_POST['email'];
            $jenis_akun = $_POST['jenis_akun'];

            // Check if the username or NIK already exists
            $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE username = ? OR nik = ?");
            $stmtCheck->execute([$username, $nik]);

            if ($stmtCheck->rowCount() > 0) {
                echo "Error: Username or NIK already exists. Please choose a different one.";
            } else {
                // Prepare the SQL statement for user insertion
                $stmtInsert = $pdo->prepare("INSERT INTO users (username, password, nik, nama, email, jenis_akun) VALUES (?, ?, ?, ?, ?, ?)");
                $stmtInsert->bindParam(1, $username);
                $stmtInsert->bindParam(2, $password);
                $stmtInsert->bindParam(3, $nik);
                $stmtInsert->bindParam(4, $nama);
                $stmtInsert->bindParam(5, $email);
                $stmtInsert->bindParam(6, $jenis_akun);

                // Execute the user insertion statement
                $stmtInsert->execute();

                echo "User added successfully.";
                pindah($url . "index.php?menu=index&act=users&submenu=lihat_user");
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    ?>
    <form action="" method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="nik">NIK:</label>
            <input type="text" class="form-control" id="nik" name="nik">
        </div>

        <div class="form-group">
            <label for="nama">Nama:</label>
            <input type="text" class="form-control" id="nama" name="nama" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email">
        </div>

        <div class="form-group">
            <label>Jenis Akun:</label>

            <div class="form-check">
                <input type="radio" class="form-check-input" checked id="biasa" name="jenis_akun" value="biasa" required>
                <label class="form-check-label" for="biasa">Biasa</label>
            </div>
            <div class="form-check">
                <input type="radio" class="form-check-input" id="superuser" name="jenis_akun" value="superuser" required>
                <label class="form-check-label" for="superuser">Superuser</label>
            </div>
        </div>


        <button type="submit" name='tmb_user' class="btn btn-primary">Submit</button>
    </form>
</div>