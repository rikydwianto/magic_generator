<?php
require 'vendor/autoload.php'; // Impor library Dotenv
require 'proses/global_fungsi.php';
include_once "./config/setting.php";
include_once "./config/koneksi.php";
@$sesi = $_SESSION['idLogin'];

if ($sesi == '' || $sesi == null) {
    tutupWindow();
}
$id_kuis = $_GET['id_kuis'];
$query = "SELECT * FROM kuis  WHERE  id_kuis='$id_kuis'";
$stmt = $pdo->query($query);
$kuis = $stmt->fetch(); ?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.0/css/buttons.dataTables.min.css">
    <!-- //cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>TAMBAH SOAL <?= $kuis['nama_kuis'] ?></title>
</head>

<body>


    <div class="row m-2">
        <div class="container">

            <h2>Detail Jawaban</h2>



            <table class='table table-bordered'>
                <tr>
                    <th>Nama Kuis</th>
                    <td><?= $kuis['nama_kuis'] ?></td>
                </tr>
                <tr>
                    <th>Nama</th>
                    <td><?= $kuis['nama_karyawan'] ?></td>
                </tr>

            </table>
            <hr>
            <div class="row">
                <h1 class="text-center ">Tambah Soal</h1>
                <div class="col-md-4 float-right">
                    <div class="mb-3">
                        <label for="kategoriFilter" class="form-label">Filter Kategori:</label>
                        <select id="kategoriFilter" class="form-select">
                            <option value="">Semua</option>
                            <?php
                            $query = "SELECT DISTINCT kategori FROM soal_bank";
                            $stmt = $pdo->query($query);

                            // Menampilkan opsi dropdown kategori
                            while ($row = $stmt->fetch()) {
                                echo '<option value="' . $row['kategori'] . '">' . $row['kategori'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="subkategoriFilter" class="form-label">Filter Sub Kategori:</label>
                        <select id="subkategoriFilter" class="form-select">
                            <option value="">Semua</option>
                            <?php
                            $query = "SELECT DISTINCT sub_kategori,kategori FROM soal_bank";
                            $stmt = $pdo->query($query);

                            // Menampilkan semua opsi subkategori
                            while ($row = $stmt->fetch()) {
                                echo '<option  value="' . $row['sub_kategori'] . '">' . $row['sub_kategori'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>


                <table id='soalTable' class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Soal</th>
                            <th>Gambar</th>
                            <th>Pilihan</th>
                            <th>Kategori</th>
                            <th>Sub Kategori</th>

                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $qsoal = "SELECT * FROM soal_bank WHERE id_soal  NOT IN (SELECT id_bank_soal FROM soal WHERE id_kuis='$id_kuis')";
                        $no = 1;
                        $stmt = $pdo->query($qsoal);

                        foreach ($stmt->fetchAll() as $row) {
                        ?>
                            <tr>
                                <td><?= $no ?></td>
                                <td><?= $row['soal'] ?></td>
                                <td>
                                    <?php
                                    if ($row['url_gambar'] != "") {


                                        $gambar = cekGambarSoal($url_api, $row['id_soal'], 'soal_bank');
                                        if ($gambar != "") {
                                    ?>
                                            <img src="<?= $gambar['url_gambar'] ?>" style="width: 300px;" class="img">
                                    <?php
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $pilihan = json_decode($row['pilihan'], true);
                                    foreach ($pilihan as $pil) {
                                        if ($pil['id'] == $row['jawaban']) {
                                            echo "<b>" . strtoupper($pil['id']) . ". " . ($pil['teks']) . "</b><br/>";
                                        } else {
                                            echo strtoupper($pil['id']) . ". " . ($pil['teks']) . "<br/>";
                                        }
                                    }
                                    ?>
                                </td>
                                <td><?= $row['kategori'] ?></td>
                                <td><?= $row['sub_kategori'] ?></td>

                                <td>
                                    <a href="javascript:tambahSoal(<?= $id_kuis ?>,<?= $row['id_soal'] ?>)" id='tmb-<?= $row['id_soal'] ?>' class="btn btn-primary" onclick="">
                                        <i id='icon-<?= $row['id_soal'] ?>' class="fa fa-plus"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php
                            $no++;
                        }
                        ?>
                    </tbody>
                </table>


            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#soalTable').DataTable();

            // Tambahkan filter berdasarkan kategori dan subkategori
            $('#kategoriFilter, #subkategoriFilter').change(function() {
                var kategori = $('#kategoriFilter').val();
                var subkategori = $('#subkategoriFilter').val();

                table.column(3).search(kategori).draw();
                table.column(4).search(subkategori).draw();
            });
        });
        // document.getElementById('kategoriFilter').addEventListener('change', function() {
        //     var selectedKategori = this.value;

        //     // Menampilkan atau menyembunyikan opsi subkategori berdasarkan kategori yang dipilih
        //     var subkategoriOptions = document.getElementById('subkategoriFilter').options;
        //     for (var i = 0; i < subkategoriOptions.length; i++) {
        //         if (selectedKategori === '' || selectedKategori === subkategoriOptions[i].className) {

        //             subkategoriOptions[i].style.display = 'block';
        //         } else {
        //             subkategoriOptions[i].style.display = 'none';
        //         }
        //     }
        // });

        function tambahSoal(id_kuis, id_soal) {
            const tombol = $("#tmb-" + id_soal);
            const icon = $("#icon-" + id_soal);
            tombol.toggleClass("btn-danger btn-success");
            icon.toggleClass("fa-plus fa-times");
            const URL_API = '<?= $url ?>api/';
            if (tombol.hasClass("btn-danger")) {
                const data = {
                    id_kuis: id_kuis,
                    id_soal: id_soal
                };
                const headers = {
                    'Content-Type': 'application/json'
                };

                const requestOptions = {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify(data)
                };

                fetch(URL_API + "kuis.php", requestOptions)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Gagal menambahkan soal ke kuis');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // console.log('Soal berhasil ditambahkan ke kuis:', data);
                        // Tambahkan logika atau pembaruan UI lainnya sesuai kebutuhan
                    })
                    .catch(error => {
                        console.error('Error:', error.message);
                    });

            } else {
                // alert(`${id_kuis} id soal ${id_soal}`)
                const data = {
                    id_kuis: id_kuis,
                    id_soal: id_soal
                };
                const headers = {
                    'Content-Type': 'application/json'
                };

                const requestOptions = {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify(data)
                };

                fetch(URL_API + "hapus_kuis.php", requestOptions)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Gagal mengapus soal ke kuis');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // console.log('Soal berhasil ditambahkan ke kuis:', data);
                        // Tambahkan logika atau pembaruan UI lainnya sesuai kebutuhan
                    })
                    .catch(error => {
                        // console.error('Error:', error.message);
                    });
            }
        }




        function closePopup() {
            // Fungsi untuk menutup popup
            window.close();
        }

        // Fungsi yang dijalankan saat popup ditutup
        window.onbeforeunload = function() {
            // Fungsi untuk merefresh halaman utama
            window.opener.location.reload();
        };

        function tambahSoalkeKuis(id_kuis, id_soal) {
            const data = {
                id_kuis: id_kuis,
                id_soal: id_soal
            };

            // Header permintaan POST
            const headers = {
                'Content-Type': 'application/json'
                // Tambahkan header lain sesuai kebutuhan, misalnya authorization header
            };

            // Konfigurasi untuk fetch API
            const requestOptions = {
                method: 'POST',
                headers: headers,
                body: JSON.stringify(data)
            };

            // Lakukan permintaan fetch
            fetch(URL_API + "kuis.php", requestOptions)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Gagal menambahkan soal ke kuis');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Soal berhasil ditambahkan ke kuis:', data);
                    // Tambahkan logika atau pembaruan UI lainnya sesuai kebutuhan
                })
                .catch(error => {
                    console.error('Error:', error.message);
                    // Tambahkan penanganan error sesuai kebutuhan
                });
        }
    </script>



</body>

</html>