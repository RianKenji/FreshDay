<?php
include '../config.php';

session_start();
$userID = $_SESSION['userID']; // Ambil userID dari session

// Query untuk menghitung notifikasi belum dibaca
$sql = "SELECT COUNT(*) AS unreadCount FROM orders WHERE userID = ? AND isRead = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $userID);
$stmt->execute();
$result = $stmt->get_result();

$unreadCount = 0;
if ($row = $result->fetch_assoc()) {
    $unreadCount = $row['unreadCount'];
}

// Kirim hasil sebagai JSON
echo json_encode(['unreadCount' => $unreadCount]);
?>
