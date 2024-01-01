<?php
function haritanggal($tanggal)
{
    $bulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    // Ubah format tanggal ke dalam format 'Hari, DD Nama_Bulan TTTT'
    $tanggalArray = explode('-', $tanggal);
    $hariIndex = date('w', strtotime($tanggal));
    $hariIndonesia = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$hariIndex];
    $bulanIndonesia = $bulan[(int)$tanggalArray[1]]; // -1 karena indeks bulan dimulai dari 0
    $tanggalIndonesia = date('d', strtotime($tanggal));
    $tahunIndonesia = date('Y', strtotime($tanggal));

    return "$hariIndonesia, $tanggalIndonesia $bulanIndonesia $tahunIndonesia";
}

function ganti_karakter($text)
{
    return   preg_replace('/[^a-zA-Z0-9()_ .,"\'\\-;]/', '', $text);
}
function no_center($no)
{
    return sprintf("%03d", $no);
}
function ganti_karakter1($text)
{
    return   preg_replace('/[^a-zA-Z0-9()_ .,"\'-;]/', '', $text);
}


function rupiah($angka)
{
    $hasil = "Rp. " . number_format($angka, 0, ',', '.');
    return $hasil;
}

function alert($isi)
{
?>
    <script>
        alert('<?php echo $isi ?>')
    </script>

<?php
}
function pindah($url)
{
?>
    <script>
        window.location.href = "<?php echo $url ?>";
    </script>
<?php

}
function tutupWindow()
{
?>
    <script>
        window.close()
    </script>
<?php

}
function jumlah_staff($pdo, $ket, $tgl, $staff, $namacabang)
{
    $sql  = "SELECT SUM(sisa_saldo) AS balance,sum(perubahan) as turunos FROM deliquency WHERE keterangan='$ket' AND cabang='$namacabang' and staff='$staff' and tgl_input='$tgl' GROUP BY staff";
    $stmt = $pdo->query($sql);
    if ($stmt->rowCount() > 0) {
        $total = $stmt->fetch();
        if ($ket == 'turunos') {
            return $total['turunos'];
        } else {
            return $total['balance'];
        }
    } else {
        return 0;
    }
    // return $sql;
}

function encodeId($sessionId)
{
    // Gunakan algoritma hash yang kuat, contoh: SHA-256
    $hashedSessionId = hash('sha256', $sessionId);
    return base64_encode($sessionId);
}

function decodeId($encodedSessionId)
{
    $hashedSessionId = base64_decode($encodedSessionId);

    // Pastikan bahwa hasil decode adalah string
    if (!is_string($hashedSessionId)) {
        return false;
    }

    // Verifikasi panjang hash sesuai dengan algoritma yang digunakan
    if (strlen($hashedSessionId) !== 64) {
        return false;
    }

    // Tambahan verifikasi keamanan sesuai kebutuhan

    return base64_decode($encodedSessionId);
}

function getTeksById($jsonArray, $targetId)
{
    foreach ($jsonArray as $item) {
        if ($item['id'] === $targetId) {
            return $item['teks'];
        }
    }
    return null; // Return null if id is not found
}

function hitung($pdo, $id_kuis, $id_soal, $ket)
{
    $q = "SELECT COUNT(keterangan) as total,keterangan FROM soal_jawab WHERE id_kuis=$id_kuis AND id_soal=$id_soal and keterangan='$ket' GROUP BY keterangan";
    $stm = $pdo->query($q);
    $stm = $stm->fetch();
    return ($stm['total'] ? $stm['total'] : 0);
    // return $q;
}
function validateInput($input)
{
    // Gunakan mysqli_real_escape_string untuk sanitasi
    // $sanitizedInput = mysqli_real_escape_string($pdo, $input);

    // Misalnya, Anda juga dapat menambahkan validasi lain sesuai kebutuhan
    // seperti memeriksa pola atau memastikan input hanya terdiri dari karakter tertentu.

    return $input;
}
