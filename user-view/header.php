<?php include "../config.php"; ?>
<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header('Location: index2.php'); // Jika belum login, redirect ke halaman login
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FRESHDAY 1.0</title>

  <link rel="icon" type="image/x-icon" href="../img/favicon.ico">
  <!-- Add Swiper's CSS -->
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css"/>

  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->


  <!-- Fonts -->
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,300;0,400;0,700;1,700&display=swap"
    rel="stylesheet">

  <!-- Feather Icons -->
  <!-- <script src="https://unpkg.com/feather-icons"></script> -->
  <script src="https://kit.fontawesome.com/de59933799.js" crossorigin="anonymous"></script>
  <script src="https://unpkg.com/feather-icons@4.10.0/dist/feather.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

  <!-- alpine js -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script src="../js/app.js"></script>

  <!-- My Style -->
  <link rel="stylesheet" href="../css/style.css">

   <style>
    #shopping-cart-btn{
    position: relative;
    }

    #shopping-cart-btn .quantity-badge{
    display: inline-block;
    padding: 1px 5px;
    background-color: red;
    border-radius: 6px;
    font-size: 0.8rem;
    position: absolute;
    top: 0;
    right: -10px;
    }

    .contact .row form .input-group-wide {
  display: flex;
  align-items: center;
  margin-top: 2rem;
  background-color: var(--bg);
  border: 1px solid #eee;
}

.contact .row form .input-group-wide textarea {
  width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 4px;
    font-size: 16px;
    background-color: var(--bg);
    color: white;
}

.fa.fa-pinterest-p, .fa.fa-whatsapp{
      font-size: 28px;
      margin: auto;
    }

    .notification-dropdown {
    color: black;
    position: absolute;
    right: 0;
    background: #fff;
    border: 1px solid #ccc;
    border-radius: 5px;
    width: 300px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    overflow: hidden;
}

.notification-dropdown ul {
    list-style: none;
    margin: 0;
    padding: 0;
}

.notification-dropdown ul li {
    padding: 10px;
    border-bottom: 1px solid #eee;
}

.notification-dropdown ul li:last-child {
    border-bottom: none;
}

.badge {
  display: inline-block;
    padding: 1px 5px;
    background-color: red;
    border-radius: 6px;
    font-size: 0.8rem;
    position: absolute;
    top: 48%;
    right: 132px;
}

</style>
</head>
<body>



  <!-- Navbar start -->
  <nav class="navbar">
    <a href="index.php" class="navbar-logo"><span>FreshDay 1.0.</span></a>

    <div class="navbar-nav">
      <a href="index.php">Home</a>
      <a href="#about">Tentang Kami</a>
      <a href="menu.php">Menu</a>
      <a href="#contact">Kontak</a>
      <div class="dropdown">
        <a href="#"><?php echo $_SESSION['user_name']; ?> ▼</a>
        <div class="dropdown-content">
          <a href="../logout2.php">Logout</a>
          <a href="profile.php">Profil</a>
          <a href="riwayat_pesanan.php">Pesanan</a>
        </div>
      </div>
    </div>

    <div class="navbar-extra">
    <!-- Notification Button -->
    <a href="#" id="notification-button"><i data-feather="bell"></i><span id="notification-badge" class="badge" style="display: none;"></span></a>

<!-- Notification Dropdown -->
<div id="notification-dropdown" class="notification-dropdown" style="display: none;">
    <ul id="notification-list">
        <!-- Notifications will be added here dynamically -->
    </ul>
</div>

    <!-- <a href="cart.php" id="shopping-cart-button">
        <i data-feather="shopping-cart"></i>
        <span class="quantity-badge">0</span>
      </a> -->
      <a href="cart.php" id="shopping-cart-btn">
        <i data-feather="shopping-cart"></i>
        <span class="quantity-badge">0</span>
      </a>
      <a href="#" id="hamburger-menu"><i data-feather="menu"></i></a>
    </div>

    <!-- Search Form start -->
    <div class="search-form">
      <input type="search" id="search-box" placeholder="search here...">
      <label for="search-box"><i data-feather="search"></i></label>
    </div>
    <!-- Search Form end -->

    <!-- Shopping Cart start -->
    <div class="shopping-cart">
  <?php
  if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
      $imagePath = !empty($item['gambar']) ? $item['gambar'] : "../img/default.jpg";
      $itemTotal = $item['harga'] * $item['jumlah'];
      ?>
      <div class="cart-item" data-menuid="<?php echo $item['menuID']; ?>">
        <img src="<?php echo $imagePath; ?>" alt="<?php echo $item['namaMenu']; ?>">
        <div class="item-detail">
          <h3><?php echo $item['namaMenu']; ?></h3>
          <div class="item-price">
            <span>Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></span> &times;
            <!-- Quantity buttons for AJAX updating -->
            <button id="remove" class="remove-item" data-action="decrease">&minus;</button>
            <span class="quantity"><?php echo $item['jumlah']; ?></span>
            <button id="add" class="add-item" data-action="increase">&plus;</button>
            &equals; <span class="item-total">Rp <?php echo number_format($itemTotal, 0, ',', '.'); ?></span>
          </div>
        </div>
      </div>
      <?php
    }
  } else {
    echo "<p>Keranjang Anda kosong.</p>";
  }
  ?>
</div>

    <!-- Shopping Cart end -->

  </nav>