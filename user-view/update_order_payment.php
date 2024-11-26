<?php
include '../config.php'; // File konfigurasi database

// Ambil data dari permintaan
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['pesananID'])) {
    $pesananID = $data['pesananID'];

    // Query untuk memperbarui kolom payed menjadi true
    $query = "UPDATE orders SET payed = TRUE WHERE pesananID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $pesananID);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update payment status.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

$conn->close();
?>
