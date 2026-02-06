<?php
function rubahkata($text)
{
    // Daftar kata-kata terlarang
    $badwords = [
        "poker",
        "blackjack",
        "roulette",
        "baccarat",
        "slot",
        "togel",
        "sicbo",
        "dominoqq",
        "capsa",
        "jackpot",
        "taruhan",
        "betting",
        "bet",
        "odd",
        "parlay",
        "pasang taruhan",
        "gambling",
        "bookie",
        "bandar",
        "taruhan bola",
        "mix parlay",
        "odds",
        "handicap",
        "kasino",
        "online casino",
        "situs judi",
        "agen judi",
        "bandar online",
        "chip gratis",
        "deposit judi",
        "withdraw judi",
        "freebet",
        "bonus deposit",
        "promo slot",
        "putaran gratis",
        "jackpot besar",
        "daftar sekarang",
        "link alternatif",
        "bermain sekarang",
        "spin gratis",
        "judi",
        "taruhan",
        "dadu",
        "lotre",
        "undian",
        "nomor togel",
        "menebak angka",
        "main kartu",
        "bandar togel",
        "menang besar",
        "cuan judi",
        "gacor",
        "slot gacor",
        "pola slot",
        "maxwin",
        "auto win",
        "pola gacor",
        "jackpot gacor",
        "scatter",
        "spin",
        "winrate",
        "modal kecil",
        "anti rungkat",
        "akun pro",
        "akun hoki",
        "slot anti rungkat",
        "deposit kecil",
        "tanpa modal",
        "bonus besar",
        "free spin",
        "auto jackpot",
        "deposit pulsa",
        "tanpa potongan",
        "garansi menang",
        "bonus rollingan",
        "bonus cashback",
        "kode referral",
        "event spesial",
        "hoki besar",
        "no limit",
        "link gacor",
        "akun gacor",
        "buyspin",
        "turbo spin",
        "slot demo",
        "akun rtp tinggi",
        "rtp live",
        "room slot",
        "jam hoki",
        "turn over",
        "win streak",
        "turbo win",
        "hoki slot"
    ];

    // Loop through badwords dan ganti dengan string kosong
    foreach ($badwords as $word) {
        $text = str_ireplace($word, '', $text);
    }

    return $text;
}
function haritanggal($tanggal)
{
    $bulan = [
        '',
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];


    $tanggalArray = explode('-', $tanggal);
    $hariIndex = date('w', strtotime($tanggal));
    $hariIndonesia = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$hariIndex];
    $bulanIndonesia = $bulan[(int)$tanggalArray[1]];
    $tanggalIndonesia = date('d', strtotime($tanggal));
    $tahunIndonesia = date('Y', strtotime($tanggal));

    return "$hariIndonesia, $tanggalIndonesia $bulanIndonesia $tahunIndonesia";
}

function aman($teks)
{
    if ($teks === null || $teks === '') {
        return '';
    }
    return preg_replace("/'/", "-", $teks);
}

function ganti_karakter($text)
{
    if ($text === null || $text === '') {
        return '';
    }
    return preg_replace('/[^a-zA-Z0-9()_ .,"\'\\-;]/', '', $text);
}
function no_center($no)
{
    return sprintf("%03d", $no);
}
function ganti_karakter1($text)
{
    if ($text === null || $text === '') {
        return '';
    }
    return preg_replace('/[^a-zA-Z0-9()_ .,"\'-;]/', '', $text);
}


function rupiah($angka)
{
    $hasil = "Rp. " . number_format($angka, 0, ',', '.');
    return $hasil;
}
function angka($angka)
{
    $hasil = number_format($angka, 0, ',', '.');
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
    $sql  = "SELECT SUM(sisa_saldo) AS balance,sum(perubahan) as turunos FROM deliquency_regional WHERE keterangan='$ket' AND regional='$namacabang' and staff='$staff' and tgl_input='$tgl' GROUP BY staff";
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
}

function jumlah_staff_cab($pdo, $ket, $tgl, $staff, $namacabang)
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
}

