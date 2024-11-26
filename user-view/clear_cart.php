<?php
require '../vendor/autoload.php'; // Pastikan jalur ini benar
include '../config.php'; // Koneksi database

// Get POST data from the fetch request
$data = json_decode(file_get_contents('php://input'), true);
$userID = $data['userID'];

// Delete items from the keranjang table for the given user
$clearKeranjangSql = "DELETE FROM keranjang WHERE userID = ?";
$clearStmt = $conn->prepare($clearKeranjangSql);
$clearStmt->bind_param("s", $userID);
$clearStmt->execute();

if ($clearStmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}

$clearStmt->close();
$conn->close();
?>
