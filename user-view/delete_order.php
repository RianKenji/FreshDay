<?php
include '../config.php'; // Koneksi database

header('Content-Type: application/json');

// Ambil data JSON dari permintaan
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['pesananID'])) {
    $pesananID = $data['pesananID'];

    // Query untuk menghapus pesanan dari database
    $queryDeleteOrder = "DELETE FROM orders WHERE pesananID = ?";
    $stmt = $conn->prepare($queryDeleteOrder);
    $stmt->bind_param("s", $pesananID);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'pesananID tidak diberikan.']);
}

$conn->close();
?>
