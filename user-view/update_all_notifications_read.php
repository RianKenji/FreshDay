<?php
// Include the database configuration file
require '../config.php';  // Adjust the path if necessary

// Ensure that the user is logged in and has a valid userID
session_start();
if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID']; // Assuming userID is stored in the session

    // Prepare the SQL query to mark all unread notifications as read
    $query = "UPDATE orders SET isRead = TRUE WHERE userID = ? AND isRead = FALSE";
    
    // Prepare the statement
    if ($stmt = $conn->prepare($query)) {
        // Bind the parameter
        $stmt->bind_param("s", $userID);  // "s" means the parameter is a string

        // Execute the statement
        if ($stmt->execute()) {
            // Check if any rows were affected
            if ($stmt->affected_rows > 0) {
                echo json_encode(['status' => 'success', 'message' => 'All unread notifications marked as read.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No unread notifications found.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error executing query.']);
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error preparing query.']);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
}

// Close the database connection
$conn->close();
?>
