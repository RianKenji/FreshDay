<?php
// Connection to the database
include '../config.php';
session_start(); // Start the session to access session variables

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    echo json_encode(['totalQuantity' => 0]);
    exit;
}

// Retrieve userID from session
$userID = $_SESSION['userID'];

// Query to get the total quantity of items in the cart
$totalQuantityQuery = "SELECT SUM(kuantitasProduk) AS totalQuantity FROM keranjang WHERE userID = ?";
$totalQuantityStmt = $conn->prepare($totalQuantityQuery);
$totalQuantityStmt->bind_param("s", $userID);
$totalQuantityStmt->execute();
$totalQuantityResult = $totalQuantityStmt->get_result();
$totalQuantityRow = $totalQuantityResult->fetch_assoc();
$totalQuantity = $totalQuantityRow['totalQuantity'] ?? 0; // Default to 0 if no items

$totalQuantityStmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode(['totalQuantity' => $totalQuantity]);
?>
