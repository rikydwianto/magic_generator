<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generateToken($data)
{
    $secretKey = 'satuduatiga'; // Ganti dengan kunci rahasia yang kuat
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600; // Token berlaku selama 1 jam

    $payload = array(
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'data' => $data
    );

    return JWT::encode($payload, $secretKey, 'HS256');
}

function verifyToken($token)
{
    $secretKey = 'satuduatiga'; // Ganti dengan kunci rahasia yang kuat

    try {
        $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
        return $decoded->data;
    } catch (\Exception $e) {
        return false;
    }
}

function loginStaff($pdo, $nikStaff, $password)
{
    // $data = json_decode(file_get_contents('php://input'), true);

    // Mendapatkan NIK dan password

    // Mengecek keberadaan pengguna
    $stmt = $pdo->prepare('SELECT * FROM staff WHERE nik_staff = ? ');
    $stmt->execute([$nikStaff]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $data = array();
    if ($user) {

        if ($user['status'] == 'aktif') {
            if ($user['password'] === $password) {
                $pesan = "LOGIN BERHASIL";
                $status = 'success';
                $data = array('id_staff' => $user['id_staff'], 'nik' => $nikStaff, 'cabang' => $user['cabang'], 'nama_staff' => $user['nama_staff']);
            } else {
                $pesan = "NIK ditemukan, Password Salah!";
                $status = 'error';
            }
        } else {
            $pesan = "$nikStaff ditemukan, Akun di Non-Aktifkan!";
            $status = 'error';
        }
    } else {
        $pesan = $nikStaff . ' USER TIDAK DITEMUKAN!';
        $status = 'error';
    }
    echo json_encode(['status' => $status, 'message' => $pesan, 'data' => $data]);
}
function loginAdmin($pdo, $nikStaff, $password)
{
    // $data = json_decode(file_get_contents('php://input'), true);

    // Mendapatkan NIK dan password

    // Mengecek keberadaan pengguna
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? or nik =? ');
    $stmt->execute([$nikStaff, $nikStaff]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $data = array();
    if ($user) {

        if ($user['password'] === $password) {
            $pesan = "LOGIN BERHASIL";
            $status = 'success';
            $data = array('id' => $user['id'], 'nik' => $nikStaff, 'id_cabang' => $user['id_cabang']);
        } else {
            $pesan = "NIK ditemukan, Password Salah!";
            $status = 'error';
        }
    } else {
        $pesan = $nikStaff . ' USER TIDAK DITEMUKAN!';
        $status = 'error';
    }
    echo json_encode(['status' => $status, 'message' => $pesan, 'data' => $data]);
}

function DetailSL($pdo, $id)
{
    // $data = json_decode(file_get_contents('php://input'), true);

    // Mendapatkan NIK dan password

    // Mengecek keberadaan pengguna
    $status = 'aktif';
    $stmt = $pdo->prepare('SELECT id_staff,nama_staff,nik_staff,cabang, c.* FROM staff s join cabang c on c.nama_cabang=s.cabang  
    WHERE id_staff = ? and status=?');
    $stmt->execute([$id, $status]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $data = array();
    if ($user) {
        $pesan = 'Data ditemukan';
        $status = 'success';
        $data = $user;
    } else {
        $pesan = ' USER TIDAK DITEMUKAN!';
        $status = 'error';
    }
    echo json_encode(['status' => $status, 'message' => $pesan, 'data' => $data]);
}
function laporanPerStaff($pdo, $nik)
{
    $query = "SELECT cs.*,dc.keterangan FROM capaian_staff cs join detail_capaian_staff dc on cs.id_capaian_staff=dc.id_capaian_staff where nik_staff='$nik' order by created_at desc";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($result) {
        $pesan = "Data berhasil diload";
        $data = $result;
        $status = 'success';
    } else {
        $status = 'error';
        $pesan = "Tidak Ditemukan!";
        $data = [];
    }

    echo json_encode(['status' => $status, 'message' => $pesan, 'data' => $data]);
}
function cekLaporan($pdo, $data)
{
    $id = $data['id_staff'];
    $nik = $data['nik'];
    $cabang = $data['cabang'];
    $minggu = $data['minggu'];
    $bulan = $data['bulan'];
    $tahun = $data['tahun'];
    $nama = $data['nama'];
    $query = "SELECT * FROM capaian_staff 
    WHERE nik_staff = :nik 
      AND cabang_staff = :cabang 
      AND minggu = :minggu 
      AND bulan = :bulan 
      AND tahun = :tahun";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nik', $nik, PDO::PARAM_STR);
    $stmt->bindParam(':cabang', $cabang, PDO::PARAM_STR);
    $stmt->bindParam(':minggu', $minggu, PDO::PARAM_INT);
    $stmt->bindParam(':bulan', $bulan, PDO::PARAM_INT);
    $stmt->bindParam(':tahun', $tahun, PDO::PARAM_INT);

    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        if ($result['status'] == 'approve') {
            $status = 'approve';
            $pesan = 'Laporan Sudah Diapprove, hanya bisa lihat data saja';
        } else if ($result['status'] == 'konfirmasi') {
            $status = 'konfirmasi';
            $pesan = 'Laporan masih dalam pengecekan manager, hubungi manager untuk reject agar dapat di rubah';
        } else {
            $status = 'pending';
            $pesan = 'Silahkan selesaikan Laporan';
        }

        $data = ['id_capaian_staff' => $result['id_capaian_staff']];
        $id_last = $result['id_capaian_staff'];
    } else {

        $cek_user = $pdo->prepare('SELECT id_staff,nama_staff,nik_staff,cabang, c.* FROM staff s join cabang c on c.nama_cabang=s.cabang  
    WHERE id_staff = ? or nik_staff=?');
        $cek_user->execute([$id, $nik]);
        $user = $cek_user->fetch(PDO::FETCH_ASSOC);

        $wilayah = $user['wilayah'];
        $regional = $user['regional'];
        $staff = $nama;
        $insertQuery = "INSERT INTO capaian_staff (nama_staff,nik_staff, cabang_staff, minggu, bulan, tahun,status,wilayah,regional) 
        VALUES (:nama_staff,:nik, :cabang, :minggu, :bulan, :tahun,:status,:wilayah,:regional)";

        $status = 'pending';
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->bindParam(':nik', $nik, PDO::PARAM_STR);
        $insertStmt->bindParam(':nama_staff', $staff, PDO::PARAM_STR);
        $insertStmt->bindParam(':cabang', $cabang, PDO::PARAM_STR);
        $insertStmt->bindParam(':status', $status, PDO::PARAM_STR);
        $insertStmt->bindParam(':wilayah', $wilayah, PDO::PARAM_STR);
        $insertStmt->bindParam(':regional', $regional, PDO::PARAM_STR);
        $insertStmt->bindParam(':minggu', $minggu, PDO::PARAM_INT);
        $insertStmt->bindParam(':bulan', $bulan, PDO::PARAM_INT);
        $insertStmt->bindParam(':tahun', $tahun, PDO::PARAM_INT);

        $insertStmt->execute();

        // Mendapatkan id_last setelah INSERT
        $id_last = $pdo->lastInsertId();
        $id_capaian_staff = $id_last;
        $anggota_masuk = 0;
        $anggota_keluar = 0;
        $nett_anggota = 0;
        $naik_par = 0;
        $turun_par = 0;
        $nett_par = 0;
        $pemb_lain = 0;
        $keterangan = "";
        $agt_cuti = 0;
        $agt_tpk = 0;
        $pemb['PMB'] = 0;
        $pemb['PSA'] = 0;
        $pemb['PPD'] = 0;
        $pemb['PRR'] = 0;
        $pemb['ARTA'] = 0;
        $pinjaman_ = json_encode($pemb);

        $sqlInsert = "INSERT INTO detail_capaian_staff (id_capaian_staff, anggota_masuk, anggota_keluar, nett_anggota, naik_par, turun_par, nett_par, pemb_lain, keterangan,agt_tpk,agt_cuti,json_pinjaman)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?)";
        $stmtInsert = $pdo->prepare($sqlInsert);
        $stmtInsert->execute([$id_capaian_staff, $anggota_masuk, $anggota_keluar, $nett_anggota, $naik_par, $turun_par, $nett_par, $pemb_lain, $keterangan, $agt_tpk, $agt_cuti, $pinjaman_]);



        $status = 'insert';
        $pesan = 'Data Baru berhasil ditambahkan';
        $data = ['id_capaian_staff' => $id_last];
    }

    $sqlCheck = "SELECT * FROM detail_capaian_staff
    WHERE id_capaian_staff = ?";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute([$id_last]);
    $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
    $data = $result;

    echo json_encode(['status' => $status, 'message' => $pesan, 'data' => $data]);
}

function detailCapaian($pdo, $id)
{
    $query = "SELECT * FROM capaian_staff cs join detail_capaian_staff dc on cs.id_capaian_staff=dc.id_capaian_staff
    WHERE cs.id_capaian_staff = :id 
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id);

    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        if ($result['status'] == 'approve') {
            $status = 'approve';
            $pesan = 'Laporan Sudah Diapprove, hanya bisa lihat data saja';
        } else if ($result['status'] == 'konfirmasi') {
            $status = 'konfirmasi';
            $pesan = 'Laporan masih dalam pengecekan manager, hubungi manager untuk reject agar dapat di rubah';
        } else {
            $status = 'pending';
            $pesan = 'Silahkan selesaikan Laporan';
        }
        $data = $result;
    } else {
        $status = 'error';
        $pesan = "Data tidak ditemukan";
        $data = [];
    }
    echo json_encode(['status' => $status, 'message' => $pesan, 'data' => $data]);
}

function updateTTD($pdo, $data)
{
    try {
        $id_capaian_staff = $data['id'];
        // Ambil data dari input form atau permintaan HTTP
        $ttd = "image/png;base64," . $data['ttd']; // Sesuaikan dengan metode pengiriman data dari client

        // Query untuk melakukan update kolom ttd
        $updateQuery = "UPDATE capaian_staff 
                        SET ttd = :ttd , status='konfirmasi'
                        WHERE id_capaian_staff = :id_capaian_staff";

        // Persiapkan dan jalankan statement
        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindParam(':id_capaian_staff', $id_capaian_staff, PDO::PARAM_INT);
        $stmt->bindParam(':ttd', $ttd, PDO::PARAM_STR);

        // Lakukan update
        $stmt->execute();

        // Berhasil melakukan update
        echo json_encode(['status' => 'success', 'message' => 'TTD Berhasil dan data berhasil disimpan']);
    } catch (PDOException $e) {
        // Gagal melakukan update
        echo json_encode(['status' => 'error', 'message' => 'Error :' . $e->getMessage()]);
    }
}

function updateCapaian($pdo, $data)
{
    try {
        // {"pmb":"1","psa":"2","ppd":"3","prr":"4","arta":"5","parTurun":"0","am":"0","ak":"0","parNaik":"0","keterangan":"aYO MAKAN","cuti":"0","tpk":"0"}

        $id_capaian_staff = $data['id'];
        $anggota_masuk = removeNonNumeric($data['am'] ? $data['am'] : 0);
        $anggota_keluar = removeNonNumeric($data['ak'] ? $data['ak'] : 0);
        $nett_anggota = $anggota_masuk - $anggota_keluar;
        $naik_par = removeNonNumeric($data['parTurun'] ? $data['parTurun'] : 0);
        $turun_par = removeNonNumeric($data['parNaik'] ? $data['parNaik'] : 0);
        $nett_par = $naik_par - $turun_par;

        $keterangan = $data['keterangan'];
        $agt_cuti = $data['cuti'] ? $data['cuti'] : 0;
        $agt_tpk = $data['tpk'] ? $data['tpk'] : 0;
        $pemb['PMB'] = $data['pmb'] ? $data['pmb'] : 0;
        $pemb['PSA'] =  $data['psa'] ? $data['psa'] : 0;
        $pemb['PPD'] =  $data['ppd'] ? $data['ppd'] : 0;
        $pemb['PRR'] =  $data['prr'] ? $data['prr'] : 0;
        $pemb['ARTA'] =  $data['arta'] ? $data['arta'] : 0;
        $pinjaman_ = json_encode($pemb);
        $pemb_lain = $pemb['PMB'] + $pemb['PSA'] + $pemb['PPD'] + $pemb['PRR'] + $pemb['ARTA'];
        // Ambil data dari input form atau permintaan HTTP
        $sqlUpdate = "UPDATE detail_capaian_staff
        SET anggota_masuk = ?, anggota_keluar = ?, nett_anggota = ?, naik_par = ?, turun_par = ?, nett_par = ?, pemb_lain = ?, keterangan = ?,agt_tpk=?,agt_cuti=?,json_pinjaman=?
        WHERE id_capaian_staff = ?";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->execute([$anggota_masuk, $anggota_keluar, $nett_anggota, $naik_par, $turun_par, $nett_par, $pemb_lain, $keterangan,  $agt_tpk, $agt_cuti, $pinjaman_, $id_capaian_staff]);
        // Berhasil melakukan update
        echo json_encode(['status' => 'success', 'message' => 'Berhasil disimpan']);
    } catch (PDOException $e) {
        // Gagal melakukan update
        echo json_encode(['status' => 'error', 'message' => 'Error :' . $e->getMessage()]);
    }
}

function hapusLaporan($pdo, $id)
{  // Delete user from the database
    $stmt = $pdo->prepare("DELETE FROM capaian_staff WHERE id_capaian_staff = :id;
    DELETE FROM detail_capaian_staff WHERE id_capaian_staff = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $result = $stmt->execute();
    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Berhasil dihapus']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal dihapus']);
    }
}

