<?php
// Connection to the database
include '../config.php';
session_start(); // Start the session to access session variables

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    echo json_encode(['message' => 'User not logged in.']);
    exit;
}

// Retrieve the POST data
$menuID = $_POST['menuID'] ?? null;
$namaMenu = $_POST['namaMenu'] ?? null;
$harga = $_POST['harga'] ?? null;
$jumlah = $_POST['jumlah'] ?? null;
$userID = $_SESSION['userID']; // Use session variable for userID

// Check for missing data
if ($menuID === null || $harga === null || $jumlah === null) {
    echo json_encode(['message' => 'Missing data.']);
    exit;
}

// Prepare the SQL statement to check if the item exists in the cart
$sql = "SELECT * FROM keranjang WHERE menuID = ? AND userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $menuID, $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Item exists, update the quantity
    $row = $result->fetch_assoc();
    $newQuantity = $row['kuantitasProduk'] + $jumlah;
    $totalHarga = $newQuantity * $harga;

    $updateSql = "UPDATE keranjang SET kuantitasProduk = ?, totalHarga = ? WHERE keranjangID = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("iis", $newQuantity, $totalHarga, $row['keranjangID']);
    
    if ($updateStmt->execute()) {
        $response = ['message' => 'Cart updated successfully!', 'totalQuantity' => $newQuantity];
    } else {
        $response = ['message' => 'Failed to update cart.'];
    }
} else {
    // Item does not exist, insert a new row
    $totalHarga = $jumlah * $harga;

    $insertSql = "INSERT INTO keranjang (menuID, userID, kuantitasProduk, totalHarga, harga) VALUES (?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("ssiii", $menuID, $userID, $jumlah, $totalHarga, $harga);
    
    if ($insertStmt->execute()) {
        $response = ['message' => 'Item added to cart!', 'totalQuantity' => $jumlah];
    } else {
        $response = ['message' => 'Failed to add item to cart.'];
    }
}

// Get the total quantity of items in the cart for the user
$totalQuantityQuery = "SELECT SUM(kuantitasProduk) AS totalQuantity FROM keranjang WHERE userID = ?";
$totalQuantityStmt = $conn->prepare($totalQuantityQuery);
$totalQuantityStmt->bind_param("s", $userID);
$totalQuantityStmt->execute();
$totalQuantityResult = $totalQuantityStmt->get_result();
$totalQuantityRow = $totalQuantityResult->fetch_assoc();
$totalQuantity = $totalQuantityRow['totalQuantity'] ?? 0;

$response['totalQuantity'] = $totalQuantity; // Add total quantity to the response

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
