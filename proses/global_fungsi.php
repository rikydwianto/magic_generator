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
function jumlah_staff($pdo,$ket,$tgl,$staff,$namacabang){
    $sql  ="SELECT SUM(sisa_saldo) AS balance,sum(perubahan) as turunos FROM deliquency WHERE keterangan='$ket' AND cabang='$namacabang' and staff='$staff' and tgl_input='$tgl' GROUP BY staff";
    $stmt = $pdo->query($sql);
    if($stmt->rowCount()>0){
        $total = $stmt->fetch();
        if($ket=='turunos'){
            return $total['turunos'];

        }
        else{
            return $total['balance'];

        }
    }
    else{
        return 0;
    }
    // return $sql;
}