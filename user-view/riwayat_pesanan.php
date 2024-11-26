<?php
// Include database connection
include "header.php";
include '../config.php'; // Replace with your actual database connection script

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit;
}

$userID = $_SESSION['userID'];

// Fetch active orders (orders with status not "Pesanan Selesai")
$queryActiveOrders = "SELECT o.pesananID, o.tanggalPesanan, o.status, 
                             SUM(p.jumlahPembayaran) as totalAmount 
                      FROM orders o
                      JOIN pembayaran p ON o.pesananID = p.pesananID
                      WHERE o.userID = ? AND o.status != 'Pesanan Selesai'
                      GROUP BY o.pesananID, o.tanggalPesanan, o.status
                      ORDER BY o.pesananID DESC";
$stmtActive = $conn->prepare($queryActiveOrders);
$stmtActive->bind_param("s", $userID);
$stmtActive->execute();
$activeOrdersResult = $stmtActive->get_result();

// Fetch completed orders (orders with status "Pesanan Selesai")
$queryOrderHistory = "
    SELECT o.pesananID, o.tanggalPesanan, o.status, 
           SUM(p.jumlahPembayaran) as totalAmount,
           CASE WHEN r.reviewID IS NOT NULL THEN 1 ELSE 0 END AS reviewExists
    FROM orders o
    JOIN pembayaran p ON o.pesananID = p.pesananID
    LEFT JOIN reviews2 r ON o.pesananID = r.pesananID AND r.userID = o.userID
    WHERE o.userID = ? AND o.status = 'Pesanan Selesai'
    GROUP BY o.pesananID, o.tanggalPesanan, o.status
    ORDER BY o.pesananID DESC";

$stmtHistory = $conn->prepare($queryOrderHistory);
$stmtHistory->bind_param("s", $userID);
$stmtHistory->execute();
$orderHistoryResult = $stmtHistory->get_result();
?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reviewRating'], $_POST['reviewComment'], $_POST['pesananID'])) {
    $pesananID = $_POST['pesananID'];
    $rating = $_POST['reviewRating'];
    $comment = $_POST['reviewComment'];

    // Check if a review already exists for this order and user
    $queryCheckReview = "SELECT reviewID FROM reviews2 WHERE pesananID = ? AND userID = ?";
    $stmtCheckReview = $conn->prepare($queryCheckReview);
    $stmtCheckReview->bind_param("ss", $pesananID, $userID);
    $stmtCheckReview->execute();
    $resultCheckReview = $stmtCheckReview->get_result();

    if ($resultCheckReview->num_rows > 0) {
        // Review already exists
        echo "<p style='color:red;'>You have already submitted a review for this order.</p>";
    } else {
        // Generate new reviewID
        $queryLastID = "SELECT reviewID FROM reviews2 ORDER BY reviewID DESC LIMIT 1";
        $resultLastID = $conn->query($queryLastID);
        if ($resultLastID->num_rows > 0) {
            $row = $resultLastID->fetch_assoc();
            $lastID = $row['reviewID'];
            $numericPart = intval(substr($lastID, 3)); // Extract the numeric part
            $newID = 'REV' . str_pad($numericPart + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newID = 'REV0001'; // Start from REV0001 if no entries exist
        }

        // Insert the review into the database
        $queryReview = "INSERT INTO reviews2 (reviewID, pesananID, userID, rating, comment) VALUES (?, ?, ?, ?, ?)";
        $stmtReview = $conn->prepare($queryReview);
        $stmtReview->bind_param("sssis", $newID, $pesananID, $userID, $rating, $comment);
        $stmtReview->execute();
        $stmtReview->close();

        echo "<p style='color:green;'>Review submitted successfully!</p>";
    }

    $stmtCheckReview->close();
}
?>



<style>
/* Your previous styles */
.order-active-title {
    margin-top: 8rem;
    text-align: center;
    font-size: 24px;
    font-weight: bold;
}

.order-history-title {
    text-align: center;
    font-size: 24px;
    font-weight: bold;
}

.order-history-table {
    width: 80%;
    border-collapse: collapse;
    margin: 20px auto;
    margin-bottom: 4rem;
    background-color: black;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.order-history-table th, .order-history-table td {
    font-size:  18px;
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.order-history-table th {
    background-color: #4CAF50;
    color: white;
}

.toggle-details {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 8px 12px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.toggle-details:hover {
    background-color: #45a049;
}

/* Modal styling */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.7);
}

.modal-content {
    background-color: #333;
    margin: 10% auto;
    padding: 20px;
    border-radius: 8px;
    width: 60%;
    color: white;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

.order-details-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: black;
}

.order-details-table th, .order-details-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.order-details-table th {
    background-color: #4CAF50;
    color: white;
}

/* Modal Styling */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    overflow: auto;
}

.modal-content {
    background-color: #333;
    color: white;
    margin: 10% auto;
    padding: 20px;
    border-radius: 8px;
    width: 60%;
}

.close , .close2 {
    display: inline-block;
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    float: right;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: #fff;
    text-decoration: none;
    cursor: pointer;
}


.close2:hover,
.close2:focus {
    color: #fff;
    text-decoration: none;
    cursor: pointer;
}


/* Star Rating Styling */
.star-rating {
    display: flex;
    justify-content: start;
    margin: 10px 0;
}

.star {
    font-size: 30px;
    color: #ddd;
    cursor: pointer;
    transition: color 0.3s;
}

.star:hover,
.star.selected {
    color: #f39c12; /* Golden color when selected */
}

.star.selected {
    color: #f39c12; /* Golden color when selected */
}

textarea {
    width: 100%;
    padding: 10px;
    margin-top: 10px;
    border-radius: 5px;
    resize: vertical;
}