function encodeId($sessionId)
{

    $hashedSessionId = hash('sha256', $sessionId);
    return base64_encode($sessionId);
}

function decodeId($encodedSessionId)
{
    $hashedSessionId = base64_decode($encodedSessionId);


    if (!is_string($hashedSessionId)) {
        return false;
    }


    if (strlen($hashedSessionId) !== 64) {
        return false;
    }



    return base64_decode($encodedSessionId);
}

function getTeksById($jsonArray, $targetId)
{
    foreach ($jsonArray as $item) {
        if ($item['id'] === $targetId) {
            return $item['teks'];
        }
    }
    return null;
}

function hitung($pdo, $id_kuis, $id_soal, $ket)
{
    $q = "SELECT COUNT(sj.keterangan) as total,sj.keterangan FROM soal_jawab sj join kuis_jawab kj on kj.id_jawab=sj.id_jawab WHERE sj.id_kuis=$id_kuis AND sj.id_soal=$id_soal and sj.keterangan='$ket' GROUP BY sj.keterangan";
    $stm = $pdo->query($q);
    $stm = $stm->fetch();
    return ($stm['total'] ? $stm['total'] : 0);
}
function validateInput($input)
{

    return $input;
}
$bulanArray = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember',
];
$jabatanOptions = array('Staff Lapang', 'MIS', 'Manager', 'Regional');
$jabatanOptions_cabang = array('MIS', 'Manager');

function menu_progress($menu)
{
    $url = $GLOBALS['url'];
    return $url . "progress.php?menu=$menu";
}

function menu_sl($menu)
{
    $url = $GLOBALS['url'];

    return $url . "index.php?menu=$menu";
}
function pesan($teks, $warna = 'success')
{
?>
    <div class="alert alert-<?= $warna ?>" role="alert">
        <?= $teks ?>
    </div>
<?php
}
function badge($teks, $warna = 'success')
{
?>
    <span class="badge text-bg-<?= $warna ?>"><?= $teks ?></span>

<?php
}
function removeNonNumeric($input)
{
    if ($input === null || $input === '') {
        return '';
    }
    // Hapus karakter selain angka
    $numericOnly = preg_replace("/[^0-9]/", "", $input);

    return $numericOnly;
}
function formatNumber($number)
{
    return number_format($number, 0, ',', '.');
}
function hitungTotalPinjaman($dataJSON)
{
    // Mengonversi string JSON menjadi array PHP
    $pinjamanArray = json_decode($dataJSON, true);

    // Menghitung total
    $totalPinjaman = array_sum($pinjamanArray);

    return $totalPinjaman;
}
function proper($string)
{
    return ucwords(strtolower($string));
}



$pinjamanArray = [
    "PMB" => "Mikro Bisnis",
    "PSA" => "Sanitasi",
    "PPD" => "Pendidikan",
    "PRR" => "Renovasi Rumah",
    "ARTA" => "Alat Rumah Tangga"
];



function warnaPlusMinus($nilai)
{
    if ($nilai < 0) {
        $warna = "text-danger"; // Merah untuk nilai negatif
    } elseif ($nilai > 0) {
        $warna = "text-success"; // Hijau untuk nilai positif
    } else {
        $warna = "text-black"; // Hitam untuk nilai nol
    }

    return $warna;
}

function warnaPlusMinusPar($nilai)
{
    if ($nilai < 0) {
        $warna = "text-success"; // Merah untuk nilai negatif
    } elseif ($nilai > 0) {
        $warna = "text-danger"; // Hijau untuk nilai positif
    } else {
        $warna = "text-black"; // Hitam untuk nilai nol
    }

    return $warna;
}

