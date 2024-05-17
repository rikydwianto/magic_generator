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
        foreach ($hari  as $day) {
            $days = strtolower($day["HARI1"]);
            //   echo $day['HARI1']."<br/>";
            foreach ($day->OfficerName_Collection as $hari_staff) {
                //   echo $day;
                foreach ($hari_staff->OfficerName as $staff) {
                    $nama_staff = explode("Total ", $staff['OfficerName1'])[0];
                    //   echo $nama_staff."<br/>";
                    foreach ($staff->CenterID_Collection->CenterID as $ctr_staf) {
                        $no_center =  $ctr_staf['CenterID'];
                        $center[] = $no_center;
                        $detail_center = $ctr_staf->Details_Collection->Details;
                        $jam = $detail_center['MeetingTime'];
                        $agt = $detail_center['Textbox128'];
                        $client = $detail_center['JumlahClient'];
                        $desa = $detail_center['DusunName'];
                        $kecamatan = $detail_center['KecamatanName'];
                        $kab = $detail_center['KabupatenName'];

                        $qtxt = "INSERT INTO 
                        `center` (`id_center`, `no_center`, `doa_center`, `hari`, `status_center`, `member_center`, `anggota_center`, `center_bayar`, `id_cabang`, `id_karyawan`, `id_laporan`, `jam_center`, `latitude`, `longitude`, `doortodoor`, `blacklist`, `konfirmasi`, `staff`,desa,kecamatan,kabupaten,anggota_hadir,nama_cabang) 
                        VALUES (NULL, '$no_center', 'y', '$days', 'hijau', '$agt', '$client', '$client', '$sesi', '0', '0', '$jam', 'null', 'null', 't', 't', 't', '$nama_staff','$desa','$kecamatan','$kab','$agt','$namaCabang'); 
                        ";
                        $pdo->query($qtxt);

                        $total_center++;
                    }
                }
            }
        }


        pindah("index.php?menu=center_proses&nama_cabang=$namaCabang");
    } else {
        alert("DITOLAK, BUKAN FILE CENTER MEETING XML");
    }
    //   echo var_dump($xml);

}

?>