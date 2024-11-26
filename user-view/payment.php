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
$totalAmount = $_GET['total']; // Total tanpa ongkir
$address = urldecode($_GET['address']);
$userID = $_SESSION['userID'];

// API Key OpenCage Geocoder Anda
$apiKey = 'eb728b6c86d84efe81359b6372d3a254'; // Ganti dengan API Key Anda

// Alamat asal dan tujuan
$alamatAsal = 'Citra Garden 7, Jakarta , Indonesia';
$alamatTujuan = urldecode($_GET['address']);

// URL untuk mengakses API OpenCage
function getCoordinates($alamat, $apiKey) {
    $alamat = urlencode($alamat);
    $url = "https://api.opencagedata.com/geocode/v1/json?q=$alamat&key=$apiKey";
    
    // Menangani kesalahan saat mendapatkan respons
    $response = @file_get_contents($url);
    
    // Memeriksa apakah file_get_contents berhasil
    if ($response === FALSE) {
        echo "Gagal mengakses API atau batas penggunaan tercapai.\n";
        return null;
    }

    $data = json_decode($response, true);

    if (isset($data['status']['code']) && $data['status']['code'] == 200 && !empty($data['results'])) {
        return $data['results'][0]['geometry']; // Mengembalikan koordinat
    } else {
        echo "Gagal mendapatkan koordinat untuk alamat: $alamat\n";
        return null;
    }
}

// Menghitung jarak antara dua titik menggunakan rumus Haversine
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // Radius bumi dalam kilometer

    // Menghitung perbedaan koordinat
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    // Menghitung jarak menggunakan rumus Haversine
    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $earthRadius * $c; // Jarak dalam kilometer

    return $distance;
}

// Mendapatkan koordinat dari kedua alamat
$coordinatesAsal = getCoordinates($alamatAsal, $apiKey);
$coordinatesTujuan = getCoordinates($alamatTujuan, $apiKey);

if ($coordinatesAsal && $coordinatesTujuan) {
    // Menghitung jarak jika kedua koordinat berhasil ditemukan
    $lat1 = $coordinatesAsal['lat'];
    $lon1 = $coordinatesAsal['lng'];
    $lat2 = $coordinatesTujuan['lat'];
    $lon2 = $coordinatesTujuan['lng'];

    $jarak = intval(calculateDistance($lat1, $lon1, $lat2, $lon2));

    if ($jarak > 30) {
        // Notifikasi jika jarak melebihi 30 km
        echo "<script>
                alert('Mohon maaf, kami tidak melayani pengiriman lebih dari 30 KM.');
                window.history.back();
              </script>";
        exit;
    } else {
        // echo "Jarak antara '$alamatAsal' dan '$alamatTujuan' adalah: " . round($jarak, 2) . " km\n";
    }
} else {
    echo "Gagal mendapatkan koordinat untuk salah satu atau kedua alamat.\n";
}



// Ambil total jumlah item dari pesanan
$queryTotalItems = "
    SELECT SUM(jumlah) AS totalItems 
    FROM order_details 
    WHERE pesananID = ?";
$totalItemsStmt = $conn->prepare($queryTotalItems);
$totalItemsStmt->bind_param("s", $pesananID);
$totalItemsStmt->execute();
$totalItemsResult = $totalItemsStmt->get_result();
$totalItems = $totalItemsResult->fetch_assoc()['totalItems'];

// Ongkir (shipping cost) bebas ongkir jika total item > 30
if ($totalItems >= 30) {
    $shippingCost = 0;
} else {
    $shippingCost = ($jarak / 5) * 5000; // Perhitungan ongkir tetap
}

// Total amount yang termasuk ongkir
$totalAmountWithShipping = $totalAmount + $shippingCost;

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
    SELECT od.menuID, od.jumlah, od.totalHarga, m.namaMenu, m.gambar
    FROM order_details od 
    JOIN menu m ON od.menuID = m.menuID 
    WHERE od.pesananID = ?";
$orderStmt = $conn->prepare($queryOrderDetails);
$orderStmt->bind_param("s", $pesananID);
$orderStmt->execute();
$orderResult = $orderStmt->get_result();

$queryItemDetails = "
    SELECT 
        od.menuID, 
        m.namaMenu AS name, 
        od.jumlah AS quantity, 
        od.harga AS price 
    FROM order_details od
    INNER JOIN menu m ON od.menuID = m.menuID
    WHERE od.pesananID = ?
