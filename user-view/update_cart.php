<?php
session_start(); // Start the session

include "../config.php";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the cart ID and new quantity from the form
    $keranjangID = $_POST['keranjangID'];
    $new_quantity = intval($_POST['new_quantity']); // Ensure quantity is an integer

    // Check if the quantity is valid
    if ($new_quantity < 1) {
        // Redirect back with an error message if quantity is invalid
        $_SESSION['error'] = "Quantity must be at least 1.";
        header("Location: cart.php"); // Redirect to cart page
        exit();
    }

    // Prepare and bind
    $stmt = $conn->prepare("UPDATE keranjang SET kuantitasProduk = ? WHERE keranjangID = ?");
    $stmt->bind_param("is", $new_quantity, $keranjangID);

    // Execute the statement
    if ($stmt->execute()) {
        $_SESSION['success'] = "Cart updated successfully.";
    } else {
        $_SESSION['error'] = "Error updating cart: " . $stmt->error;
    }

    // Close statement
    $stmt->close();

    // Close the database connection
    $conn->close();

    // Redirect back to the cart page
    header("Location: cart.php");
    exit();
}
?>
