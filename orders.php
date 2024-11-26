<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order List</title>
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        .menu-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }
        .action-buttons a {
            margin-right: 10px;
        }
        .add-menu-btn {
            background-color: #b49a7d!important;
            color: #fff!important;
        }
        .header-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .menu-image {
            width: 80%;
            height: 100px;
        }
        .badge-status {
            padding: 5px 10px;
            border-radius: 5px;
        }
        .badge-dikelola {
            background-color: #f0ad4e; /* Orange */
        }
        .badge-pending {
            background-color: #ffc107; /* Yellow */
        }
        .badge-dikirim {
            background-color: #5bc0de; /* Light Blue */
        }
        .badge-selesai {
            background-color: #5cb85c; /* Green */
        }
    </style>

</head>
<body>
<?php include "header.php";?>

<div class="container mt-5">
    <h1 class="mb-4">Daftar Pesanan</h1>

    <!-- Header Controls with "Tambah Order" and Search Form -->
    <div class="header-controls">
        <!-- <a href="add_order.php" class="btn add-menu-btn">+ Tambah Order</a> -->
        <form method="GET" action="" class="form-inline">
            <input type="text" name="search" class="form-control mr-2" placeholder="Cari order..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
    
    <!-- Order Table - Responsive for smaller screens -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Order ID</th>
                    <th scope="col">Pelanggan</th>
                    <th scope="col">Status</th>
                    <th scope="col">Tanggal Pesanan</th>
                    <th scope="col">Detail Pesanan</th> <!-- New Column for Order Details -->
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Database connection
                    include "config.php";

                    // Pagination variables
                    $resultsPerPage = 20;
                    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($currentPage - 1) * $resultsPerPage;

                    // Base query for fetching orders
                    $dataOrder = "SELECT o.*, u.userName AS userName FROM orders o 
                                JOIN users u ON o.userID = u.userID 
                                WHERE o.payed = 1
                                ORDER BY o.pesananID DESC";

                    // Check if search query exists and modify $dataOrder
                    if (isset($_GET['search']) && !empty($_GET['search'])) {
                        $search = $conn->real_escape_string($_GET['search']);
                        $dataOrder = "SELECT o.*, u.userName AS userName FROM orders o 
                                    JOIN users u ON o.userID = u.userID 
                                    WHERE o.pesananID LIKE '%$search%'
                                    ORDER BY o.pesananID DESC
                                    LIMIT $offset, $resultsPerPage";
                    } else {
                        $dataOrder .= " LIMIT $offset, $resultsPerPage";
                    }


                // Fetch orders
                $result = mysqli_query($conn, $dataOrder);
                
                if ($result->num_rows > 0) {
                    $no = $offset + 1;
                    while ($row = $result->fetch_assoc()) {
                        // Query to fetch order details for the current pesananID
                        $orderID = $row['pesananID'];
                        $orderDetailsQuery = "
                            SELECT od.menuID, m.namaMenu, od.jumlah, od.totalHarga 
                            FROM order_details od
                            JOIN menu m ON od.menuID = m.menuID
                            WHERE od.pesananID = '$orderID'";
                        $detailsResult = mysqli_query($conn, $orderDetailsQuery);

                        // Determine status label and create dropdown options
                $statusOptions = ['Dibuat', 'Pesanan Masuk', 'Dikirim', 'Pesanan Selesai', 'Batal'];
                $statusDropdown = "<form method='POST' action='update_status.php' style='display:inline;'>
                    <input type='hidden' name='pesananID' value='{$row['pesananID']}'>
                    <select name='status' class='form-control' onchange='this.form.submit()'>";

                foreach ($statusOptions as $status) {
                    $selected = ($row['status'] == $status) ? 'selected' : '';
                    $statusDropdown .= "<option value='{$status}' {$selected}>{$status}</option>";
                }

                $statusDropdown .= "</select></form>";

                        // Render the main order row
                        echo "<tr>
                            <td>{$no}</td>
                            <td>{$row['pesananID']}</td>
                            <td>{$row['userName']}</td>
                            <td>{$statusDropdown}</td>
                            <td>{$row['tanggalPesanan']}</td>
                            <td>
                                <button class='btn btn-link' data-toggle='collapse' data-target='#details-{$no}' aria-expanded='false'>Lihat Detail</button>
                                <div id='details-{$no}' class='collapse'>
                                    <table class='table table-sm'>
                                        <thead>
                                            <tr>
                                                <th>Nama Menu</th>
                                                <th>Jumlah</th>
                                                <th>Total Harga</th>
                                            </tr>
                                        </thead>
                                        <tbody>";

                        // Render order details rows
                        while ($detail = $detailsResult->fetch_assoc()) {
                            echo "<tr>
                                <td>{$detail['namaMenu']}</td>
                                <td>{$detail['jumlah']}</td>
                                <td>{$detail['totalHarga']}</td>
                            </tr>";
                        }

                        echo "          </tbody>
                                    </table>
                                </div>
                            </td>
                            <td class='action-buttons'>
                                <a href='delete_order.php?hapus={$row['pesananID']}' class='btn btn-sm btn-danger'>Delete</a>
                            </td>
                        </tr>";

                        $no++;
                    }
                } else {
                    echo "<tr><td colspan='7'>No orders found</td></tr>";
                }

                  // Count total orders for pagination
                  $countQuery = "SELECT COUNT(*) AS total FROM orders";
                  if (isset($_GET['search']) && !empty($_GET['search'])) {
                      $countQuery .= " WHERE pesananID LIKE '%$search%'";
                  }
                  $countResult = mysqli_query($conn, $countQuery);
                  $totalOrders = $countResult->fetch_assoc()['total'];
                  $totalPages = ceil($totalOrders / $resultsPerPage); // Calculate total pages

                  $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination Controls -->
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php if ($currentPage > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=<?php echo $currentPage - 1; ?>&search=<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">Previous</a></li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <?php if ($currentPage < $totalPages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?php echo $currentPage + 1; ?>&search=<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">Next</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<!-- Bootstrap JS and jQuery -->
<?php include 'footer.php';?>
</body>
</html>
