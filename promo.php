<?php
require 'header.php';
include('config.php');

// Ambil data promo dari database
$sql = "SELECT * FROM promos";
$stmt = $conn->prepare($sql);
$stmt->execute();
$promos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Promo</title>
    <!-- Menambahkan link ke file CSS Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJ6E3i0i3gkzVj4I4bm8h2+zdJN4E/ALH59R2FZYlFbZz8Ob9UN9kKhXZzGx" crossorigin="anonymous">
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Daftar Promo</h1>
    <a href="add_promo.php" class="btn btn-primary mb-3">Tambah Promo</a>
    
    <!-- Tabel Promo -->
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Promo ID</th>
                <th>Nama Promo</th>
                <th>Deskripsi</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Harga Promo</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($promos as $promo): ?>
                <tr>
                    <td><?php echo $promo['promoID']; ?></td>
                    <td><?php echo $promo['namaPromo']; ?></td>
                    <td><?php echo $promo['deskripsi']; ?></td>
                    <td><?php echo $promo['tanggalMulai']; ?></td>
                    <td><?php echo $promo['tanggalSelesai']; ?></td>
                    <td>RP <?php echo number_format($promo['hargaPromo'], 2, ',', '.'); ?></td>
                    <td>
                        <a href="edit_promo.php?id=<?php echo $promo['promoID']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_promo.php?id=<?php echo $promo['promoID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus promo ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Menambahkan link ke file JS Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0gjG3b7NwRZ7YBFLovzlf9Jey0ZYgVURnW5y2U/7sTRntxSY" crossorigin="anonymous"></script>
</body>
<?php require 'footer.php'?>
</html>
