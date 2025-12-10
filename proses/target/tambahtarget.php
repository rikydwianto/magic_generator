<?php
$daftarTahun = array(2024, 2025);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $tahun = $_POST['tahun'];
    $kegiatan = $_POST['kegiatan'];
    $idUser = $sesi;
    $bulanArray = $_POST['bulan'];
    $targetPerbulanArray = $_POST['targetPerbulan'];
    $selectedKegiatan = $_POST['kegiatan'];
    list($kegiatan, $singkatan) = explode('|', $selectedKegiatan);
    $countBulan = count($bulanArray);
    if ($countBulan > 0) {
        for ($i = 0; $i < $countBulan; $i++) {
            $query = "INSERT INTO `target` (`aktifitas`, `singkatan`, `bulan`, `tahun`, `target`, `id_user`) 
            VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(1, $kegiatan);
            $stmt->bindParam(2, $singkatan);
            $stmt->bindParam(3, $bulanArray[$i]);
            $stmt->bindParam(4, $tahun);
            $stmt->bindParam(5, $targetPerbulanArray[$i]);
            $stmt->bindParam(6, $idUser);
            $stmt->execute();
        }
        pindah($url . "index.php?menu=index&act=target&submenu=tambahtarget");
    } else {
        alert("Gagal : Tidak ada bulan yang ditambahkan\nSilahkan Tambahkan Target perbulannya");
    }
}

@$akt = $_GET['akt'];
@$tahun_url = $_GET['tahun'];

?>
<div class="container mt-5">
    <h2 class="mb-4">Form Input Target</h2>
    <form id="targetForm" method="post" onsubmit="return validateForm()">
        <div class="form-group">
            <label for="tahun">Tahun:</label>
            <select class="form-control" id="tahun" name="tahun" required>
                <!-- <option value="">Pilih Tahun</option> -->
                <?php

                foreach ($daftarTahun as $tahun) {
                    if ($tahun == $tahun_url) {
                        echo "<option selected value='$tahun'>$tahun</option>";
                    } else {
                        echo "<option value='$tahun'>$tahun</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="kegiatan">Kegiatan:</label>
            <select class="form-control" id="kegiatan" name="kegiatan" required>
                <option value="" disabled selected>Pilih Kegiatan</option>
                <?php
                $stmt = $pdo->query("SELECT * FROM kegiatan");
                $daftarKegiatan = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($daftarKegiatan as $kegiatan) {
                    if ($kegiatan['kegiatan'] == $akt) $sel = "selected";
                    else $sel = '';
                    echo "<option $sel value='{$kegiatan['kegiatan']}|{$kegiatan['singkatan']}'> $kegiatan[kegiatan] - $kegiatan[singkatan]</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="idUser">NAMA :</label>
            <input type="text" readonly class="form-control" value='<?= $detailAkun['nama'] ?>' id="idUser" name="idUser" required>
            <input type="hidden" class="form-control" value='<?= $sesi ?>' id="idUser" name="idUser" required>
        </div>
        <?php
        if ($akt == "") {
        ?>
            <button type="button" class="btn btn-primary" onclick="tambahBulan()">Tambah
                Bulan
            </button>
        <?php
        } else {
            $bulan = $_GET['bulan'];
        ?>
            <div class="col-5">
                <label for="bulan">Bulan:</label>
                <select class="form-control" name="bulan[]" id='bulan' required>
                    <option value="<?= $bulan ?>" selected><?= $bulan ?> - <?= $bulanArray[$bulan] ?></option>

                </select>
            </div>
            <div class="col-5">
                <label for="targetPerbulan">Target Perbulan:</label>
                <input type="number" class="form-control" name="targetPerbulan[]" required>
            </div>
        <?php
        }
        ?>



        <div class="target-bulanan" id="targetBulanan">
            <h3>Rincian Target Bulanan</h3>
            <!-- Tempat untuk menambahkan entri bulanan -->
        </div>


        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
</div>

<script>
    const bulanArray = <?php echo json_encode($bulanArray); ?>;

    function tambahBulan() {
        const targetBulanan = document.getElementById('targetBulanan');

        const div = document.createElement('div');
        div.classList.add('form-row', 'mb-2', 'col-lg-12');
        div.innerHTML = `
            <div class="col-5">
                <label for="bulan">Bulan:</label>
                <select class="form-control" name="bulan[]" id='bulan' required>
                    <option value="" disabled selected>Pilih Bulan</option>
                    ${Object.entries(bulanArray).map(([key, value]) => `<option value="${key}"> ${key} - ${value}</option>`).join('')}
                </select>
            </div>
            <div class="col-5">
                <label for="targetPerbulan">Target Perbulan:</label>
                <input type="number" class="form-control" name="targetPerbulan[]" required>
            </div>
            <div class="col-2">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-danger btn-block" onclick="hapusBulan(this)">Hapus Bulan</button>
            </div>
        `;

        targetBulanan.appendChild(div);
    }

    function hapusBulan(button) {
        const targetBulanan = document.getElementById('targetBulanan');
        targetBulanan.removeChild(button.parentElement.parentElement);
    }
</script>