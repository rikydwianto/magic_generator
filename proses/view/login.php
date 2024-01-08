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



    try {


        $username = $_POST["username"];
        $password = $_POST["password"];

        $query = $pdo->prepare("SELECT * FROM users WHERE username = :username or nik = :username");
        $query->bindParam(':username', $username);
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {

            if ($password == $result["password"]) {

                $_SESSION["idLogin"] = ($result["id"]);
                $_SESSION["jenisAkun"] = ($result["jenis_akun"]);
                pindah($url . "index.php?menu=index");
            } else {

                echo "Password SALAH!";
            }
        } else {

            echo "AKUN TIDAK DITEMUKAN!!";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>