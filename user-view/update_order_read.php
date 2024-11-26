<?php
// Database connection
require '../config.php'; // Your database configuration

// Ensure that the order ID (pesananID) is provided
if (isset($_POST['pesananID'])) {
    $pesananID = $_POST['pesananID'];

    // Update the order as read in the orders table
    $query = "UPDATE orders SET isRead = TRUE WHERE pesananID = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$pesananID]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Order marked as read.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Order not found or already read.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
