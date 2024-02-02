<?php
require './../vendor/autoload.php'; // Impor library Dotenv
require './../proses/global_fungsi.php';
include_once "./../config/setting.php";
include_once "./../config/koneksi.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNDIAN DOORPRIZE KOMIDA REGIONAL H</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@900&family=Julius+Sans+One&family=Kanit:wght@200&family=Kodchasan:ital,wght@0,500;0,600;1,400&family=Montserrat+Alternates:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />

    <style>

    </style>
</head>

<body>
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-md-8">
                <h2>LIST KARYAWAN</h2>
                <div class="card">
                    <div class="card-body">
                        <form action="#" method="post">
                            <div class="form-group">
                                <label for="cabangSelect">Cabang:</label>
                                <select class="form-control" id="cabangSelect" name="cabangSelect">
                                    <option value="">SEMUA CABANG</option>
                                    <?php


                                    // Query untuk mendapatkan data cabang dari tabel nik_undi
                                    $query = "SELECT DISTINCT cabang FROM nik_undi order by cabang asc";
                                    $result = $pdo->query($query);
                                    $result  = $result->fetchAll();
                                    foreach ($result as $row) {
                                        echo "<option value='" . $row["cabang"] . "'>" . $row["cabang"] . "</option>";
                                    }

                                    // Menutup koneksi database
                                    ?>
                                </select>
                            </div>
                        </form>

                        <table class="table table-bordered" id='table'>
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>NIK</th>
                                    <th>CABANG</th>
                                    <th>NAMA</th>
                                    <th>Dapat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $q = $pdo->query("select * from nik_undi order by nik asc");
                                $hasil = $q->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($hasil as $kar) {
                                ?>
                                    <tr>
                                        <td><?= $no ?></td>
                                        <td><?= $kar['nik'] ?></td>
                                        <td><?= $kar['cabang'] ?></td>
                                        <td><?= $kar['nama'] ?></td>
                                        <td><?= $kar['dapat'] ?></td>

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

            <!-- Kolom 2: Detail -->
            <div class="col-md-4">
                <h2>DETAIL</h2>
                <div class="card">
                    <div class="card-body" id="detailContainer">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Info Detail -->
                                <h5 class="card-title">Detail</h5>
                                <p id="detailId"><strong>NIK :</strong></p>
                                <p id="detailNama"><strong>Nama:</strong></p>
                                <p id="detailCabang"><strong>Cabang:</strong></p>
                                <hr>
                                <!-- Tambahkan info detail lainnya sesuai kebutuhan -->
                            </div>
                            <div class="col-md-12">
                                <table class="table-bordered">
                                    <thead>
                                        <tr>
                                            <th>CABANG</th>
                                            <th>TOTAL STAFF</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $cab = "SELECT cabang, count(*) as total_staff FROM nik_undi group by cabang order by cabang asc";
                                        $result = $pdo->query($cab);
                                        $cab  = $result->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($cab as $caba) {
                                        ?>
                                            <tr>
                                                <td><?= $caba['cabang'] ?></td>
                                                <td><?= $caba['total_staff'] ?></td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script>
        var url = "<?= $url . 'undian/' ?>";
    </script>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#table').DataTable();

            // Tambahkan filter berdasarkan kategori dan subkategori
            $('#cabangSelect').change(function() {
                var cabang = $('#cabangSelect').val();

                table.column(2).search(cabang).draw();
                // table.column(5).search(subkategori).draw();
            });
        });



        const channel = new BroadcastChannel('myChannel');

        // Mendengarkan pesan yang dikirim ke channel
        channel.addEventListener('message', event => {
            // Mendapatkan data dari pesan
            const receivedData = event.data;

            cekDetail(receivedData)
            // peringatan(receivedData);
            // Lakukan sesuatu dengan data yang diterima
        });
        // cekDetail('003729/2017')

        function cekDetail(nik) {
            $.ajax({
                type: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
                url: url + 'api_undian.php?ambil_nik', // Sesuaikan dengan lokasi file update_api.php
                data: {
                    nik: nik,
                },
                success: function(response) {
                    var data = response.data

                    document.getElementById("detailId").innerHTML = "<strong>NIK :</strong> " + data.nik;
                    document.getElementById("detailNama").innerHTML = "<strong>Nama:</strong> " + data.nama;
                    document.getElementById("detailCabang").innerHTML = "<strong>Cabang:</strong> " + data
                        .cabang;

                    // Menampilkan gambar (photo)
                    document.getElementById("detailPhoto").src = data.photo;
                    document.getElementById("detailPhoto").alt = "Photo of " + data.nama;
                },

            });


        }

        function peringatan(data) {
            Swal.fire({
                title: 'ADA PEMENANG',
                icon: 'success',
                text: data + ' MENJADI PEMENANG SILAHKAN DI CEK!'
            })
        }
    </script>
</body>

</html>