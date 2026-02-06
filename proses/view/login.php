<?php
$error_message = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $username = trim($_POST["username"] ?? '');
        $password = trim($_POST["password"] ?? '');

        if ($username === '' || $password === '') {
            $error_message = "Username/NIK dan password wajib diisi.";
        } else {
            $query = $pdo->prepare("SELECT * FROM users WHERE username = :username OR nik = :username");
            $query->bindParam(':username', $username);
            $query->execute();

            $result = $query->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                if ($password == $result["password"]) {
                    $_SESSION["idLogin"] = ($result["id"]);
                    $_SESSION["jenisAkun"] = ($result["jenis_akun"]);
                    $_SESSION["id_cabang"] = ($result["id_cabang"]);
                    $_SESSION["jabatan"] = ($result["jabatan"]);
                    $_SESSION["regional"] = ($result["regional"]);

                    $redirect = $url . "index.php?menu=index";
                    echo "<script>
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Login Berhasil!',
                                text: 'Selamat datang kembali',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(function() {
                                window.location.href = '" . $redirect . "';
                            });
                        } else {
                            window.location.href = '" . $redirect . "';
                        }
                    </script>";
                    exit;
                } else {
                    $error_message = "Password salah! Silakan coba lagi.";
                }
            } else {
                $error_message = "Username atau NIK tidak ditemukan!";
            }
        }
    } catch (PDOException $e) {
        $error_message = "Terjadi kesalahan: " . $e->getMessage();
    }
}
?>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="login-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h2 class="login-title">Selamat Datang</h2>
            <p class="login-subtitle">Silakan masuk dengan akun Anda</p>
        </div>

        <form method="post" id="loginForm">
            <div class="form-group mb-3">
                <label for="username" class="form-label">
                    <i class="fas fa-user me-2"></i>Username atau NIK
                </label>
                <input type="text" class="form-control form-control-lg" name="username" id="username" placeholder="Masukkan username atau NIK" required autofocus>
            </div>

            <div class="form-group mb-4">
                <label for="password" class="form-label">
                    <i class="fas fa-lock me-2"></i>Password
                </label>
                <div class="password-input">
                    <input type="password" class="form-control form-control-lg" name="password" id="password" placeholder="Masukkan password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100 login-btn">
                <i class="fas fa-sign-in-alt me-2"></i>Masuk
            </button>
        </form>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger mt-3 animated fadeIn">
                <i class="fas fa-exclamation-circle me-2"></i><?= $error_message ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>
