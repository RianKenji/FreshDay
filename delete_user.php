<?php
session_start(); // Start the session
include "config.php"; 

if (isset($_GET['hapus']) && !empty($_GET['hapus'])) { // Ensure 'hapus' is set and not empty
    $userID = $_GET['hapus']; // Assuming userID is a varchar as well

    // Check if userID is valid, e.g., within acceptable varchar length
    if (strlen($userID) <= 255) { // Adjust to the max length allowed in the database
        $stmt = $conn->prepare("DELETE FROM users WHERE userID = ?");
        $stmt->bind_param("s", $userID); // Use "s" as userID is varchar

        if ($stmt->execute()) {
            $_SESSION['notification'] = ['type' => 'success', 'message' => 'USER SUCCESSFULLY DELETED']; // Save success message to session
        } else {
            $_SESSION['notification'] = ['type' => 'danger', 'message' => 'ERROR: Unable to delete user.']; // Save error message to session
        }

        $stmt->close(); // Close the prepared statement
    } else {
        $_SESSION['notification'] = ['type' => 'danger', 'message' => 'ERROR: Invalid user ID.']; // Error message if userID is too long
    }
    
    mysqli_close($conn); // Close the connection
    header('Location: customer.php'); // Redirect to the users page
    exit(); // Ensure exit after redirect
} else {
    $_SESSION['notification'] = ['type' => 'danger', 'message' => 'ERROR: User ID is missing.']; // Error if ID is missing
    header('Location: customer.php'); // Redirect to the users page
    exit();
}
?>
