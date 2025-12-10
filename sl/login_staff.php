<?php
require './../vendor/autoload.php'; // Impor library Dotenv
require './../proses/global_fungsi.php';
include_once "./../config/setting.php";
include_once "./../config/koneksi.php";
$url = $url . 'sl/';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }

        .login-container {
            max-width: 400px;
            margin: auto;
            margin-top: 100px;
        }
    </style>
</head>

<body>

    <div class="container login-container">
        <h2 class="text-center mb-4">Login</h2>

        <form method="post">
            <div class="mb-3">
                <label for="username" class="form-label">NIK</label>
                <input type="text" name='username' value="<?= @$_POST['username'] ?>" class="form-control" id="username" placeholder="Enter your username">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name='password' class="form-control" id="password" placeholder="Enter your password">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Login</button>
            <?php

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Mendapatkan nilai dari form
                $username = $_POST['username'];
                $password = $_POST['password'];

                try {
                    // Melakukan query untuk mendapatkan informasi staff
                    $stmt = $pdo->prepare("SELECT * FROM staff WHERE nik_staff = :username AND password = :password and status='aktif'");
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':password', $password);
                    $stmt->execute();

                    // Memeriksa apakah data staff ditemukan
                    if ($stmt->rowCount() > 0) {
                        // Mendapatkan data staff
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);

                        // Menyimpan ID staff ke dalam session
                        $_SESSION['id_staff'] = $row['id_staff'];
                        $_SESSION['nik_staff'] = $row['nik_staff'];

                        // Redirect ke halaman dashboard
                        pindah("$url_sl");
                    } else {
                        // Jika data staff tidak ditemukan, kembali ke halaman login
            ?>
                        <div class="alert alert-danger" role="alert">
                            Username Salah atau akun anda dinonaktifkan!
                        </div>
            <?php
                    }
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            }
            ?>

        </form>
    </div>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Your custom scripts go here -->
    <script>

    </script>

</body>

</html>