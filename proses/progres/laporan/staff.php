<h2>Laporan Per Staff</h2>
<hr>


<table class='table' id='cabang'>
    <thead>
        <tr>
            <th>NO</th>
            <th>CABANG</th>
            <th>NIK</th>
            <th>NAMA</th>
            <th>LIHAT</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $cabang = $detailAkun['nama_cabang'];
        $staff = $pdo->prepare("select * from staff where cabang in (select nama_cabang from cabang where regional=?) and status='aktif' and cabang=? order by cabang,nama_staff ");
        $staff->execute([$regional, $cabang]);
        $staff = $staff->fetchAll();
        foreach ($staff as $row) {
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $row['cabang'] ?></td>
            <td><?= $row['nik_staff'] ?></td>
            <td><?= $row['nama_staff'] ?></td>
            <td>
                <a href="javascript:bukaCapaian('<?= $row['nik_staff'] ?>')" class="btn btn-success btn-sm">Detail</a>
            </td>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<script>
function bukaCapaian(nik) {
    // Membuka tab baru
    window.open('popup_detail_capaian.php?nik=' + nik, '_blank', 'width=800,height=600');
}
</script>