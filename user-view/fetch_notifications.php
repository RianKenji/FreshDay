<?php
include '../config.php';

session_start();
$userID = $_SESSION['userID']; // Ambil userID dari session

// Query untuk menggabungkan data berdasarkan pesananID
$sql = "
    SELECT 
        o.pesananID,
        GROUP_CONCAT(CONCAT(m.namaMenu, ' (', od.jumlah, ' pcs)') SEPARATOR ', ') AS detailPesanan,
        o.status,
        o.isRead
    FROM orders o
    JOIN order_details od ON o.pesananID = od.pesananID
    JOIN menu m ON od.menuID = m.menuID
    WHERE o.userID = ? AND o.status IN ('Pesanan Masuk', 'Dikirim', 'Pesanan Selesai') AND o.payed = 1
    GROUP BY o.pesananID, o.status
    ORDER BY o.tanggalPesanan DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $userID);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode($notifications);
?>
