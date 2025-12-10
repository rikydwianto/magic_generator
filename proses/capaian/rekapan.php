<h1>Form Pencarian Data</h1>

<form action="" method="post" class="form-inline">


    <div class="form-group col-lg-4">
        <label for="bulan" class="sr-only">Bulan:</label>
        <select class="form-control" id="bulan" name="bulan" required>
            <?php

            foreach ($bulanArray as $nomor => $bulanOption) {
                if ($nomor == date("m") || $nomor == $_POST['bulan']) {
                    $selbul = "selected";
                } else $selbul = "";
                echo "<option $selbul value=\"$nomor\">$nomor - $bulanOption</option>";
            }
            ?>
        </select>
    </div>

    <div class="form-group  col-lg-4">
        <label for="tahun" class="sr-only">Tahun:</label>
        <select class="form-control" id="tahun" name="tahun" required>
            <option value="2024" selected>2024</option>
            <option value="2024">2025</option>
            <option value="2024">2026</option>
        </select>
    </div>


    <button type="submit" class="btn btn-primary mb-2">Cari Data</button>
</form>
<?php
// Ambil nilai dari formulir

$id_user = $sesi;
if (isset($_POST['bulan'])) {
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
} else {
    $bulan = date("m") + 0;
    $tahun = date("Y");
}

try {
    // Ganti dengan informasi koneksi database Anda

    // Query untuk mencari data berdasarkan input formulir
    $query = "SELECT * FROM
                target t
            WHERE
                t.id_user = :id_user
                AND t.bulan = :bulan
                AND t.tahun = :tahun
                group by t.aktifitas
                
                ";
    // Persiapkan dan eksekusi statement SQL
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    $stmt->bindParam(':bulan', $bulan, PDO::PARAM_STR);
    $stmt->bindParam(':tahun', $tahun, PDO::PARAM_INT);
    $stmt->execute();

    // Tampilkan hasil dalam tabel HTML
?>
    <h2>REKAP CAPAIAN BULAN <?= strtoupper($bulanArray[$bulan]) ?> TAHUN <?= $tahun ?></h2>
    <table class='table table-bordered'>
        <thead>
            <tr>
                <th>NO</th>
                <th>Kegiatan</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Target</th>
                <th>Capaian</th>
                <th>+/-</th>
                <th>Progres(%)</th>
                <th>KET</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $aktifitas = $row['aktifitas'];
                    $query_cari = "SELECT sum(c.capaian) AS capaian_capaian
                FROM
                    capaian c
                WHERE
                    c.id_user = :id_user
                    AND c.bulan = :bulan
                    AND c.tahun = :tahun
                    AND c.aktifitas= :aktifitas
    
                    ";



                    // Persiapkan dan eksekusi statement SQL
                    $stmt1 = $pdo->prepare($query_cari);

                    $stmt1->bindParam(':id_user', $id_user, PDO::PARAM_INT);
                    $stmt1->bindParam(':bulan', $bulan, PDO::PARAM_STR);
                    $stmt1->bindParam(':tahun', $tahun, PDO::PARAM_INT);
                    $stmt1->bindParam(':aktifitas', $aktifitas, PDO::PARAM_STR);
                    $stmt1->execute();
                    $data = $stmt1->fetch(PDO::FETCH_ASSOC);
                    $capaian = $data['capaian_capaian'] ? $data['capaian_capaian'] : 0;
                    $progress = round(($capaian / $row['target']) * 100, 2);
                    $kurang = $capaian - $row['target'];
            ?>
                    <tr class='<?= ($progress >= 100 ? "bg-success text-white" : '') ?>'>
                        <td><?= $no++ ?></td>
                        <td><?= $row['aktifitas'] ?>(<?= $row['singkatan'] ?>)</td>
                        <td><?= $bulanArray[$row['bulan']] ?></td>
                        <td><?= $row['tahun'] ?></td>
                        <td class="text-center"><?= $row['target'] ?></td>
                        <td class="text-center"><?= $capaian ?></td>
                        <td class="text-center"><?= $kurang ?></td>
                        <td class="text-center <?= ($progress >= 100 ? "bg-success text-white" : '') ?>"><?= $progress ?> % </td>
                        <td class="">
                            <?= ($progress >= 100 ? "TERCAPAI" : 'BELUM TERCAPAI') ?></td>
                    </tr>
                <?php
                }
            } else {
                ?>
                <tr>
                    <td class='text-center' colspan="9">Tidak ada data!</td>
                </tr>
            <?php
            }


            ?>
        </tbody>
    </table>
<?php
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} finally {
    // Tutup koneksi
    $pdo = null;
}
