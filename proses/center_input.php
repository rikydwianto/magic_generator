<div class="container-fluid">
    <div class="row">
        <h1>Input Center Meeting</h1>

        <div class="col-6">
            <form method="post" enctype="multipart/form-data">
                <div class="col-12">
                    <label for="formFile" class="form-label">SILAHKAN PILIH FILE : JADWAL CENTER MEETING</label>
                    <input class="form-control" type="file" name='file' accept=".xml" id="formFile">
                    <!-- <input type="submit" value="Proses"  class='btn btn-danger' name='preview'> --> <br>
                    <input type="submit" onclick="return confirm('yakin sudah benar?')" value="Proses"
                        class='btn btn-info' name='xml-preview'>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
if (isset($_POST['xml-preview'])) {
    libxml_use_internal_errors(true);

    $file = $_FILES['file']['tmp_name'];
    $path = $file;
    $xml = simplexml_load_file($path) or die("Error: Cannot create object");


    $validate = $xml['Name'];
    if ($validate == "CenterMeeting") {
        //   echo ($xml[0]->Tablix2['HARI_Collection']);
        $xml = new SimpleXMLElement($path, 0, true);

        $raw = $xml;
        $executionTime = $raw->attributes()->ExecutionTime;
        $pecah = explode(",", $executionTime);
        $namaCabang = $pecah[0];

        $hapus_center = "delete from center where nama_cabang='$namaCabang' or id_cabang='$sesi'";
        $pdo->query($hapus_center);

        $xml = ($xml->Tablix2->HARI_Collection);
        $hari = $xml->HARI;

        $total_center = 0;
        $hitung_hari = count($hari);

        foreach ($hari as $day) {
            // Ambil nama hari dan ubah menjadi huruf kecil
            $days = strtolower($day["HARI1"] ?? '');

            foreach ($day->OfficerName_Collection as $hari_staff) {
                foreach ($hari_staff->OfficerName as $staff) {
                    // Ambil nama staff dengan memisahkan string berdasarkan "Total"
                    $nama_staff = explode("Total ", $staff['OfficerName1'] ?? '')[0];


                    foreach ($staff->CenterID_Collection->CenterID  as $ctr_staf) {
                        foreach ($ctr_staf->CenterName as $center) {
                            // Ambil ID Center
                            $no_center = $ctr_staf['CenterID'] ?? '';

                            // Ambil detail center
                            $detail_center = $center->Details_Collection->Details ?? null;
                            // var_dump($detail_center);
                            // Ambil atribut dari detail
                            $jam = rubahkata($detail_center['MeetingTime'] ?? '');
                            $agt = $detail_center['Textbox128'] ?? '0';
                            $client = $detail_center['JumlahClient'] ?? '0';
                            $desa = aman(ganti_karakter(rubahkata($detail_center['DusunName'] ?? '')));
                            $kecamatan = aman(ganti_karakter(rubahkata($detail_center['KecamatanName'] ?? '')));
                            $kab = aman(ganti_karakter(rubahkata($detail_center['KabupatenName'] ?? '')));
                            // Siapkan query SQL
                            $qtxt = "
                            INSERT INTO `center` (
                                `id_center`,
                                `no_center`,
                                `doa_center`,
                                `hari`,
                                `status_center`,
                                `member_center`,
                                `anggota_center`,
                                `center_bayar`,
                                `id_cabang`,
                                `id_karyawan`,
                                `id_laporan`,
                                `jam_center`,
                                `latitude`,
                                `longitude`,
                                `doortodoor`,
                                `blacklist`,
                                `konfirmasi`,
                                `staff`,
                                `desa`,
                                `kecamatan`,
                                `kabupaten`,
                                `anggota_hadir`,
                                `nama_cabang`
                            ) VALUES (
                                NULL,
                                :no_center,
                                'y',
                                :days,
                                'hijau',
                                :agt,
                                :client,
                                :client_bayar,
                                :id_cabang,
                                '0',
                                '0',
                                :jam,
                                NULL,
                                NULL,
                                't',
                                't',
                                't',
                                :nama_staff,
                                :desa,
                                :kecamatan,
                                :kabupaten,
                                :anggota_hadir,
                                :nama_cabang
                            );
                        ";

                            // Debug query jika diperlukan
                            // echo $qtxt . '<br>';

                            // Eksekusi query menggunakan prepared statements
                            $arr =  [
                                ':no_center' => $no_center,
                                ':days' => $days,
                                ':agt' => $agt,
                                ':client' => $client,
                                ':client_bayar' => $client,
                                ':id_cabang' => $sesi,
                                ':jam' => $jam,
                                ':nama_staff' => $nama_staff,
                                ':desa' => $desa,
                                ':kecamatan' => $kecamatan,
                                ':kabupaten' => $kab,
                                ':anggota_hadir' => $agt,
                                ':nama_cabang' => $namaCabang,
                            ];
                            $stmt = $pdo->prepare($qtxt);
                            $stmt->execute($arr);
                            // var_dump($arr);
                            // Tambahkan ke total center
                            $total_center++;
                        }
                    }
                }
            }
        }


        echo "Total center processed: $total_center";



        pindah("index.php?menu=center_proses&nama_cabang=$namaCabang");
    } else {
        alert("DITOLAK, BUKAN FILE CENTER MEETING XML");
    }
    //   echo var_dump($xml);

}

?>