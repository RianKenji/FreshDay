<?php
include '../config.php'; // Database connection file
include 'header.php';

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    echo "Please log in to view your cart.";
    exit;
}

$userID = $_SESSION['userID'];

// Fetch the cart items for the current user
$sql = "
    SELECT k.keranjangID, k.kuantitasProduk, k.totalHarga, m.namaMenu, k.harga, m.gambar
    FROM keranjang k
    JOIN menu m ON k.menuID = m.menuID
    WHERE k.userID = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userID);
$stmt->execute();
$result = $stmt->get_result();

// Calculate the total price of all items in the cart
$totalCartPrice = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Optional: Add your CSS file for styling -->
    <link rel="stylesheet" href="../css/cart.css">
</head>

<style>
    /* Style dasar untuk gambar */
    .item-image {
    width: 80px; /* Ukuran default gambar */
    cursor: pointer;
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out, opacity 0.3s ease-in-out;
}

.item-image:hover {
    transform: scale(1.1); /* Zoom gambar */
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Tambahkan bayangan */
    opacity: 0.9; /* Sedikit transparan */
    border: 2px solid #4CAF50; /* Tambahkan border hijau */
}

/* Modal zoom */
#imageModal {
    display: none; /* Modal tersembunyi secara default */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8); /* Latar belakang gelap */
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

#imageModal img {
    max-width: 90%;
    max-height: 90%;
    border: 2px solid white;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

#imageModal span {
    position: absolute;
    top: 10px;
    right: 20px;
    font-size: 30px;
    color: white;
    cursor: pointer;
}


/* Title */
.delivery-title {
    font-size: 20px;
    margin-bottom: 15px;
    font-weight: bold;
    color: white;
}

/* Radio button container */
.delivery-options {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    justify-content: flex-end;
}

input[type="radio"] {
    appearance: none;
    -webkit-appearance: none; /* Untuk kompatibilitas browser lama */
    -moz-appearance: none;
}


.radio-container {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}

/* Custom radio button */
.custom-radio {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid #4CAF50;
    border-radius: 50%;
    position: relative;
    transition: all 0.3s ease;
}

.custom-radio::after {
    content: '';
    width: 10px;
    height: 10px;
    background: #4CAF50;
    border-radius: 50%;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0);
    transition: transform 0.2s ease-in-out;
}

input[type="radio"]:checked + .custom-radio::after {
    transform: translate(-50%, -50%) scale(1);
}

/* Label text styling */
.label-text {
    font-size: 16px;
    color: white;
    font-weight: bold;
}

/* Address input styling */
#address-container {
    margin-top: 10px;
}

.address-label {
    font-size: 16px;
    color: white;
    font-weight: bold;
    margin-bottom: 5px;
    display: block;
    text-align: right!important;
}

.input-address {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    margin-top: 5px;
    transition: all 0.3s ease;
}

.input-address:focus {
    border-color: #4CAF50;
    box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
    outline: none;
}

.checkout-button:hover {
    background: #45a049;
}

</style>

<body>
<div class="container-cart">
<h2 class="cart-title">Keranjang</h2>