function detailAdmin($pdo, $id)
{
    // Mengecek keberadaan pengguna
    $stmt = $pdo->prepare('SELECT s.id,s.nik,s.jabatan,s.nama,email,s.jenis_akun,c.* FROM users s JOIN cabang c ON c.id_cabang=s.id_cabang  
     WHERE id =? ');
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $data = array();
    if ($user) {
        $pesan = 'Data ditemukan';
        $status = 'success';
        $data = $user;
    } else {
        $pesan = ' USER TIDAK DITEMUKAN!';
        $status = 'error';
    }
    echo json_encode(['status' => $status, 'message' => $pesan, 'data' => $data]);
}

function laporanPerCabang($pdo, $cabang, $id)
{

    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?  ');
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user['jabatan'] == 'Regional') {
        $regional = $user['regional'];
        $query = "SELECT cs.id_capaian_staff, cs.nik_staff, cs.nama_staff, cs.cabang_staff, cs.regional, cs.wilayah, cs.minggu, cs.bulan, cs.tahun, cs.created_at, cs.status, dc.keterangan 
        FROM
        capaian_staff cs
        JOIN detail_capaian_staff dc
          ON cs.id_capaian_staff = dc.id_capaian_staff
        JOIN cabang c
          ON c.`nama_cabang` = cs.`cabang_staff`
          WHERE (c.`regional`=? ) AND STATUS <>'approve'
            ORDER BY created_at DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$regional]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $query = "SELECT cs.id_capaian_staff, cs.nik_staff, cs.nama_staff, cs.cabang_staff, cs.regional, cs.wilayah, cs.minggu, cs.bulan, cs.tahun, cs.created_at, cs.status, dc.keterangan 
        FROM
        capaian_staff cs
        JOIN detail_capaian_staff dc
          ON cs.id_capaian_staff = dc.id_capaian_staff
        JOIN cabang c
          ON c.`nama_cabang` = cs.`cabang_staff`
          WHERE (c.`id_cabang`=? OR c.`nama_cabang`=?) AND STATUS <>'approve'
            ORDER BY created_at DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$cabang, $cabang]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    if ($result) {
        $pesan = "Data berhasil diload";
        $data = $result;
        $status = 'success';
    } else {
        $status = 'error';
        $pesan = "Data Tidak Ditemukan!";
        $data = [];
    }

    echo json_encode(['status' => $status, 'message' => $pesan, 'data' => $data]);
}


