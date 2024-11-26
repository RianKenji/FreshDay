<?php
// Include database connection
include '../config.php'; // Replace with your actual database connection script

session_start(); // Start the session to access user information

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $userID = $_SESSION['userID']; // Assuming userID is stored in the session
    $address = htmlspecialchars($_POST['address']);
    $totalCartPrice = 0; // To calculate the total price for the order

    // Get the next pesananID
    $queryMaxID = "SELECT MAX(pesananID) AS maxID FROM orders";
    $resultMaxID = $conn->query($queryMaxID);
    $rowMaxID = $resultMaxID->fetch_assoc();
    $currentMaxID = $rowMaxID['maxID'];

    // Generate the next pesananID
    if ($currentMaxID) {
        $newIDNumber = intval(substr($currentMaxID, 3)) + 1; // Increment the numeric part
    } else {
        $newIDNumber = 1; // Start from 1 if there are no existing orders
    }
    $pesananID = "ORD" . str_pad($newIDNumber, 4, '0', STR_PAD_LEFT); // Generate the new ID

    // Insert order into orders table
    $insertOrderSql = "INSERT INTO orders (pesananID, tanggalPesanan, status, userID, alamatPengiriman) VALUES (?, NOW(), 'Pesanan Masuk', ?, ?)";
    $stmt = $conn->prepare($insertOrderSql);
    $stmt->bind_param("sss", $pesananID, $userID, $address);
    $stmt->execute();

    // Fetch items from the keranjang table for the current user
    $queryKeranjang = "SELECT menuID, kuantitasProduk FROM keranjang WHERE userID = ?";
    $keranjangStmt = $conn->prepare($queryKeranjang);
    $keranjangStmt->bind_param("s", $userID);
    $keranjangStmt->execute();
    $keranjangResult = $keranjangStmt->get_result();

    // Insert each item from the keranjang into order_details
    while ($cartItem = $keranjangResult->fetch_assoc()) {
        $menuID = $cartItem['menuID'];
        $jumlah = $cartItem['kuantitasProduk'];

        // Get the unit price from the menu table
        $queryUnitPrice = "SELECT harga FROM keranjang WHERE menuID = ?"; // Adjust table and field names as necessary
        $priceStmt = $conn->prepare($queryUnitPrice);
        $priceStmt->bind_param("s", $menuID);
        $priceStmt->execute();
        $priceResult = $priceStmt->get_result();
        
        if ($priceResult->num_rows > 0) {
            $priceRow = $priceResult->fetch_assoc();
            $unitPrice = $priceRow['harga'];

            // Check if unitPrice is numeric
            if (!is_numeric($unitPrice)) {
                die("Error: Unit price for menuID $menuID is not numeric.");
            }

            $itemTotalPrice = $unitPrice * $jumlah; // Calculate total price for the item
            $totalCartPrice += $itemTotalPrice;

            // Insert into order_details
            $insertOrderDetailSql = "INSERT INTO order_details (pesananID, menuID, jumlah, totalHarga, harga) VALUES (?, ?, ?, ?, ?)";
            $detailStmt = $conn->prepare($insertOrderDetailSql);
            $detailStmt->bind_param("ssiii", $pesananID, $menuID, $jumlah, $itemTotalPrice, $unitPrice);
            if (!$detailStmt->execute()) {
                die("Execution failed: " . $detailStmt->error);
            }
        } else {
            die("Error: No unit price found for menuID $menuID.");
        }
    }

    // Redirect to a payment page or show a success message
    header("Location: payment.php?orderID=$pesananID&total=" . urlencode($totalCartPrice) . "&address=" . urlencode($address));

    // Close the statement and connection
    $stmt->close();
    $detailStmt->close();
    $keranjangStmt->close();
    $clearStmt->close();
    $priceStmt->close(); // Close unit price statement
    $conn->close();
} else {
    // Redirect to cart if not a POST request
    header('Location: cart.php');
    exit;
}
?>
