<?php
try {
    // Create a PDO pdoection
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
        $id = $_GET["id"];

        // Delete user from the database
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        echo "User deleted successfully.";
        pindah(menu_progress("users/index"));
    } else {
        echo "Invalid request.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the pdoection
$pdo = null;
