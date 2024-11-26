<?php
// Include konfigurasi database
include('config.php');

// Cek apakah ada parameter 'hapus' di URL
if (isset($_GET['hapus'])) {
    $pesananID = $_GET['hapus'];

    // Hapus pesanan dari tabel order_details terlebih dahulu jika ada data terkait
    $delete_order_details = "DELETE FROM order_details WHERE pesananID = ?";
    $stmt = $conn->prepare($delete_order_details);
    $stmt->bind_param("s", $pesananID);
    $stmt->execute();

    // Hapus pesanan dari tabel orders
    $delete_order = "DELETE FROM orders WHERE pesananID = ?";
    $stmt = $conn->prepare($delete_order);
    $stmt->bind_param("s", $pesananID);
    if ($stmt->execute()) {
        // Redirect ke halaman sebelumnya atau halaman lain setelah berhasil
        header('Location: orders.php?status=success');
    } else {
        // Redirect ke halaman sebelumnya dengan status error jika gagal
        header('Location: orders.php?status=error');
    }
} else {
    // Jika tidak ada parameter 'hapus' di URL, redirect ke halaman order list
    header('Location: orders.php');
}

?>
