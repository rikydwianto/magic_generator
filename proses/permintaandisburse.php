<div class="container-fluid">
    <div class="row">
        <h1>Input Permintaan disburse</h1>

        <div class="col-6">
            <form method="post" enctype="multipart/form-data">
                <div class="col-12">
                    <label for="formFile" class="form-label">SILAHKAN PILIH FILE : PERMINTAAN DISBURSE XML</label>
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

    // Upload XML file
    $file = $_FILES['file']['tmp_name'];
    $xml = simplexml_load_file($file);

    if ($xml === false) {
        echo "Failed to load XML.";
    } else {
        try {
            // Ambil nama cabang dan tanggal dari atribut Report
            $reportAttribs = $xml->attributes();
            $textbox101 = (string)$reportAttribs['Textbox101'];
            $_Textbox102 = (string)$reportAttribs['Textbox102'];
            preg_match_all('/\d{4}-\d{2}-\d{2}/', $_Textbox102, $matches);

            if (!empty($matches[0])) {
                $tanggal_awal  = $matches[0][0]; // 2025-08-22
                $tanggal_akhir = $matches[0][1]; // 2025-08-29
            }


            // echo $_Textbox102;
            // exit;
            $textboxParts = explode(" ", $textbox101);

            $namaCabang = $textboxParts[1]; // Cabang
            // $tanggal = date("Y-m-d", strtotime($textboxParts[3])); // Tanggal

            // Hapus data lama untuk cabang dan tanggal yang sama
            $pdo->query("DELETE FROM permintaan_disburse WHERE nama_cabang = '$namaCabang' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'");


            // Ambil elemen Details_Collection
            $detailsCollection = $xml->Tablix1->Details_Collection->Details;

            foreach ($detailsCollection as $detail) {
                // Ambil data dari atribut <Details>
                $centerID = (string)$detail['CenterID'];
                $clientID = (string)$detail['ClientID'];
                $clientName = (string)$detail['ClientName'];
                $officerName = (string)$detail['OfficerName'];
                $productName = (string)$detail['ProductName'];
                $gpPokok = (float)$detail['GpPokok'];
                $gpNishbah = (float)$detail['GpNishbah'];
                $period = (int)$detail['Period'];
                $pinjKe = (int)$detail['Pinjke'];
                $jenisTopUp = (string)$detail['JenisTopUp'];
                $loanAmount = (float)$detail['LoanAmount'];
                $osPokokTopUP = (float)$detail['OsPokokTopUP'];
                $netDisburse = (float)$detail['NetDisburse'];
                $DisbDate = $detail['DisbDate'];

                // Simpan data ke database
                $stmt = $pdo->prepare("INSERT INTO permintaan_disburse
                    (nama_cabang, tanggal, center_id, client_id, client_name, officer_name, product_name, gp_pokok, gp_nishbah, period, pinj_ke, jenis_top_up, loan_amount, os_pokok_top_up, net_disburse) 
                    VALUES (:nama_cabang, :tanggal, :center_id, :client_id, :client_name, :officer_name, :product_name, :gp_pokok, :gp_nishbah, :period, :pinj_ke, :jenis_top_up, :loan_amount, :os_pokok_top_up, :net_disburse)");

                $stmt->bindParam(':nama_cabang', $namaCabang);
                $stmt->bindParam(':tanggal', $DisbDate);
                $stmt->bindParam(':center_id', $centerID);
                $stmt->bindParam(':client_id', $clientID);
                $stmt->bindParam(':client_name', $clientName);
                $stmt->bindParam(':officer_name', $officerName);
                $stmt->bindParam(':product_name', $productName);
                $stmt->bindParam(':gp_pokok', $gpPokok);
                $stmt->bindParam(':gp_nishbah', $gpNishbah);
                $stmt->bindParam(':period', $period);
                $stmt->bindParam(':pinj_ke', $pinjKe);
                $stmt->bindParam(':jenis_top_up', $jenisTopUp);
                $stmt->bindParam(':loan_amount', $loanAmount);
                $stmt->bindParam(':os_pokok_top_up', $osPokokTopUP);
                $stmt->bindParam(':net_disburse', $netDisburse);

                if (!$stmt->execute()) {
                    echo "Error: " . implode(", ", $stmt->errorInfo());
                }
            }

            // tambah 1 hari tanggal awal
            $tanggal_awal = date("Y-m-d", strtotime($tanggal_awal . " +1 day"));
            echo "Data berhasil disimpan.";
            pindah("index.php?menu=permintaan_disburse_print&nama_cabang=$namaCabang&tanggal=$tanggal_awal&tanggal_akhir=$tanggal_akhir");
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}


?>