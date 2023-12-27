<h2>Silakan Masukan kode akses</h2>
<form method="post">
    <div class="col-md-3">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" name="password" required>
        </div>

        <!-- CSRF Token -->

        <button type="submit" class="btn btn-primary">Login</button>

    </div>
</form>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai jawaban dari formulir
    // Validasi CSRF token
    // Ambil data dari formulir login
    try {

        // Ambil data dari formulir login
        $username = filter_var($_POST["username"], FILTER_SANITIZE_STRING);
        $password = filter_var($_POST["password"], FILTER_SANITIZE_STRING);
        // Query untuk mencari pengguna berdasarkan username
        $query = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $query->bindParam(':username', $username);
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // Verifikasi password
            if ($password == $result["password"]) {
                // Password benar, set session dan arahkan ke halaman selamat datang
                $_SESSION["idLogin"] = ($result["id"]);
                pindah($url . "index.php?menu=index");
            } else {
                // Password salah, arahkan kembali ke halaman login
                echo "Password SALAH!";
            }
        } else {
            // Pengguna tidak ditemukan, arahkan kembali ke halaman login
            echo "AKUN TIDAK DITEMUKAN!!";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>