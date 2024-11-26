<?php
include '../config.php';

if (isset($_GET['pesananID'])) {
    $pesananID = $_GET['pesananID'];

    $queryOrderDetails = "SELECT od.menuID, m.namaMenu, od.jumlah, od.totalHarga, m.gambar
                          FROM order_details od 
                          JOIN menu m ON od.menuID = m.menuID 
                          WHERE od.pesananID = ?";
    $stmt = $conn->prepare($queryOrderDetails);
    $stmt->bind_param("s", $pesananID);
    $stmt->execute();
    $orderDetailsResult = $stmt->get_result();

    while ($detailRow = $orderDetailsResult->fetch_assoc()) {
        echo '<tr>
                <td><img src="../img/' . htmlspecialchars($detailRow['gambar']) . '" alt="' . htmlspecialchars($detailRow['namaMenu']) . '" style="width: 60px; height: auto;"></td>
                <td>' . htmlspecialchars($detailRow['namaMenu']) . '</td>
                <td>' . htmlspecialchars($detailRow['jumlah']) . '</td>
                <td>IDR ' . number_format($detailRow['totalHarga'], 0, ',', '.') . '</td>
              </tr>';
    }

    $stmt->close();
}
$conn->close();
?>