<?php if ($result->num_rows > 0): ?>
    <table class="cart-table">
        <thead>
            <tr class="cart-header">
                <th class="cart-header-item">Nama Menu</th>
                <th class="cart-header-item">Gambar</th>
                <th class="cart-header-item">Harga</th>
                <th class="cart-header-item">Kuantitas</th>
                <th class="cart-header-item">Total</th>
                <th class="cart-header-item">Edit</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php 
                $itemTotalPrice = $row['harga'] * $row['kuantitasProduk']; 
                $totalCartPrice += $itemTotalPrice;
                ?>
                <tr class="cart-item">
                    <td class="cart-item-name"><?php echo htmlspecialchars($row['namaMenu']); ?></td>
                    <td class="cart-item-image">
                    <img src="../img/<?php echo htmlspecialchars($row['gambar']); ?>" 
     alt="<?php echo htmlspecialchars($row['namaMenu']); ?>" 
     class="item-image zoomable">
                    </td>
                    <td class="cart-item-price">IDR <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                    <td class="cart-item-quantity"><?php echo $row['kuantitasProduk']; ?></td>
                    <td class="cart-item-total">IDR <?php echo number_format($itemTotalPrice, 0, ',', '.'); ?></td>
                    <td class="cart-item-actions">
                        <form action="update_cart.php" method="post" class="update-form">
                            <input type="hidden" name="keranjangID" value="<?php echo $row['keranjangID']; ?>">
                            <input type="number" name="new_quantity" value="<?php echo $row['kuantitasProduk']; ?>" min="1" class="quantity-input">
                            <button type="submit" class="update-button">Update</button>
                        </form>
                        <form action="remove_from_cart.php" method="post" class="remove-form">
                            <input type="hidden" name="keranjangID" value="<?php echo $row['keranjangID']; ?>">
                            <button type="submit" class="remove-button">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h3 class="cart-total">Total: RP <?php echo number_format($totalCartPrice, 0, ',', '.'); ?></h3>
    <form action="checkout.php" method="post" class="checkout-container">
    <div class="form-group">
        <h3 class="delivery-title">Pilih Metode Pengiriman</h3>
        <div class="delivery-options">
            <label class="radio-container">
                <input type="radio" id="delivery" name="method" value="delivery" required>
                <span class="custom-radio"></span>
                <span class="label-text">Delivery</span>
            </label>
            <label class="radio-container">
                <input type="radio" id="pickup" name="method" value="pickup">
                <span class="custom-radio"></span>
                <span class="label-text">Pickup</span>
            </label>
        </div>
    </div>
    <div class="form-group" id="address-container" style="display: none;">
        <label for="address" class="address-label">Alamat Pengiriman:</label>
        <input type="text" name="address" id="address" placeholder="Alamat, Kota, Indonesia" class="input-address">
    </div>
    <button type="submit" class="checkout-button">Checkout</button>
</form>


</form>

<?php else: ?>
    <p class="empty-cart-message" style="margin-bottom: 230px;">Keranjangmu Kosong.</p>
<?php endif; ?>
</div>

<div id="confirmModal" class="modal">
    <div class="modal-content">
        <p>Apakah Anda yakin ingin menghapus item ini dari keranjang?</p>
        <div class="modal-actions">
            <button id="confirmYes" class="confirm-button">Ya</button>
            <button id="confirmNo" class="cancel-button">Tidak</button>
        </div>
    </div>
</div>

<div id="imageModal">
    <span>&times;</span>
    <img src="" alt="Zoomed Image">
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const deliveryOption = document.getElementById('delivery');
        const pickupOption = document.getElementById('pickup');
        const addressContainer = document.getElementById('address-container');

        // Tampilkan atau sembunyikan input alamat berdasarkan pilihan
        deliveryOption.addEventListener('change', () => {
            if (deliveryOption.checked) {
                addressContainer.style.display = 'block';
                document.getElementById('address').setAttribute('required', true);
            }
        });

        pickupOption.addEventListener('change', () => {
            if (pickupOption.checked) {
                addressContainer.style.display = 'none';
                document.getElementById('address').removeAttribute('required');
            }
        });
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('confirmModal');
    const confirmYes = document.getElementById('confirmYes');
    const confirmNo = document.getElementById('confirmNo');
    let formToDelete = null;

    // Attach event listeners to "Remove" buttons
    document.querySelectorAll('.remove-form').forEach((form) => {
        form.addEventListener('submit', (e) => {
            e.preventDefault(); // Prevent form submission
            formToDelete = form; // Store the form to be deleted
            modal.style.display = 'block'; // Show modal
        });
    });

    // Handle "Yes" button
    confirmYes.addEventListener('click', () => {
        if (formToDelete) {
            formToDelete.submit(); // Submit the stored form
        }
        modal.style.display = 'none'; // Hide modal
    });

    // Handle "No" button
    confirmNo.addEventListener('click', () => {
        modal.style.display = 'none'; // Hide modal
    });
});

</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Modal dan elemen gambar
        const modal = document.getElementById('imageModal');
        const modalImage = modal.querySelector('img');
        const closeBtn = modal.querySelector('span');

        // Tambahkan event listener pada gambar
        document.querySelectorAll('.zoomable').forEach(image => {
            image.addEventListener('click', () => {
                modal.style.display = 'flex';
                modalImage.src = image.src;
            });
        });

        // Tutup modal saat klik tombol close
        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        // Tutup modal saat klik di luar gambar
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
</script>



<?php
include "footer.php";
$stmt->close();
$conn->close();
?>

</body>
</html>
