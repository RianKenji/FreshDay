<?php
include '../config.php'; // File konfigurasi database

// Ambil data dari form
$nama = $_POST['nama'] ?? '';
$email = $_POST['email'] ?? '';
$noHP = $_POST['noHP'] ?? '';
$komentar = $_POST['testimonial'] ?? '';
$kategori = 'Dalam Pertimbangan'; // Ubah sesuai kebutuhan, bisa juga ditambahkan sebagai dropdown pada form
$userID = null; // Jika user login, gunakan ID user

// Validasi data
if (empty($nama) || empty($email) || empty($noHP) || empty($komentar)) {
    die("Semua field harus diisi!");
}

// Query untuk menyimpan data
$query = "INSERT INTO reviews (nama, email, noHP, komentar, kategori, userID) 
          VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($query);
$stmt->bind_param("ssssss", $nama, $email, $noHP, $komentar, $kategori, $userID);

if ($stmt->execute()) {
    header('Location: index.php'); 
} else {
    echo "Gagal mengirim pesan: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
