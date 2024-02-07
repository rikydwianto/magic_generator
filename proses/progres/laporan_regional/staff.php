<h1>Laporan Per Staff</h1>

<select class="form-control" id="filtercabang" name="cabang" required>

    <option value="">Pilih Cabang</option>
    <?php
    $query = "SELECT * FROM cabang where regional='$regional' order by kode_cabang asc";
    $result = $pdo->query($query);

    // Loop untuk menampilkan setiap elemen dalam array sebagai opsi
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $selcab = $row['nama_cabang'] == $_GET['cabang'] ? "selected" : "";
        echo '<option ' . $selcab . ' value="' . $row['nama_cabang'] . '">' . $row['kode_cabang'] . " - " . $row['nama_cabang'] .  " - " . $row['wilayah'] . '</option>';
    }

    ?>
</select>

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
        $staff = $pdo->prepare("select * from staff where cabang in (select nama_cabang from cabang where regional=?) and status='aktif' order by cabang,nama_staff ");
        $staff->execute([$regional]);
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