<!DOCTYPE html>
<html lang="en">
<link rel="icon" type="image/x-icon" href="img/favicon.ico">

<Body>
    <?php include "header.php";?>
    <?php
// Database connection
include 'config.php'; // Ensure config.php has your database credentials

// Query to get total monthly earnings
$query = "SELECT SUM(jumlahPembayaran) AS total_monthly_earnings FROM pembayaran WHERE MONTH(tanggalPembayaran) = MONTH(CURRENT_DATE()) AND YEAR(tanggalPembayaran) = YEAR(CURRENT_DATE())";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$query2 = "SELECT SUM(jumlahPembayaran) AS total_annual_earnings FROM pembayaran WHERE YEAR(tanggalPembayaran) = YEAR(CURRENT_DATE())";
$result2 = mysqli_query($conn, $query2);
$row2 = mysqli_fetch_assoc($result2);

$query3 = "SELECT SUM(jumlahPembayaran) AS total_daily_earnings FROM pembayaran WHERE DATE(tanggalPembayaran) = CURRENT_DATE()";
$result3 = mysqli_query($conn, $query3);
$row3 = mysqli_fetch_assoc($result3);

$query4 = "SELECT COUNT(pesananID) AS total_orders_today FROM orders WHERE DATE(tanggalPesanan) = CURRENT_DATE()";
$result4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($result4);

$annualEarnings = $row2['total_annual_earnings'] ?? 0; // Default to 0 if no result
$monthlyEarnings = $row['total_monthly_earnings'] ?? 0; // Default to 0 if no result
$dailyEarnings = $row3['total_daily_earnings'] ?? 0; // Default to 0 if no result
$totalOrdersToday = $row4['total_orders_today'] ?? 0; // Default to 0 if no result



$query3 = "
    SELECT MONTH(tanggalPembayaran) AS month, SUM(jumlahPembayaran) AS total_payment
    FROM pembayaran
    WHERE YEAR(tanggalPembayaran) = YEAR(CURRENT_DATE())
    GROUP BY MONTH(tanggalPembayaran)
    ORDER BY MONTH(tanggalPembayaran)
";
$result3 = mysqli_query($conn, $query3);

// Debugging: Check the result
if (!$result3) {
    die("Query failed: " . mysqli_error($conn));
}

$data = [];
while ($row3 = mysqli_fetch_assoc($result3)) {
    $data[] = $row3;
}

$query_popular_menu = "SELECT namaMenu, SUM(order_details.jumlah) AS popularity, gambar 
                       FROM order_details 
                       JOIN menu ON order_details.menuID = menu.menuID 
                       GROUP BY order_details.menuID 
                       ORDER BY popularity DESC 
                       LIMIT 1"; // Fetch only the top 1 popular menu item
$popular_menu = $conn->query($query_popular_menu);



?>




                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                        <!-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a> -->
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                       <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Pendapatan Bulanan</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">RP <?php echo number_format($monthlyEarnings, 0, ',', '.'); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Pendapatan Tahunan </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?php echo number_format($annualEarnings, 0, ',', '.');?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pendapatan Harian
                                            </div>
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-auto">
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?php echo number_format($dailyEarnings, 0, ',', '.');?></div>
                                                </div>
                                                <div class="col">
                                                    <!-- <div class="progress progress-sm mr-2">
                                                        <div class="progress-bar bg-info" role="progressbar"
                                                            style="width: 50%" aria-valuenow="50" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Requests Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Pesanan Hari Ini</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($totalOrdersToday, 0, ',', '.');?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Row -->

                    <div class="row">

                        <!-- Area Chart -->
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Earnings Overview</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="newAreaChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Pie Chart -->

                        <?php
                        include('config.php'); // Pastikan file config.php berisi koneksi ke database Anda

                        // Query untuk mengambil jumlah pembayaran berdasarkan metode pembayaran
                        $query = "SELECT metodePembayaran, COUNT(*) as total FROM pembayaran GROUP BY metodePembayaran";
                        $result = mysqli_query($conn, $query);

                        $paymentData = [];
                        while ($row = mysqli_fetch_assoc($result)) {
                            $paymentData[] = $row;
                        }

                        mysqli_close($conn); // Tutup koneksi database
                        ?>

                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Revenue Sources</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <!-- <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Dropdown Header:</div>
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                        </div> -->
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2" style="margin-left:25px;">
                                        <canvas id="newPieChart"></canvas>
                                    </div>
                                    <div class="mt-4 text-center small">
                                        <!-- <span class="mr-2">
                                            <i class="fas fa-circle text-primary"></i> Direct
                                        </span>
                                        <span class="mr-2">
                                            <i class="fas fa-circle text-success"></i> Social
                                        </span>
                                        <span class="mr-2">
                                            <i class="fas fa-circle text-info"></i> Referral
                                        </span> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Content Column -->
                        <div class="col-lg-6 mb-4">

                            <!-- Project Card Example -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Latest Orders</h6>
                                </div>
                                <!-- <div class="card-body">
                                    <h4 class="small font-weight-bold">Server Migration <span
                                            class="float-right">20%</span></h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 20%"
                                            aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h4 class="small font-weight-bold">Sales Tracking <span
                                            class="float-right">40%</span></h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 40%"
                                            aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h4 class="small font-weight-bold">Customer Database <span
                                            class="float-right">60%</span></h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar" role="progressbar" style="width: 60%"
                                            aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h4 class="small font-weight-bold">Payout Details <span
                                            class="float-right">80%</span></h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 80%"
                                            aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h4 class="small font-weight-bold">Account Setup <span
                                            class="float-right">Complete!</span></h4>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%"
                                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div> -->
                            </div>

                            <!-- Color System -->
                            <!-- <div class="row">
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-primary text-white shadow">
                                        <div class="card-body">
                                            Primary
                                            <div class="text-white-50 small">#4e73df</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-success text-white shadow">
                                        <div class="card-body">
                                            Success
                                            <div class="text-white-50 small">#1cc88a</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-info text-white shadow">
                                        <div class="card-body">
                                            Info
                                            <div class="text-white-50 small">#36b9cc</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-warning text-white shadow">
                                        <div class="card-body">
                                            Warning
                                            <div class="text-white-50 small">#f6c23e</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-danger text-white shadow">
                                        <div class="card-body">
                                            Danger
                                            <div class="text-white-50 small">#e74a3b</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-secondary text-white shadow">
                                        <div class="card-body">
                                            Secondary
                                            <div class="text-white-50 small">#858796</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-light text-black shadow">
                                        <div class="card-body">
                                            Light
                                            <div class="text-black-50 small">#f8f9fc</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-dark text-white shadow">
                                        <div class="card-body">
                                            Dark
                                            <div class="text-white-50 small">#5a5c69</div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->

                        </div>

                        <div class="col-lg-6 mb-4">

                            <!-- Illustrations -->
                            <!-- Most Popular Menu Item -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Most Popular Menu Item</h6>
                                </div>
                                <div class="card-body">
                                    <?php if ($row_pop_menu = $popular_menu->fetch_assoc()): ?>
                                        <div class="text-center">
                                            <!-- Display the image for the menu item -->
                                            <img src="img/<?php echo $row_pop_menu['gambar']; ?>" class="img-fluid px-3 px-sm-4" style="width: 15rem;" alt="<?php echo $row_pop_menu['namaMenu']; ?>">
                                        </div>
                                        <p><strong><?php echo $row_pop_menu['namaMenu']; ?></strong></p>
                                        <p>Popularity: <?php echo $row_pop_menu['popularity']; ?> Botol Terjual</p>
                                    <?php else: ?>
                                        <p>No popular menu items found.</p>
                                    <?php endif; ?>
                                </div>
                            </div>


                            <!-- Approach -->

                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->
