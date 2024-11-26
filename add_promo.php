<?php include "config.php";?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Promo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
if (isset($_POST['submit'])) {
    // Ambil data dari form
    $promoID = uniqid('P'); // generate unique promoID
    $namaPromo = $_POST['namaPromo'];
    $deskripsi = $_POST['deskripsi'];
    $tanggalMulai = $_POST['tanggalMulai'];
    $tanggalSelesai = $_POST['tanggalSelesai'];
    $hargaPromo = $_POST['hargaPromo'];

    // Masukkan data promo ke tabel promos
    $stmt = $conn->prepare("INSERT INTO promos (promoID, namaPromo, deskripsi, tanggalMulai, tanggalSelesai, hargaPromo) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssd", $promoID, $namaPromo, $deskripsi, $tanggalMulai, $tanggalSelesai, $hargaPromo);
    $stmt->execute();

    // Ambil semua menu yang dipilih
    $menuIDs = $_POST['menuID']; // Ini adalah array menuID yang dipilih

    // Menambahkan menu yang dipilih ke tabel promo_items
    foreach ($menuIDs as $menuID) {
        $stmt = $conn->prepare("INSERT INTO promo_items (promoID, menuID) VALUES (?, ?)");
        $stmt->bind_param("ss", $promoID, $menuID);
        $stmt->execute();
    }

    echo "Promo berhasil ditambahkan!";
}



?>
    

  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow">
          <div class="card-body">
            <h3 class="card-title text-center mb-4">Tambah Promo Baru</h3>
            <form method="POST" action="add_promo.php">
    <div class="form-group">
        <label for="namaPromo">Nama Promo</label>
        <input type="text" name="namaPromo" id="namaPromo" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="deskripsi">Deskripsi</label>
        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" required></textarea>
    </div>
    <div class="form-group">
        <label for="tanggalMulai">Tanggal Mulai</label>
        <input type="date" name="tanggalMulai" id="tanggalMulai" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="tanggalSelesai">Tanggal Selesai</label>
        <input type="date" name="tanggalSelesai" id="tanggalSelesai" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="hargaPromo">Harga Promo</label>
        <input type="number" step="0.01" name="hargaPromo" id="hargaPromo" class="form-control" required>
    </div>
    <div class="form-group">
    <label>Pilih Menu</label><br>
    <?php
    $result = $conn->query("SELECT menuID, namaMenu FROM menu");
    while ($row = $result->fetch_assoc()) {
        echo "<input type='checkbox' name='menuID[]' value='" . $row['menuID'] . "'> " . $row['namaMenu'] . "<br>";
    }
    ?>
</div>
    <button type="submit" class="btn btn-primary" name="submit">Tambah Promo</button>
</form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php require "footer.php"; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
