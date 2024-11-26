<?php
require '../vendor/autoload.php'; // Pastikan jalur ini benar
include '../config.php'; // Koneksi database

session_start();

// Set konfigurasi Midtrans
\Midtrans\Config::$serverKey = 'SB-Mid-server-XkR8GqB9M7hegWC96Is76sZm'; // Ganti dengan server key Anda
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

$pesananID = $_GET['orderID'];
$totalAmount = $_GET['total'];
$address = urldecode($_GET['address']);
$userID = $_SESSION['userID'];

// Ambil informasi pelanggan dari database berdasarkan userID
$queryCustomer = "SELECT username, email, phone, alamat FROM users WHERE userID = ?";
$stmt = $conn->prepare($queryCustomer);
$stmt->bind_param("s", $userID);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

if (!$customer) {
    die("Data pelanggan tidak ditemukan.");
}

// Ambil rincian pesanan dari database
$queryOrderDetails = "
    SELECT od.menuID, od.jumlah, od.totalHarga, m.namaMenu, m.imagePath
    FROM order_details od 
    JOIN menu m ON od.menuID = m.menuID 
    WHERE od.pesananID = ?";
$orderStmt = $conn->prepare($queryOrderDetails);
$orderStmt->bind_param("s", $pesananID);
$orderStmt->execute();
$orderResult = $orderStmt->get_result();

// Siapkan data transaksi untuk Midtrans
$params = [
    'transaction_details' => [
        'order_id' => $pesananID,
        'gross_amount' => (int)$totalAmount,
    ],
    'customer_details' => [
        'first_name' => $customer['username'],
        'email' => $customer['email'],
        'phone' => $customer['phone'],
        'shipping_address' => [
            'address' => $customer['alamat'],
        ],
    ],
];

// Dapatkan token Snap dari Midtrans
$snapToken = \Midtrans\Snap::getSnapToken($params);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Pembayaran</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #000;
            color: #fff;
        }
        .card {
            background-color: #333;
            color: #fff;
        }
        .table th, .table td {
            color: #fff;
        }
        .pay-button {
            background-color: #28a745;
            border: none;
            padding: 10px;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        .pay-button:hover {
            background-color: #218838;
        }
        .menu-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }
    </style>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-Z6D3Kyi_J3c2qQll"></script>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Halaman Pembayaran</h2>
    <div class="row">
        <div class="col-md-8">
            <h4>Detail Pesanan</h4>
            <table class="table table-dark table-bordered">
                <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama Menu</th>
                    <th>Jumlah</th>
                    <th>Total Harga</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($order = $orderResult->fetch_assoc()): ?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($order['imagePath']); ?>" alt="Menu Image" class="menu-image"></td>
                        <td><?php echo htmlspecialchars($order['namaMenu']); ?></td>
                        <td><?php echo htmlspecialchars($order['jumlah']); ?></td>
                        <td><?php echo 'RP ' . htmlspecialchars(number_format($order['totalHarga'], 0, ',', '.')); ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="3" class="text-right">Total</th>
                    <th>RP <?php echo htmlspecialchars(number_format($totalAmount, 0, ',', '.')); ?></th>
                </tr>
                </tfoot>
            </table>
        </div>

        <div class="col-md-4">
            <h4>Detail Pelanggan</h4>
            <div class="card p-3">
                <p><strong>Nama:</strong> <?php echo htmlspecialchars($customer['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
                <p><strong>Telepon:</strong> <?php echo htmlspecialchars($customer['phone']); ?></p>
                <p><strong>Alamat:</strong> <?php echo htmlspecialchars($customer['alamat']); ?></p>
            </div>

            <button id="pay-button" class="pay-button mt-3">Bayar Sekarang</button>
        </div>
    </div>
</div>

<script type="text/javascript">
        document.getElementById('pay-button').onclick = function() {
    snap.pay('<?php echo $snapToken; ?>', {
        onSuccess: function(result) {
            alert("Payment Success!");
            
            // Kirim data pembayaran ke server untuk disimpan di database
            fetch('save_payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    pembayaranID: 'PAY' + Math.floor(Math.random() * 1000000).toString().padStart(4, '0'), // Buat ID pembayaran
                    pesananID: '<?php echo $pesananID; ?>',
                    metodePembayaran: result.payment_type,
                    tanggalPembayaran: result.transaction_time,
                    jumlahPembayaran: result.gross_amount
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Payment recorded successfully!");
                    window.location.href = "cart.php"; // Redirect ke halaman sukses
                } else {
                    alert("Error recording payment.");
                }
            })
            .catch(error => console.error('Error:', error));
        },
        onPending: function(result) {
            alert("Payment Pending!");
            console.log(result);
        },
        onError: function(result) {
            alert("Payment Failed!");
            console.log(result);
        },
        onClose: function() {
            alert("Payment was not completed.");
        }
    });
};
    </script>
</body>
</html>
<?php
$stmt->close();
$orderStmt->close();
$conn->close();
?>
