<?php
// Konfigurasi database
include 'config.php';
include 'header.php';

// Mendapatkan semua ulasan dengan nama pengguna
$sql = "SELECT r.reviewID, r.pesananID, r.userID, r.rating, r.comment, r.reviewDate, r.isTestimonial, u.namaDepan, u.namaBelakang 
        FROM reviews2 r
        JOIN users u ON r.userID = u.userID
        ORDER BY r.reviewDate DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Ulasan</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Daftar Ulasan</h1>
        <table class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>ID Pesanan</th>
                    <th>Nama User</th>
                    <th>Rating</th>
                    <th>Komentar</th>
                    <th>Tanggal</th>
                    <th>Status Testimoni</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['pesananID']); ?></td>
                            <td><?= htmlspecialchars($row['namaDepan']) . ' ' . htmlspecialchars($row['namaBelakang']); ?></td>
                            <td><?= htmlspecialchars($row['rating']); ?> / 5</td>
                            <td><?= htmlspecialchars($row['comment']); ?></td>
                            <td><?= htmlspecialchars($row['reviewDate']); ?></td>
                            <td><?= $row['isTestimonial'] ? 'Testimoni' : 'Belum'; ?></td>
                            <td>
                                <?php if (!$row['isTestimonial']): ?>
                                    <form method="POST" action="approve_review.php" style="display: inline;">
                                        <input type="hidden" name="reviewID" value="<?= $row['reviewID']; ?>">
                                        <button type="submit" class="btn btn-success btn-sm">Setujui</button>
                                    </form>
                                <?php else: ?>
                                    <span class="badge bg-success">Sudah Testimoni</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Belum ada ulasan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS & Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?php include 'footer.php';?>
</html>
