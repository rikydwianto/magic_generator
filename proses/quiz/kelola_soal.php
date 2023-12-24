<?php
$id_kuis = $_GET['id_kuis'];
$query = "select * from kuis where id_kuis='$id_kuis'";
$stmt = $pdo->query($query);
$kuis = $stmt->fetch();
if (isset($_GET['del'])) {
    $id_soal = $_GET['id_soal'];
    $qhapussoal = "DELETE FROM soal where id_soal='$id_soal'";
    $hapus = $pdo->query($qhapussoal);
    if ($hapus) {
        pindah($url . "index.php?menu=quiz&act=kelola_soal&id_kuis=" . $id_kuis . "&id_soal=$id_soal ");
    } else {
        echo "gagal hapus data";
    }
}

$qsoal = "select * from soal where id_kuis='$id_kuis'";
$smt = $pdo->query($qsoal);


?>
<div class="container mt-2">
    <h2 class="mb-4">Kelola Soal</h2>
    <table class='table table-bordered'>
        <tr>
            <th>Nama Kuis</th>
            <td><?= $kuis['nama_kuis'] ?></td>
        </tr>
        <tr>
            <th>Pembuat</th>
            <td><?= $kuis['nama_karyawan'] ?></td>
        </tr>
        <tr>
            <th>&nbsp;</th>
            <td>
                <a href="javascript:void(0)" id='openPopupBtn' class="btn btn-lg btn-primary">
                    <i class="fa fa-plus"></i>
                    Tambah Soal
                </a>
            </td>
        </tr>
    </table>
    <hr class='mb-9'>
    <div class="row ">
        <!-- Pertanyaan 1 -->
        <?php
        $no = 1;
        foreach ($smt->fetchAll() as $soal) {
            $json = json_decode($soal['pilihan'], true);
            $jawaban = $soal['jawaban'];

        ?>
            <div class="col-md-12 mb-2">
                <div class="card ">
                    <div class="card-header   d-flex justify-content-between">
                        <h5 class="mb-0">Soal <?= $no ?>. <?= $soal['soal'] ?></h5>
                        <a onclick="return window.confirm('Apakah anda yakin untuk mengapus ini?')" href="<?= $url . "index.php?menu=quiz&act=kelola_soal&del&id_kuis=" . $id_kuis ?>&id_soal=<?= $soal['id_soal'] ?>" class="btn btn-danger btn-sm ">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>

                    <div class="card-body">
                        <p class="card-text">
                        <ul class="list-group">
                            <?php
                            foreach ($json as $pil) {
                                if ($pil['id'] == $soal['jawaban']) {
                            ?>
                                    <li class="list-group-item  "><b><?= strtoupper($pil['id']) ?>. <?= $pil['teks'] ?></b>
                                    </li>
                                <?php
                                } else {
                                ?>
                                    <li class="list-group-item  "><?= strtoupper($pil['id']) ?>. <?= $pil['teks'] ?></li>
                            <?php
                                }
                            }
                            ?>
                        </ul>
                        </p>

                        <!-- Pilihan -->

                        <!-- <p class="mt-3"><strong>Jawaban: <?= $jawaban ?></strong></p> -->
                    </div>
                </div>
            </div>

        <?php
            $no++;
        }
        ?>
        <hr>


    </div>
</div>

</div>
<script>
    // Logika untuk membuka popup saat tombol diklik
    document.getElementById('openPopupBtn').addEventListener('click', function() {
        // Fungsi untuk membuka popup
        openPopup();
    });

    // Fungsi untuk membuka popup
    function openPopup() {
        let id = '<?= $id_kuis ?>'
        // Logic untuk membuka popup (gunakan modal, dialog, atau elemen semacamnya)
        // Contoh sederhana menggunakan window.open untuk tujuan demonstrasi
        var popupWindow = window.open('<?= $url ?>popup_soal.php?id_kuis=' + id, 'Popup', 'width=700,height=700');
        // Set fungsi untuk menangani peristiwa penutupan popup
        popupWindow.onunload = function() {
            // Fungsi untuk merefresh halaman utama
            location.reload();
        };
    }
</script>