function prosesApproval($pdo, $data)
{
    try {
        $id_capaian_staff = $data['id'];
        // Ambil data dari input form atau permintaan HTTP

        $status = $data['status'];
        // Query untuk melakukan update kolom ttd
        $updateQuery = "UPDATE capaian_staff 
                        SET status = :status
                        WHERE id_capaian_staff = :id_capaian_staff";

        // Persiapkan dan jalankan statement
        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindParam(':id_capaian_staff', $id_capaian_staff, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);

        // Lakukan update
        $stmt->execute();

        // Berhasil melakukan update
        echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan']);
    } catch (PDOException $e) {
        // Gagal melakukan update
        echo json_encode(['status' => 'error', 'message' => 'Error :' . $e->getMessage()]);
    }
}

function updateFCMToken($pdo, $data)
{
    $nik = $data['nik'];
    $id = $data['id'];
    $token = $data['token'];
    $tipe = $data['tipe'];

    if ($tipe == 'admin') {
        // Ambil data dari input form atau permintaan HTTP

        // Query untuk melakukan update kolom ttd
        $updateQuery = "UPDATE users SET fcm_token = :token WHERE nik = :nik and id=:id;
        update staff set fcm_token=null where fcm_token=:token
        ";

        // Persiapkan dan jalankan statement
        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindParam(':nik', $nik, PDO::PARAM_STR);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
    } else if ($tipe == 'staff') {
        // Query untuk melakukan update kolom ttd
        $updateQuery = "UPDATE staff SET fcm_token = :token WHERE nik_staff = :nik and id_staff=:id ;
                update users set fcm_token=null where fcm_token=:token";

        // Persiapkan dan jalankan statement
        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindParam(':nik', $nik, PDO::PARAM_STR);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
    }
    $stmt->execute();
    echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan']);
}

