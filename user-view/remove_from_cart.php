<?php
// remove_from_cart.php

// Start the session to access session variables (if needed)
include "../config.php";

// Check if keranjangID is set in the POST request
if (isset($_POST['keranjangID'])) {
    // Cast keranjangID to an integer
    $keranjangID = (int) $_POST['keranjangID'];
    
    // Prepare the DELETE SQL statement
    $sql = "DELETE FROM keranjang WHERE keranjangID = $keranjangID";
    
    // Execute the statement and check for success
    if ($conn->query($sql) === TRUE) {
        // Redirect to the cart page with a success message
        header("Location: cart.php?message=Item removed successfully.");
        exit();
    } else {
        // Redirect to the cart page with an error message
        header("Location: cart.php?message=Error removing item: " . $conn->error);
        exit();
    }
} else {
    // Redirect if keranjangID is not set
    header("Location: cart.php?message=No item specified for removal.");
    exit();
}

// Close the database connection
$conn->close();
?>
