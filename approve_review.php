<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reviewID'])) {
    $reviewID = $_POST['reviewID'];

    // Update ulasan menjadi testimoni
    $sql = "UPDATE reviews2 SET isTestimonial = TRUE WHERE reviewID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $reviewID);

    if ($stmt->execute()) {
        echo "<script>alert('Ulasan berhasil disetujui sebagai testimoni!'); window.location.href='reviews2.php';</script>";
    } else {
        echo "<script>alert('Gagal menyetujui ulasan.'); window.location.href='reviews2.php';</script>";
    }
    $stmt->close();
}

$conn->close();
?>