button {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
    margin-top: 10px;
}

button:hover {
    background-color: #45a049;
}

#reviewForm {
    margin-left: 20px;
}

</style>

<h2 class="order-active-title">Active Orders</h2>

<?php if ($activeOrdersResult->num_rows > 0): ?>
    <table class="order-history-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Tanggal Pembelian</th>
                <th>Status</th>
                <th>Total Bayar</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $activeOrdersResult->fetch_assoc()): ?>
                <tr class="order-row">
                    <td><?php echo htmlspecialchars($row['pesananID']); ?></td>
                    <td><?php echo htmlspecialchars($row['tanggalPesanan']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>IDR <?php echo number_format($row['totalAmount'], 0, ',', '.'); ?></td>
                    <td>
                        <button class="toggle-details" data-pesananid="<?php echo htmlspecialchars($row['pesananID']); ?>">View Details</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p style="text-align:center; margin-top:20px; font-size:16px;">No active orders found.</p>
<?php endif; ?>

<h2 class="order-history-title">Order History</h2>

<?php if ($orderHistoryResult->num_rows > 0): ?>
    <table class="order-history-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Tanggal Pembelian</th>
                <th>Status</th>
                <th>Total Bayar</th>
                <th>Details</th>
                <th>Review</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $orderHistoryResult->fetch_assoc()): ?>
    <tr class="order-row">
        <td><?php echo htmlspecialchars($row['pesananID']); ?></td>
        <td><?php echo htmlspecialchars($row['tanggalPesanan']); ?></td>
        <td><?php echo htmlspecialchars($row['status']); ?></td>
        <td>IDR <?php echo number_format($row['totalAmount'], 0, ',', '.'); ?></td>
        <td>
            <button class="toggle-details" data-pesananid="<?php echo htmlspecialchars($row['pesananID']); ?>">View Details</button>
        </td>
        <td>
            <?php if ($row['reviewExists'] == 1): ?>
                <button class="review-button" disabled>Reviewed</button>
            <?php else: ?>
                <button class="review-button" data-pesananid="<?php echo htmlspecialchars($row['pesananID']); ?>">Give Review</button>
            <?php endif; ?>
        </td>
    </tr>
<?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p style="text-align:center; margin-top:20px; font-size:16px;">No orders found.</p>
<?php endif; ?>


<!-- Modal structure for review -->
<div id="reviewModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Submit Your Review</h3>
        <form id="reviewForm" method="POST">
            <input type="hidden" name="pesananID" id="reviewPesananID" />
            
            <label for="reviewRating">Rating:</label>
            <div class="star-rating">
                <span class="star" data-value="1">&#9733;</span>
                <span class="star" data-value="2">&#9733;</span>
                <span class="star" data-value="3">&#9733;</span>
                <span class="star" data-value="4">&#9733;</span>
                <span class="star" data-value="5">&#9733;</span>
            </div>

            <input type="hidden" name="reviewRating" id="reviewRating" required />

            <label for="reviewComment">Comment:</label>
            <textarea id="reviewComment" name="reviewComment" required></textarea>
            <button type="submit">Submit Review</button>
        </form>
    </div>
</div>



<!-- Modal structure -->
<div id="orderDetailsModal" class="modal">
    <div class="modal-content">
        <span class="close2">&times;</span>
        <h3>Order Details</h3>
        <table class="order-details-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Menu Name</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody id="order-details-content">
                <!-- Dynamic content will be inserted here -->
            </tbody>
        </table>
    </div>
</div>

<script>
// JavaScript to toggle modal visibility and load order details via AJAX
document.querySelectorAll('.toggle-details').forEach(button => {
    button.addEventListener('click', function() {
        const pesananID = this.getAttribute('data-pesananid');

        // AJAX request to load order details
        fetch(`fetch_order_details.php?pesananID=${pesananID}`)
            .then(response => response.text())
            .then(data => {
                document.getElementById('order-details-content').innerHTML = data;
                document.getElementById('orderDetailsModal').style.display = 'block';
            });
    });
});

// Close modal when the user clicks on <span> (x)
document.querySelector('.close2').onclick = function() {
    document.getElementById('orderDetailsModal').style.display = 'none';
};

// Close modal when the user clicks anywhere outside of the modal
window.onclick = function(event) {
    const modal = document.getElementById('orderDetailsModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
};
</script>

<script>
document.querySelectorAll('.star').forEach(star => {
    star.addEventListener('click', function() {
        const rating = this.getAttribute('data-value');
        document.getElementById('reviewRating').value = rating; // Store the rating in hidden input
        
        // Remove the 'selected' class from all stars
        document.querySelectorAll('.star').forEach(s => s.classList.remove('selected'));
        
        // Add 'selected' class to the clicked star and all previous stars
        for (let i = 0; i < rating; i++) {
            document.querySelectorAll('.star')[i].classList.add('selected');
        }
    });
});

document.querySelectorAll('.review-button').forEach(button => {
    button.addEventListener('click', function() {
        const pesananID = this.getAttribute('data-pesananid');
        document.getElementById('reviewPesananID').value = pesananID;
        document.getElementById('reviewModal').style.display = 'block';
    });
});

// Close the modal when the user clicks on <span> (x)
document.querySelector('.close').onclick = function() {
    document.getElementById('reviewModal').style.display = 'none';
};

// Close modal if user clicks anywhere outside the modal
window.onclick = function(event) {
    const modal = document.getElementById('reviewModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
};


</script>

<?php
// Close the statement and connection
include "footer.php";
$stmtActive->close();
$stmtHistory->close();
$conn->close();
?>
