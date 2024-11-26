<?php
// Include database configuration
include "config.php";
require('fpdf186/fpdf.php');

// Retrieve filter dates from POST
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : '';

// Build query with date filters
$query = "SELECT pembayaran.*, orders.*, menu.menuID, menu.namaMenu, order_details.jumlah, 
                 DATE(pembayaran.tanggalPembayaran) AS tanggalPembayaran
          FROM pembayaran
          JOIN orders ON pembayaran.pesananID = orders.pesananID
          JOIN order_details ON orders.pesananID = order_details.pesananID
          JOIN menu ON order_details.menuID = menu.menuID
          WHERE orders.payed = 1";




$conditions = [];

if (!empty($startDate)) {
    $startDate = $conn->real_escape_string($startDate);
    $conditions[] = "tanggalPembayaran >= '$startDate'";
}

if (!empty($endDate)) {
    $endDate = $conn->real_escape_string($endDate);
    $conditions[] = "tanggalPembayaran <= '$endDate'";
}

if (count($conditions) > 0) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

// Execute the query
$result = mysqli_query($conn, $query);

// Initialize FPDF and set up PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Laporan Penjualan', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, "Periode: $startDate - $endDate", 0, 1, 'C');

// Table headers
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 10, 'No', 1);
$pdf->Cell(25, 10, 'Pembayaran ID', 1);
$pdf->Cell(25, 10, 'Pesanan ID', 1);
$pdf->Cell(35, 10, 'Metode Bayar', 1);
$pdf->Cell(30, 10, 'Tanggal', 1);
$pdf->Cell(30, 10, 'Jumlah (Rp)', 1);
$pdf->Cell(50, 10, 'Nama Menu', 1); // Lebar lebih besar
$pdf->Cell(15, 10, 'Jumlah', 1);
$pdf->Ln();

$no = 1;
$totalPembayaran = 0;

if ($result->num_rows > 0) {
    $pdf->SetFont('Arial', '', 10);

    while ($row = $result->fetch_assoc()) {
        // Potong teks panjang untuk menjaga kerapian
        $metodePembayaran = strlen($row['metodePembayaran']) > 20 ? substr($row['metodePembayaran'], 0, 20) . '...' : $row['metodePembayaran'];
        
        // Isi tabel
        $pdf->Cell(10, 10, $no, 1);
        $pdf->Cell(25, 10, $row['pembayaranID'], 1);
        $pdf->Cell(25, 10, $row['pesananID'], 1);
        $pdf->Cell(35, 10, $metodePembayaran, 1);
        $pdf->Cell(30, 10, $row['tanggalPembayaran'], 1);
        $pdf->Cell(30, 10, number_format($row['jumlahPembayaran'], 2, ',', '.'), 1);
        $pdf->Cell(20, 10, $row['namaMenu'], 1);
        $pdf->Cell(15, 10, $row['jumlah'], 1);
        $pdf->Ln();

        $totalPembayaran += $row['jumlahPembayaran'];
        $no++;

        // Periksa apakah sudah mencapai batas halaman
        if ($pdf->GetY() > 260) { 
            $pdf->AddPage();
            // Tambahkan header ulang
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(10, 10, 'No', 1);
            $pdf->Cell(25, 10, 'Pembayaran ID', 1);
            $pdf->Cell(25, 10, 'Pesanan ID', 1);
            $pdf->Cell(35, 10, 'Metode Bayar', 1);
            $pdf->Cell(30, 10, 'Tanggal', 1);
            $pdf->Cell(30, 10, 'Jumlah (Rp)', 1);
            $pdf->Cell(20, 10, 'Menu ID', 1);
            $pdf->Cell(15, 10, 'Jumlah', 1);
            $pdf->Ln();
        }
    }

    // Total di akhir tabel
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(155, 10, 'Total Pendapatan:', 1, 0, 'R');
    $pdf->Cell(30, 10, number_format($totalPembayaran, 2, ',', '.'), 1, 1, 'R');
} else {
    $pdf->Cell(0, 10, 'No data available for the selected dates.', 1, 1, 'C');
}


// Close database connection
$conn->close();

// Output PDF to browser
$pdf->Output('D', 'laporan_penjualan.pdf');
exit();
?>
