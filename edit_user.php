<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID = $_POST['userID'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Update the admin details in the database
    $updateQuery = "UPDATE users SET username='$username', email='$email', phone='$phone' WHERE userID='$userID' AND userType='Admin'";
    
    if ($conn->query($updateQuery) === TRUE) {
        header("Location: admin.php?status=success");
    } else {
        echo "Error: " . $conn->error;
    }
    
    $conn->close();
}
?>
