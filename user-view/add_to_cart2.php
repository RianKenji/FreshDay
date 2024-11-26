<?php
session_start();
include('../config.php');

// Check if data is received from the AJAX request
if (isset($_POST['type']) && $_POST['type'] == 'bundling') {
    // Get bundling data from the POST request
    $bundlingID = $_POST['bundlingID'];
    $namaBundling = $_POST['namaBundling'];
    $hargaPromo = $_POST['hargaPromo'];
    $produk1 = $_POST['produk1'];
    $produk2 = $_POST['produk2'];
    $gambar = $_POST['gambar'];
    $tanggalMulai = $_POST['tanggalMulai'];
    $tanggalSelesai = $_POST['tanggalSelesai'];
    $userID = $_SESSION['userID']; // Assuming userID is stored in the session

    // Default quantity for bundling (you can adjust this if needed)
    $kuantitasProduk = 1; 

    // Calculate total price for the bundle (if it's based on a single item or the entire bundle)
    $totalHarga = $hargaPromo * $kuantitasProduk; 

    // Set isPromo to 1 for bundling items
    $isPromo = 1;

    // Prepare the SQL to insert into the `keranjang` table
    $sql = "INSERT INTO keranjang (kuantitasProduk, totalHarga, userID, isPromo, promoID, bundlingID) 
            VALUES (?, ?, ?, ?, ?, ?)";

    // Prepare and bind parameters
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("idssss", $kuantitasProduk, $totalHarga, $userID, $isPromo, $bundlingID, $bundlingID);
        
        // Execute the query
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Promo Bundling added to cart']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add to cart']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare the SQL query']);
    }

} else {
    // Handle the case where the type is not 'bundling' or data is missing
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
