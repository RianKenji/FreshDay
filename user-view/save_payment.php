<?php
include '../config.php'; // Koneksi ke database

// Dapatkan data JSON dari permintaan POST
$data = json_decode(file_get_contents("php://input"), true);

// Validasi apakah semua data yang dibutuhkan ada
if (isset($data['pembayaranID'], $data['pesananID'], $data['metodePembayaran'], $data['tanggalPembayaran'], $data['jumlahPembayaran'])) {
    $pembayaranID = $data['pembayaranID'];
    $pesananID = $data['pesananID'];
    $metodePembayaran = $data['metodePembayaran'];
    $tanggalPembayaran = $data['tanggalPembayaran'];
    $jumlahPembayaran = $data['jumlahPembayaran'];

    // Simpan data ke dalam tabel pembayaran
    $query = "INSERT INTO pembayaran (pembayaranID, pesananID, metodePembayaran, tanggalPembayaran, jumlahPembayaran) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $pembayaranID, $pesananID, $metodePembayaran, $tanggalPembayaran, $jumlahPembayaran);

    if ($stmt->execute()) {
        // Jika berhasil
        echo json_encode(["success" => true]);
    } else {
        // Jika gagal
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }

    // Tutup statement dan koneksi
    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid data received"]);
}

$conn->close();
?>