function cariCabang($pdo,  $id)
{
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?  ');
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user['jabatan'] == 'Regional') {
        $cab = $pdo->prepare('SELECT * FROM cabang WHERE regional = ? and kode_cabang<=500 ');
        $cab->execute([$user['regional']]);
        $data = $cab->fetchAll(PDO::FETCH_ASSOC);

        $pesan = "Data Berhasil diload";
    } else {
        $cab = $pdo->prepare('SELECT * FROM cabang WHERE id_cabang = ?  ');
        $cab->execute([$user['id_cabang']]);
        $data = $cab->fetchAll(PDO::FETCH_ASSOC);

        $pesan = "Data Berhasil diload";
    }
    $status = 'success';

    echo json_encode(['status' => $status, 'message' => $pesan, 'data' => $data]);
}


function cekProgresCabang($pdo, $data)
{
    $minggu = isset($data['minggu']) ? $data['minggu'] : '';
    $bulan = isset($data['bulan']) ? $data['bulan'] : '';
    $tahun = isset($data['tahun']) ? $data['tahun'] : '';
    $cabang = isset($data['cabang']) ? $data['cabang'] : '';

    $query = "
        SELECT
            cs.cabang_staff,
            cs.status,
            COUNT(cs.nik_staff) AS jumlah_staff,
            SUM(dcs.anggota_masuk) AS total_anggota_masuk,
            SUM(dcs.anggota_keluar) AS total_anggota_keluar,
            SUM(dcs.nett_anggota) AS total_nett_anggota,
            SUM(dcs.naik_par) AS total_naik_par,
            SUM(dcs.turun_par) AS total_turun_par,
            SUM(dcs.nett_par) AS total_nett_par,
            SUM(dcs.pemb_lain) AS total_pemb_lain,
            SUM(dcs.agt_tpk) AS total_agt_tpk,
            SUM(dcs.agt_cuti) AS total_agt_cuti,
            SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(dcs.json_pinjaman, '$.PMB')) AS INT)) AS total_PMB,
            SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(dcs.json_pinjaman, '$.PSA')) AS INT)) AS total_PSA,
            SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(dcs.json_pinjaman, '$.PPD')) AS INT)) AS total_PPD,
            SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(dcs.json_pinjaman, '$.PRR')) AS INT)) AS total_PRR,
            SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(dcs.json_pinjaman, '$.ARTA')) AS INT)) AS total_ARTA
        FROM
            capaian_staff cs
        JOIN
            detail_capaian_staff dcs ON cs.id_capaian_staff = dcs.id_capaian_staff
        WHERE
            cs.cabang_staff = :cabang AND cs.minggu = :minggu and cs.bulan=:bulan and cs.tahun=:tahun and cs.status='approve'
        GROUP BY
            cs.cabang_staff
        ";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':cabang', $cabang, PDO::PARAM_STR);
    $stmt->bindParam(':minggu', $minggu, PDO::PARAM_INT);
    $stmt->bindParam(':bulan', $bulan, PDO::PARAM_INT);
    $stmt->bindParam(':tahun', $tahun, PDO::PARAM_INT);
    $stmt->execute();

    $stmtCheck = $pdo->prepare("SELECT id,status,keterangan FROM capaian_cabang WHERE minggu = ? AND bulan = ? AND tahun = ?  and nama_cabang=?");
    $stmtCheck->execute([$minggu, $bulan, $tahun, $cabang]);
    $existingData = $stmtCheck->fetch(PDO::FETCH_ASSOC);
    $query = "SELECT  COUNT(*) as jumlah_staff FROM staff where cabang='$cabang' and status='aktif'";

    $hit_ = $pdo->prepare($query);
    $hit_->execute();

    // Mengambil hasil query
    $jml_staff = $hit_->fetch()['jumlah_staff'];

    $pesan = "Berhasil di Load";
    $status = 'success';
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $q_belum = $pdo->prepare("select s.nik_staff,s.nama_staff,s.cabang,status,fcm_token from staff s where cabang='$cabang' 
    and nik_staff not in(select nik_staff from capaian_staff where cabang_staff= :cabang and minggu= :minggu and bulan=:bulan and tahun=:tahun and status='approve' ) ");
    $q_belum->bindParam(":cabang", $cabang);
    $q_belum->bindParam(":tahun", $tahun);
    $q_belum->bindParam(":bulan", $bulan);
    $q_belum->bindParam(":minggu", $minggu);
    $q_belum->execute();
    $belum_laporan = $q_belum->fetchAll(PDO::FETCH_ASSOC);

    $q_sudah = $pdo->prepare("select s.nik_staff,s.nama_staff,s.cabang,status,fcm_token from staff s where cabang='$cabang' 
    and nik_staff in(select nik_staff from capaian_staff where cabang_staff= :cabang and minggu= :minggu and bulan=:bulan and tahun=:tahun  ) ");
    $q_sudah->bindParam(":cabang", $cabang);
    $q_sudah->bindParam(":tahun", $tahun);
    $q_sudah->bindParam(":bulan", $bulan);
    $q_sudah->bindParam(":minggu", $minggu);
    $q_sudah->execute();
    $sudah_laporan = $q_sudah->fetchAll(PDO::FETCH_ASSOC);

    $data = array(
        'hasil' => $result,
        'jml_staff' => ($jml_staff),
        'progress' => ($existingData),
        'belum_laporan' => ($belum_laporan),
        'sudah_laporan' => $sudah_laporan
    );

    echo json_encode(['status' => $status, 'message' => $pesan, 'data' => $data]);
}