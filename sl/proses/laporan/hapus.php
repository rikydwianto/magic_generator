<?php
try {
    // Create a PDO pdoection
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
        $id = $_GET["id"];

        // Delete user from the database
        $stmt = $pdo->prepare("DELETE FROM capaian_staff WHERE id_capaian_staff = :id;
        DELETE FROM detail_capaian_staff WHERE id_capaian_staff = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $result = $stmt->execute();

        echo "User deleted successfully.";
        pindah(menu_sl("laporan/index"));
    } else {
        echo "Invalid request.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the pdoection
$pdo = null;