function getTotalMinggu($field, $cabang, $minggu, $bulan, $tahun)
{
    $pdo = $GLOBALS['pdo'];
    try {
        // Query untuk mendapatkan total nett anggota
        $query = "SELECT SUM($field) AS hasil
                  FROM capaian_staff cs
                  INNER JOIN detail_capaian_staff dcs
                  ON dcs.id_capaian_staff = cs.id_capaian_staff
                  WHERE cs.cabang_staff = :cabang
                  AND cs.minggu = :minggu
                  AND cs.bulan = :bulan
                  AND cs.tahun = :tahun
                  AND cs.status = 'approve'";

        $stmt = $pdo->prepare($query);
        // $stmt->bindParam(':field', $field);
        $stmt->bindParam(':cabang', $cabang);
        $stmt->bindParam(':minggu', $minggu);
        $stmt->bindParam(':bulan', $bulan);
        $stmt->bindParam(':tahun', $tahun);
        $stmt->execute();

        // Mengambil hasil query
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Menutup koneksi database
        $pdo = null;

        return $result['hasil'] ? $result['hasil'] : 0;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

function hitungSelisihWaktu($tanggalAwal, $tanggalAkhir)
{
    $dateTimeAwal = new DateTime($tanggalAwal);
    $dateTimeAkhir = new DateTime($tanggalAkhir);

    $selisih = $dateTimeAwal->diff($dateTimeAkhir);

    // Menghitung total detik dari selisih waktu
    $totalDetik = $selisih->s + ($selisih->i * 60) + ($selisih->h * 3600) + ($selisih->d * 86400);

    // Mengonversi total detik ke format jam:menit:detik
    $jam = floor($totalDetik / 3600);
    $menit = floor(($totalDetik % 3600) / 60);
    $detik = $totalDetik % 60;

    return sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
}

function getImageTypeFromUrl($imageUrl)
{
    // Ambil informasi gambar menggunakan getimagesize
    $imageInfo = @getimagesize($imageUrl);

    if ($imageInfo === false) {
        // Gagal mendapatkan informasi gambar
        return false;
    }

    // Ambil tipe MIME dari informasi gambar
    $imageType = $imageInfo['mime'];

    return $imageType;
}


function cekGambarSoal($url_api, $id_soal, $ket)
{
    $apiUrl = $url_api . 'gambar_soal.php';  // Gantilah dengan URL API yang sesuai
    $apiUrl .= "?id=$id_soal&ket=$ket";


    // Membuat data yang akan dikirimkan ke API
    $data = array(
        'id' => $id_soal,
        'ket' => $ket
    );

    // Menginisialisasi cURL
    $ch = curl_init();

    // Mengatur opsi cURL
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    // Menjalankan cURL dan mendapatkan hasil
    $result = curl_exec($ch);

    // Menutup sesi cURL
    curl_close($ch);

    // Mengembalikan hasil dari API dalam bentuk array
    $hasil =  json_decode($result, true);
    return $hasil['hasil'];
}
function angka_mentah($angka)
{
    if ($angka == 0 || $angka == '') {
        return 0;
    } else {
        $angka1 =  str_replace(",", "", $angka);
        $angka1 = (int)$angka1;
        return $angka1;
    }
}

function sendMessageFCM($title, $body, $token, $customData = '')
{
    $serverKey = 'AAAA-hYTrAY:APA91bFWry4fUr-5Opw1dGfcpiWHohDeu-Op5K6xajn1DZL4XPNj1Te6IVNCDwlRJYWsKoq3FL_9qORWAMXzvL9GZiFOLt7A_BR597O61nwtgCxprHJflL7DZ-hb4iLElWX0w2SogLGo';
    //106672304548785949555
    $url = 'https://fcm.googleapis.com/fcm/send';

    $notification = [
        'title' => $title,
        'body' => $body,
    ];

    $data = [
        'notification' => $notification,
        'to' => $token,
        'data' => ($customData), // Data tambahan disertakan di sini
    ];

    $headers = [
        'Authorization: Bearer ' . $serverKey,
        'Content-Type: application/json',
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Hanya gunakan di lingkungan pengembangan, jangan di produksi

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);

    if ($response === false) {
        die('Error: ' . curl_error($ch));
    }

    curl_close($ch);

    return $response;
}
