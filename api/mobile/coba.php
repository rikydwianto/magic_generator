<?php
$json_data = '{
    "tanggal": "2024-03-21",
    "cabang": "Sidrap",
    "lat": "-3.92453",
    "lng": "119.791",
    "center": "176",
    "tipe": "Kunjungan Usaha",
    "lokasi": "Jl. Andi Makkasau No.2, Pangkajene, Kec. Maritengngae, Kabupaten Sidenreng Rappang, Sulawesi Selatan 91611, Indonesia",
    "akurasi": "22",
    "hp": "085280591987",
    "nama": "Sumarni Sudirman",
    "jenis_usaha": "Jual Kue",
    "nik": "3729/2017",
    "id": "1",
    "photo": "{\"photoUrls\":[\"https:\\/\\/firebasestorage.googleapis.com\\/v0\\/b\\/comdev-tool.appspot.com\\/o\\/images%2Fimage_1711010782328.jpg?alt=media&token=9fdb7cf0-bb98-49dd-84f8-705047485b76\",\"https:\\/\\/firebasestorage.googleapis.com\\/v0\\/b\\/comdev-tool.appspot.com\\/o\\/images%2Fimage_1711010808143.jpg?alt=media&token=4ddf2a36-d9d5-45ac-9885-a2f6b89a0e5d\"]}"
  }';

$data = json_decode($json_data, true);

// Ambil array fotoUrls dari string JSON dan loop untuk menampilkan gambar
$photoUrls = json_decode($data['photo'], true)['photoUrls'];
foreach ($photoUrls as $photoUrl) {
    $query = "delete from photo_kunjungan where id_kunjungan='$id_kun;'";
    $query .= 'INSERT INTO photo_kunjungan (id_kunjungan, url_photo) VALUES (:id_kunjungan, :url_photo)';
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_kunjungan', $id_kun);
    $stmt->bindParam(':url_photo', $photoUrl);
    $stmt->execute();
}