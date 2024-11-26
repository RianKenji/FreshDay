<?php
// Assuming you have a connection to the database
include '../config.php';

// Query to get active orders (where status is 'Pending' or 'Dikirim')
$query = "SELECT orderID FROM orders WHERE status IN ('Pending', 'Dikirim')";
$result = mysqli_query($conn, $query);

// Prepare the response
$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}

// Return the orders in JSON format
echo json_encode(['orders' => $orders]);
?>
