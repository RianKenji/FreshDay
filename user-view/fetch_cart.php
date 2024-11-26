<?php
session_start();
include '../config.php'; // Include your database connection

if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];
    $cartItems = [];

    // Fetch cart data from the keranjang table
    $query = "SELECT k.keranjangID, k.kuantitasProduk, k.totalHarga, m.menuID, m.namaMenu, m.harga, m.gambar 
              FROM keranjang k 
              JOIN menu m ON k.menuID = m.menuID 
              WHERE k.userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
    }

    echo json_encode($cartItems);
} else {
    echo json_encode([]);
}
?>
