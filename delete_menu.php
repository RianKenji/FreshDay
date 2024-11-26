<?php
session_start(); // Memulai session
include "config.php"; 

if (isset($_GET['hapus']) && !empty($_GET['hapus'])) { // Memastikan 'hapus' ada dan tidak kosong
    $menuID = $_GET['hapus']; // Tidak perlu konversi ke integer karena tipe varchar

    // Cek apakah menuID valid, misalnya tidak terlalu panjang sesuai panjang varchar di database
    if (strlen($menuID) <= 255) { // Sesuaikan panjang dengan panjang maksimal yang diperbolehkan di database
        $stmt = $conn->prepare("DELETE FROM menu WHERE menuID = ?");
        $stmt->bind_param("s", $menuID); // Menggunakan "s" karena menuID adalah varchar

        if ($stmt->execute()) {
            $_SESSION['notification'] = ['type' => 'success', 'message' => 'DATA BERHASIL DIHAPUS']; // Simpan pesan ke session
        } else {
            $_SESSION['notification'] = ['type' => 'danger', 'message' => 'ERROR: Unable to delete data.']; // Simpan pesan error ke session
        }

        $stmt->close(); // Tutup prepared statement
    } else {
        $_SESSION['notification'] = ['type' => 'danger', 'message' => 'ERROR: Invalid menu ID.']; // Pesan error jika menuID terlalu panjang
    }
    
    mysqli_close($conn); // Tutup koneksi
    header('Location: menu.php'); // Redirect ke menu.php
    exit(); // Pastikan untuk keluar setelah redirect
} else {
    $_SESSION['notification'] = ['type' => 'danger', 'message' => 'ERROR: Menu ID is missing.']; // Jika ID tidak ada
    header('Location: menu.php'); // Redirect ke menu.php
    exit();
}
?>
