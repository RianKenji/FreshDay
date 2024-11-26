<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pesananID = $conn->real_escape_string($_POST['pesananID']);
    $status = $conn->real_escape_string($_POST['status']);

    $updateQuery = "UPDATE orders SET status='$status' , isRead = 0 WHERE pesananID='$pesananID'";
    
    if ($conn->query($updateQuery) === TRUE) {
        header("Location: orders.php"); // Redirect to the order list
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>
