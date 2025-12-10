<h2>Selamat Datang </h2>
<hr>
<?php
// Simulasi data pengguna (gantilah dengan data sesuai kebutuhan)
$pengguna = $detailAkun;

?>

<!-- Struktur HTML untuk menampilkan informasi di dashboard dengan Bootstrap -->
<div class=" mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body ">
                    <h5 class="card-title"><?php echo $pengguna['nama']; ?></h5>
                    <p class="card-text">Jabatan : <?php echo $pengguna['jabatan']; ?></p>
                    <p class="card-text">NIK : <?php echo $pengguna['nik']; ?></p>
                    <p class="card-text">Username : <?php echo $pengguna['username']; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Informasi Pengguna</h5>
                    <p class="card-text"><i class="fas fa-envelope"></i> <strong>Email:</strong>
                        <?php echo $pengguna['email']; ?></p>
                    <p class="card-text"><i class="fas fa-globe"></i> <strong>Regional:</strong>
                        <?php echo $pengguna['regional']; ?></p>
                    <p class="card-text"><i class="fas fa-building"></i> <strong>Cabang:</strong>
                        <?php echo $pengguna['nama_cabang']; ?> - <?php echo $pengguna['kode_cabang']; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>