<?php include "footer.php";?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php
// Initialize an array with all months (1-12)
$allMonths = range(1, 12);
$monthlyPayments = array_fill(0, 12, 0); // Default all payments to 0

// Fill the monthlyPayments array with actual data from the database
foreach ($data as $row) {
    $monthlyPayments[$row['month'] - 1] = $row['total_payment']; // Indexing starts from 0
}
?>

<script>
// Data from PHP (with empty months handled)
const monthlyPayments = <?php echo json_encode($monthlyPayments); ?>;
console.log("Monthly Payments: ", monthlyPayments); // Debugging

// Array of month names in Indonesian
const monthNames = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

// Chart.js code
var ctx = document.getElementById("newAreaChart").getContext("2d");
var myLineChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: monthNames, // Use month names instead of numbers
        datasets: [{
            label: "Pendapatan Bulanan (RP)",
            data: monthlyPayments, // The total payments for each month
            backgroundColor: "rgba(2,117,216,0.2)",
            borderColor: "rgba(2,117,216,1)",
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                ticks: {
                    beginAtZero: true,
                    stepSize: 1
                },
                title: {
                    display: true,
                    text: 'Bulan'
                }
            },
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Pendapatan (RP)'
                }
            }
        }
    }
});
</script>

<script>
// Data dari PHP ke JavaScript
var paymentData = <?php echo json_encode($paymentData); ?>;
</script>
<script>
// Data dari PHP ke JavaScript
var paymentData = <?php echo json_encode($paymentData); ?>;
</script>
<script>
// Ambil label dan data dari array paymentData
var labels = [];
var data = [];
var total = 0;

paymentData.forEach(function(item) {
    labels.push(item.metodePembayaran);  // Nama metode pembayaran
    data.push(item.total);               // Jumlah transaksi untuk masing-masing metode
    total += item.total;                 // Hitung total untuk persentase
});

// Membuat chart menggunakan Chart.js
var ctx = document.getElementById("newPieChart").getContext("2d");
var newPieChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: labels,  // Label untuk setiap metode pembayaran
        datasets: [{
            data: data,  // Data untuk jumlah setiap metode pembayaran
            backgroundColor: ['#007bff', '#28a745', '#17a2b8', '#ffc107', '#dc3545', '#6f42c1'],  // Warna untuk setiap bagian pie chart
            hoverBackgroundColor: ['#0056b3', '#218838', '#138496', '#d39e00', '#bd2130', '#5a2d8c'], // Warna saat hover
            borderColor: '#ffffff',  // Warna border
            borderWidth: 2          // Ketebalan border
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',  // Posisikan legenda di bawah
                labels: {
                    boxWidth: 10,   // Ukuran kotak legenda (dot kecil)
                    boxHeight: 10,  // Tinggi kotak legenda
                    font: {
                        size: 12    // Ukuran font label
                    },
                    padding: 15     // Jarak antara legenda
                }
            },
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        var value = tooltipItem.raw;
                        var percentage = ((value / total) * 100).toFixed(2);
                        return tooltipItem.label + ': ' + value + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});
</script>


</Body>

