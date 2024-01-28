<?php
require './../vendor/autoload.php'; // Impor library Dotenv
require './../proses/global_fungsi.php';
include_once "./../config/setting.php";
include_once "./../config/koneksi.php";
$id_kuis = isset($_SESSION['id_kuis']) ? intval($_SESSION['id_kuis']) : 0;
$id_jawab = $_SESSION['id_kuis_jawab'];


// Query untuk mengecek apakah kuis dengan ID tertentu ada atau tidak
$sql = "SELECT * FROM kuis WHERE id_kuis = :id_kuis";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_kuis', $id_kuis, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($stmt->rowCount() === 0 || $row['status'] == 'tidakaktif') {
    pindah($url_quiz . "404.php"); // Gantilah 404.php dengan halaman 404 yang sesuai
    exit();
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $row['nama_kuis'] ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@9">
    <link rel="stylesheet" href="<?= $url_quiz . 'style.css' ?>">


    <script>
        let url_api = "<?= $url . "api/" ?>";
        let url = "<?= $url_quiz ?>";
        let id_kuis = "<?= $id_kuis ?>";
        let id_jawab = "<?= $id_jawab ?>";
    </script>
</head>

<body>



    <div class="container-fluid mt-5">
        <form id="quizForm">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <div class="container-fluid">
                                <div class="row gx-2">
                                    <div class="col">
                                        <div class="">
                                            <h4 class="mb-0" id='hitung_soal'>Soal ke 1 dari 4</h4>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="">
                                            <h4 class='text-white text-right' style="float: inline-end;" id="countdown">
                                                00:00</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>




                        </div>
                        <div class="card-body">

                            <div id="gambar"></div>
                            <div id="questionContainer" class="question-container">

                            </div>
                            <div class="loader"></div>

                            <div id="tombol">
                                <button id="nextBtn" type="button" onclick="validateForm()" class="btn btn-primary mt-3">Jawab</button>
                                <button id="cekHasil" type="button" onclick="validateForm1()" class="btn btn-success mt-3">Jawab Dan Cek Hasil</button>
                                <!-- <button type="button" onclick="hapusWaktu()" class="btn btn-danger mt-3">Reset
                                    Waktu</button> -->
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>


    <!-- Bootstrap JS and Popper.js -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>


    <script src='<?= $url_quiz . 'script_quiz1.js' ?>'></script>
</body>

</html>