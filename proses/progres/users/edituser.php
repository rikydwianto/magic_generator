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
            $regional = $_POST["regional"];
            $jabatan = $_POST["jabatan"];
            $cabang = $_POST["cabang"];

            // Update user data in the database
            $stmt = $pdo->prepare("UPDATE users SET username=?, nik=?, nama=?, email=?, jenis_akun=?,regional=?,jabatan=?,id_cabang=? WHERE id=?");
            $stmt->execute([$username, $nik, $nama, $email, $jenis_akun, $regional, $jabatan, $cabang, $id]);

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
                        <label for="regional">REGIONAL:</label>
                        <select class="form-control" id="regional" name="regional">
                            <option value="">Pilih Regional</option>
                            <?php
                            $sel_reg = "";
                            for ($letter = 'A'; $letter < 'Z'; $letter++) {
                                if ($letter == $user['regional']) $sel_reg = "selected";
                                else $sel_reg = "";
                                echo '<option ' . $sel_reg . ' value="' . $letter . '">Regional ' . $letter . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jabatan">Jabatan:</label>

                        <select class="form-control" id="jabatan" name="jabatan">

                            <option value="">Pilih Jabatan</option>

                            <?php
                            // Loop untuk menampilkan setiap elemen dalam array sebagai opsi
                            foreach ($jabatanOptions_cabang as $jabatan) {
                                if ($jabatan == $user['jabatan']) $sel_jab = "selected";
                                else $sel_jab = "";
                                echo '<option ' . $sel_jab . ' value="' . $jabatan . '">' . $jabatan . '</option>';
                            }
                            ?>
                        </select>

                    </div>
                    <div class="form-group">
                        <label for="cabang">Cabang: </label>

                        <select class="form-control" id="cabang" name="cabang">

                            <option value="">Pilih Cabang</option>
                            <?php
                            $query = "SELECT id_cabang, nama_cabang FROM cabang where regional='$regional'";
                            $result = $pdo->query($query);

                            // Loop untuk menampilkan setiap elemen dalam array sebagai opsi
                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                $select = ($row['id_cabang'] == $user['id_cabang'] ? "selected" : "");
                                echo '<option ' . $select . ' value="' . $row['id_cabang'] . '">' . $row['nama_cabang'] . '</option>';
                            }

                            ?>
                        </select>

                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= $user['email'] ?>">
                    </div>

                    <div class="form-group">
                        <label for="jenis_akun">Jenis Akun:</label>
                        <select class="form-control" id="jenis_akun" name="jenis_akun">
                            <option value="biasa" <?= ($user['jenis_akun'] === 'biasa') ? 'selected' : '' ?>>User</option>
                        </select>
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