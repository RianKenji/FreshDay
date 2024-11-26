<?php
session_start();
include '../config.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

// Ambil userID dari sesi
$userID = $_SESSION['userID'];

// Update data profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data form
    $namaDepan = $_POST['namaDepan'];
    $namaBelakang = $_POST['namaBelakang'];
    $phone = $_POST['phone'];
    $alamat = $_POST['alamat'];

    // Cek jika ada file gambar yang diupload
    $gambar = null;
    if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] == 0) {
        // Tentukan lokasi penyimpanan gambar
        $targetDir = "../img/";
        $fileName = basename($_FILES['profilePicture']['name']);
        $targetFile = $targetDir . $fileName;

        // Pindahkan file ke direktori tujuan
        if (move_uploaded_file($_FILES['profilePicture']['tmp_name'], $targetFile)) {
            $gambar = $targetFile;
        }
    }

    // Update data pengguna
    $sql = "UPDATE users SET namaDepan = ?, namaBelakang = ?, phone = ?, alamat = ?, gambar = ? WHERE userID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $namaDepan, $namaBelakang, $phone, $alamat, $gambar, $userID);
    $stmt->execute();
    $stmt->close();

    // Redirect ke halaman profil
    header("Location: profile.php");
    exit();
}

$conn->close();
?>
