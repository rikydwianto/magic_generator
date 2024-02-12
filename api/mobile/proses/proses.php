<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generateToken($data)
{
    $secretKey = 'satuduatiga'; // Ganti dengan kunci rahasia yang kuat
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600; // Token berlaku selama 1 jam

    $payload = array(
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'data' => $data
    );

    return JWT::encode($payload, $secretKey, 'HS256');
}

function verifyToken($token)
{
    $secretKey = 'satuduatiga'; // Ganti dengan kunci rahasia yang kuat

    try {
        $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
        return $decoded->data;
    } catch (\Exception $e) {
        return false;
    }
}

function loginStaff($pdo, $nikStaff, $password)
{
    // $data = json_decode(file_get_contents('php://input'), true);

    // Mendapatkan NIK dan password

    // Mengecek keberadaan pengguna
    $stmt = $pdo->prepare('SELECT * FROM staff WHERE nik_staff = ? ');
    $stmt->execute([$nikStaff]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $data = array();
    if ($user) {

        if ($user['status'] == 'aktif') {
            if ($user['password'] === $password) {
                $pesan = "LOGIN BERHASIL";
                $status = 'success';
                $data = array('id_staff' => $user['id_staff'], 'nik' => $nikStaff, 'cabang' => $user['cabang']);
            } else {
                $pesan = "NIK ditemukan, Password Salah!";
                $status = 'error';
            }
        } else {
            $pesan = "$nikStaff ditemukan, Akun di Non-Aktifkan!";
            $status = 'error';
        }
    } else {
        $pesan = $nikStaff . ' USER TIDAK DITEMUKAN!';
        $status = 'error';
    }
    echo json_encode(['status' => $status, 'message' => $pesan, 'data' => $data]);
}
function loginAdmin($pdo, $nikStaff, $password)
{
    // $data = json_decode(file_get_contents('php://input'), true);

    // Mendapatkan NIK dan password

    // Mengecek keberadaan pengguna
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? or nik =? ');
    $stmt->execute([$nikStaff, $nikStaff]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $data = array();
    if ($user) {

        if ($user['password'] === $password) {
            $pesan = "LOGIN BERHASIL";
            $status = 'success';
            $data = array('id' => $user['id'], 'nik' => $nikStaff, 'id_cabang' => $user['id_cabang']);
        } else {
            $pesan = "NIK ditemukan, Password Salah!";
            $status = 'error';
        }
    } else {
        $pesan = $nikStaff . ' USER TIDAK DITEMUKAN!';
        $status = 'error';
    }
    echo json_encode(['status' => $status, 'message' => $pesan, 'data' => $data]);
}