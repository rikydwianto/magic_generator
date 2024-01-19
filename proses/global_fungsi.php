<?php
function haritanggal($tanggal)
{
    $bulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];


    $tanggalArray = explode('-', $tanggal);
    $hariIndex = date('w', strtotime($tanggal));
    $hariIndonesia = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$hariIndex];
    $bulanIndonesia = $bulan[(int)$tanggalArray[1]];
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