";

$stmt = $conn->prepare($queryItemDetails);
$stmt->bind_param("s", $pesananID);
$stmt->execute();
$result = $stmt->get_result();

$item_details = [];

// Menambahkan detail item dari database
while ($row = $result->fetch_assoc()) {
    $item_details[] = [
        'id'       => $row['menuID'],
        'name'     => $row['name'],
        'quantity' => (int)$row['quantity'],
        'price'    => (int)$row['price'],
    ];
}

// Menambahkan ongkir sebagai item baru
$item_details[] = [
    'id'       => 'shipping', // ID untuk ongkir
    'name'     => 'Ongkos Kirim', // Nama item ongkir
    'quantity' => 1, // Ongkir hanya satu item
    'price'    => (int)$shippingCost, // Nilai ongkir yang sudah dihitung sebelumnya
];


// Siapkan data transaksi untuk Midtrans
$params = [
    'transaction_details' => [
        'order_id' => $pesananID,
        'gross_amount' => (int)$totalAmountWithShipping, // Gunakan totalAmount dengan ongkir
    ],
    'item_details' => $item_details, // Dari query sebelumnya
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
        .back-button {
            background-color: red;
            border: none;
            padding: 10px;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        .back-button:hover {
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
                        <td><img src="../img/<?php echo htmlspecialchars($order['gambar']); ?>" alt="Menu Image" class="menu-image"></td>
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
            <!-- Total Payment Section -->
            <h4>Total Pembayaran</h4>
            <div class="card p-3 mb-3">
                <p><strong>Subtotal:</strong> RP <?php echo htmlspecialchars(number_format($totalAmount, 0, ',', '.')); ?></p>
                <p><strong>Ongkir:</strong> RP <?php echo number_format($shippingCost, 0, ',', '.'); ?></p>
                <p><strong>Total Pembayaran:</strong> RP <?php echo htmlspecialchars(number_format($totalAmountWithShipping, 0, ',', '.')); ?></p>
            </div>

            <!-- Customer Details Section -->
            <h4>Detail Pelanggan</h4>
            <div class="card p-3">
                <p><strong>Nama:</strong> <?php echo htmlspecialchars($customer['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
                <p><strong>Telepon:</strong> <?php echo htmlspecialchars($customer['phone']); ?></p>
                <p><strong>Alamat:</strong> <?php echo htmlspecialchars($address); ?></p>
            </div>

            <button id="pay-button" class="pay-button mt-3">Bayar Sekarang</button>
            <button id="back-button" class="back-button mt-3">Kembali</button>
            </div>
    </div>
</div>

<script type="text/javascript">
    document.getElementById('pay-button').onclick = function() {
        snap.pay('<?php echo $snapToken; ?>', {
            onSuccess: function(result) {

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
                        // Perbarui kolom payed di tabel orders
                        fetch('update_order_payment.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                pesananID: '<?php echo $pesananID; ?>'
                            })
                        })
                        .then(orderResponse => orderResponse.json())
                        .then(orderData => {
                            if (orderData.success) {

                                // Hapus data keranjang dari database
                                fetch('clear_cart.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        userID: '<?php echo $userID; ?>'
                                    })
                                })
                                .then(cartResponse => cartResponse.json())
                                .then(cartData => {
                                    if (cartData.success) {
                                        window.location.href = "cart.php"; // Redirect ke halaman sukses
                                    } else {
                                        alert("Error clearing cart.");
                                    }
                                })
                                .catch(error => console.error('Error clearing cart:', error));
                            } else {
                                alert("Error updating order payment status.");
                            }
                        })
                        .catch(error => console.error('Error updating order payment status:', error));
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

    document.getElementById('back-button').onclick = function() {
    const pesananID = '<?php echo $pesananID; ?>';
    if (confirm("Apakah Anda yakin ingin kembali? Data pesanan akan dihapus.")) {
        // Kirim permintaan untuk menghapus pesanan
        fetch('delete_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ pesananID: pesananID })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Jika berhasil, kembali ke halaman sebelumnya
                window.location.href = 'cart.php';
            } else {
                alert("Gagal menghapus pesanan.");
            }
        })
        .catch(error => console.error('Error:', error));
    }
};

</script>

</body>
</html>
<?php
$stmt->close();
$orderStmt->close();
$conn->close();
?>
