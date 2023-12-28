<?php
$id_kuis = $_GET['id_kuis'];
$query = "select * from kuis where id_kuis='$id_kuis'";
$stmt = $pdo->query($query);
$kuis = $stmt->fetch()
?>
<h2>HASIL PRE POST - TEST</h2>
<h3>Judul : <?= $kuis['nama_kuis'] ?></h3>
<?php

$query = "
    SELECT 
        unique_id_2,
        id_kuis,
        nama,
        cabang,nik,
        MAX(CASE WHEN jenis_kuis = 'pre' THEN total_score END) AS pre_test_score,
        MAX(CASE WHEN jenis_kuis = 'post' AND post_number = 1 THEN total_score END) AS post_test_1_score,
        MAX(CASE WHEN jenis_kuis = 'post' AND post_number = 2 THEN total_score END) AS post_test_2_score,
        MAX(CASE WHEN jenis_kuis = 'post' AND post_number = 3 THEN total_score END) AS post_test_3_score
    FROM (
        SELECT 
            unique_id_2,
            id_kuis,
            nama,
            cabang,
            nik,
            jenis_kuis,
            total_score,
            ROW_NUMBER() OVER (PARTITION BY unique_id_2, jenis_kuis ORDER BY created) AS post_number
        FROM kuis_jawab
    ) AS numbered_data
    where id_kuis='$id_kuis'
    GROUP BY unique_id_2 order by nik;
";

$stmt = $pdo->query($query);
$hasilQuery = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<table class='table table-bordered'>
    <thead>
        <tr>
            <th>Cabang</th>
            <th>NIK</th>
            <th>Nama</th>
            <th>Pre-Score</th>
            <th>Post-1 Score</th>
            <th>Post-2 Score</th>
            <th>Post-3 Score</th>
        </tr>
    </thead>
    <tbody>

        <?php foreach ($hasilQuery as $hasil) : ?>
            <tr>
                <td><?php echo $hasil['cabang']; ?></td>
                <td><?php echo $hasil['nik']; ?></td>
                <td><?php echo $hasil['nama']; ?></td>
                <td><?php echo $hasil['pre_test_score']; ?></td>
                <td><?php echo $hasil['post_test_1_score']; ?></td>
                <td><?php echo $hasil['post_test_2_score']; ?></td>
                <td><?php echo $hasil['post_test_3_score']; ?></td>
            </tr>
        <?php endforeach; ?>

    </tbody>
</table>