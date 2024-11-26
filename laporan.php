<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        .action-buttons a {
            margin-right: 10px;
        }
        .header-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .filter-buttons .btn {
            margin-right: 5px;
        }
    </style>
</head>
<body>
<?php include "header.php";?>

<div class="container-fluid"> 
    <div class="card shadow mb-4">
        <div class="container mt-5">
            <h1 class="mb-4">Laporan Penjualan</h1>

            <!-- Header Controls with Date Filter and Report Button -->
            <div class="header-controls">
                <form method="GET" action="" class="form-inline">
                    <input type="date" name="start_date" class="form-control mr-2" placeholder="Tanggal Mulai" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
                    <input type="date" name="end_date" class="form-control mr-2" placeholder="Tanggal Akhir" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">
                    <button type="submit" class="btn btn-primary mr-2">Filter</button>

                    <!-- Filter Buttons -->
                    <!-- <div class="filter-buttons">
                        <button type="submit" name="filter" value="today" class="btn btn-info">Hari Ini</button>
                        <button type="submit" name="filter" value="month" class="btn btn-info">Bulan Ini</button>
                        <button type="submit" name="filter" value="year" class="btn btn-info">Tahun Ini</button>
                    </div> -->
                </form>
                
                <!-- Generate Report Button -->
                <form method="POST" action="generate_report.php">
                    <input type="hidden" name="start_date" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
                    <input type="hidden" name="end_date" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">
                    <button type="submit" class="btn btn-success">Generate Report</button>
                </form>
            </div>

            <!-- Tabel Laporan -->
<table class="table table-striped table-bordered">
<thead class="thead-dark">
    <tr>
        <th scope="col">No</th>
        <th scope="col">Pembayaran ID</th>
        <th scope="col">Pesanan ID</th>
        <th scope="col">Nama Pembeli</th>
        <th scope="col">Metode Pembayaran</th>
        <th scope="col">Tanggal Pembayaran</th>
        <th scope="col">Jumlah Pembayaran</th>
        <th scope="col">Detail Order</th>
    </tr>
</thead>

    <tbody>
        <?php
        // Koneksi ke database
        include "config.php";

        // Query default untuk mengambil data pembayaran dan detail order
        $query = "
        SELECT 
            pembayaran.pembayaranID,
            pembayaran.pesananID,
            pembayaran.metodePembayaran,
            pembayaran.tanggalPembayaran,
            pembayaran.jumlahPembayaran,
            order_details.menuID,
            order_details.jumlah,
            order_details.totalHarga,
            menu.namaMenu,
            users.namaDepan,
            users.namaBelakang
        FROM pembayaran
        LEFT JOIN orders ON pembayaran.pesananID = orders.pesananID
        LEFT JOIN order_details ON orders.pesananID = order_details.pesananID
        LEFT JOIN menu ON order_details.menuID = menu.menuID
        LEFT JOIN users ON orders.userID = users.userID
    ";
    

    $conditions = [];
    if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
        $startDate = DateTime::createFromFormat('Y-m-d', $_GET['start_date']);
        if ($startDate) {
            $startDate = $startDate->format('Y-m-d');
            $conditions[] = "pembayaran.tanggalPembayaran >= '$startDate'";
        }
    }
    
    if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
        $endDate = DateTime::createFromFormat('Y-m-d', $_GET['end_date']);
        if ($endDate) {
            $endDate = $endDate->format('Y-m-d 23:59:59');
            $conditions[] = "pembayaran.tanggalPembayaran <= '$endDate'";
        }
    }
    
    if (count($conditions) > 0) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

        $query .= " ORDER BY pembayaran.tanggalPembayaran DESC";

        $result = mysqli_query($conn, $query);
        $totalPembayaran = 0; // Inisialisasi total pembayaran

        if ($result->num_rows > 0) {
            $no = 1;
            $currentPembayaranID = ""; // Untuk mengelompokkan data
            while ($row = $result->fetch_assoc()) {
                if ($currentPembayaranID !== $row['pembayaranID']) {
                    $totalPembayaran += $row['jumlahPembayaran']; // Tambahkan jumlah pembayaran ke total
                    echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['pembayaranID']}</td>
                        <td>{$row['pesananID']}</td>
                        <td>{$row['namaDepan']} {$row['namaBelakang']}</td>
                        <td>{$row['metodePembayaran']}</td>
                        <td>{$row['tanggalPembayaran']}</td>
                        <td>Rp " . number_format($row['jumlahPembayaran'], 2, ',', '.') . "</td>
                        <td>
                            <ul>";
                    $currentPembayaranID = $row['pembayaranID'];
                    $no++;
                }                
                // Cetak detail order
                echo "<li>{$row['namaMenu']} (x{$row['jumlah']}) - Rp " . number_format($row['totalHarga'], 2, ',', '.') . "</li>";

            }
            echo "</ul></td></tr>";
        } else {
            echo "<tr><td colspan='7'>No data found</td></tr>";
        }

        $conn->close();
        ?>
    </tbody>
</table>


        </div>
    </div>
            <!-- Menampilkan Total Pembayaran -->
            <div class="mt-3">
                <h4>Total Pendapatan: Rp <?php echo number_format($totalPembayaran, 2, ',', '.'); ?></h4>
            </div>
</div> <!--penutup container fluid-->

<!-- Bootstrap JS and dependencies (Popper.js and jQuery) -->

<?php include "footer.php";?>

</body>
</html>
