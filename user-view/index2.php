<?php include "../config.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FRESHDAY 1.0</title>

  <link rel="icon" type="image/x-icon" href="../img/favicon.ico">


  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,300;0,400;0,700;1,700&display=swap"
    rel="stylesheet">

  <!-- Feather Icons -->
  <script src="https://unpkg.com/feather-icons"></script>
  <script src="https://kit.fontawesome.com/de59933799.js" crossorigin="anonymous"></script>


  <!-- My Style -->
  <link rel="stylesheet" href="../css/style.css">
</head>
<style>
/* TESTIMONIALS SECTION */


:root {
    --main-color: #ff5e5e;
    --border: 2px solid #444;
}

.review {
    padding: 50px 20px;
    margin: 0 50px;
    text-align: center;
}

.review .heading {
    font-size: 2rem;
    margin-bottom: 30px;
    color: #fff;
}

.review .heading span {
    color: var(--main-color);
}

.review .box-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(30rem, 1fr));
    gap: 1.5rem;
}

.review .box-container .box {
    background-color: #333;
    border: var(--border);
    border-radius: 8px;
    text-align: center;
    padding: 3rem 2rem;
    transition: transform 0.3s ease;
}

.review .box-container .box:hover {
    transform: scale(1.05);
}

.review .box-container .box .quote {
    font-size: 2.5rem; /* Adjust size as needed */
    color: var(--main-color); /* Use your main color or adjust */
}

.review .box-container .box p {
    font-size: 1.5rem;
    line-height: 1.8;
    color: #ccc;
    padding: 2rem 0;
}

.review .box-container .box .user {
    width: 7rem;
    height: 7rem;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 1rem;
}

.review .box-container .box h3 {
    padding: 1rem 0;
    font-size: 2rem;
    color: #fff;
}

.review .box-container .box .stars {
    color: var(--main-color);
}

.review .box-container .box .stars i {
    font-size: 1.5rem;
    margin: 0 2px;
}
  </style>
<body>

  <!-- Navbar start -->
  <nav class="navbar">
    <a href="#" class="navbar-logo"><span>FreshDay 1.0.</span></a>

    <div class="navbar-nav">
      <a href="#home">Home</a>
      <a href="#about">Tentang Kami</a>
      <a href="#products">Menu</a>
      <a href="#contact">Kontak</a>
      <a href="../login.php">Login</a>
    </div>

    <div class="navbar-extra">
      <a href="#" id="search-button"><i data-feather="search"></i></a>
      <a href="#" id="shopping-cart-button"><i data-feather="shopping-cart"></i></a>
      <a href="#" id="hamburger-menu"><i data-feather="menu"></i></a>
    </div>

    <!-- Search Form start -->
    <div class="search-form">
      <input type="search" id="search-box" placeholder="search here...">
      <label for="search-box"><i data-feather="search"></i></label>
    </div>
    <!-- Search Form end -->

    <!-- Shopping Cart start -->
   <!-- Shopping Cart start -->
    <?php if (isset($_SESSION['user_name']) === true): ?>
    <div class="shopping-cart">
    <!-- Isi keranjang belanja -->
    </div>
    <?php else: ?>
    <div class="shopping-cart">
    <div class="cart-item">
    <p>Silakan login terlebih dahulu untuk melihat keranjang belanja.</p>
    </div></div>
    <?php endif; ?>
  <!-- Shopping Cart end -->
    <!-- Shopping Cart end -->

  </nav>
  <!-- Hero Section start -->
  <section class="hero" id="home">
    <div class="mask-container">
      <main class="content">
      <h1>Mari Nikmati Secangkir <span>Kopi</span></h1>
        <p>▫️Fresh ur day with freshday!▫️ #FDyourFavouriteDrink</p>
        <p>Buka Senin-Sabtu 09.00 - 16.00</p>
        <a href="#products" class="cta">Beli Sekarang</a>
      </main>
    </div>
  </section>
  <!-- Hero Section end -->

  <!-- About Section start -->
  <section id="about" class="about">
    <h2><span>Tentang</span> Kami</h2>

    <div class="row">
      <div class="about-img">
        <img src="../img/tentang-kami.jpg" alt="Tentang Kami">
      </div>
      <div class="content">
        <h3>Kenapa memilih kopi kami?</h3>
        <p style="text-align:justify;">Freshday merupakan bisnis yang dimulai dari rasa cinta kita terhadap minuman kopi instan
dalam kemasan botol atau kaleng yang sudah kehilangan esensi dari cita rasa kopi itu
sendiri, dari situlah awal mula kami memulai bisnis ini. Freshday sudah berjalan aktif selama
2 bulan dan untuk kedepannya tentu kami akan lebih menawarkan berbagai varian rasa dan
hal – hal menakjubkan lainnya seperti varian rasa yang belum pernah ada dan minuman
yang limited edition</p>
      </div>
    </div>
  </section>
  <!-- About Section end -->

 <!-- Products Section start -->
<section class="products" id="products">
  <h2><span>Produk Unggulan</span> Kami</h2>
  <p style="text-align:center;">Temukan berbagai varian kopi yang dapat memanjakan selera Anda.</p>
  <div class="row">
    <?php
    // Query untuk mengambil data produk dari database
    $sql = "SELECT * FROM menu";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      // Looping melalui setiap produk
      while ($row = $result->fetch_assoc()) {
        // Cek apakah gambar produk ada
        $imagePath = "../img/" . $row['gambar'];
        if (!file_exists($imagePath)) {
          $imagePath = "../img/default.jpg"; // Gambar default jika tidak ada
        }
        ?>
        <div class="product-card">
          <div class="product-icons">
            <a href="#"><i data-feather="shopping-cart"></i></a>
            <!-- Tombol Detail Produk -->
            <a href="#" 
               class="item-detail-button" 
               data-image="<?php echo $imagePath; ?>"
               data-name="<?php echo $row['namaMenu']; ?>"
               data-description="<?php echo $row['deskripsi']; ?>"
               data-price="<?php echo number_format($row['harga'], 0, ',', '.'); ?>">
               <i data-feather="eye"></i>
            </a>
          </div>
          <div class="product-image">
            <img src="<?php echo $imagePath; ?>" alt="<?php echo $row['namaMenu']; ?>">
          </div>
          <div class="product-content">
            <h3><?php echo $row['namaMenu']; ?></h3>
            <div class="product-price">IDR <?php echo number_format($row['harga'], 0, ',', '.'); ?></div>
          </div>
        </div>
        <?php
      }
    } else {
      echo "<p>Tidak ada produk yang tersedia.</p>";
    }
    ?>
  </div>
</section>
<!-- Products Section end -->
<!-- TESTIMONIALS SECTION -->
<section class="review" id="review">
    <h1 class="heading"><span>Testimo</span>nials</h1>
    <div class="box-container">
        <div class="box">
        <i class="fas fa-quote-left quote"></i>
        <p>
                Kelas Pagi Lebih Semangat Ditemenin FreshDay
            </p>
            <img src="../img/testi/testi1.jpg" alt="" class="user">
            <h3>Verrend</h3>
            <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-half-alt"></i>
            </div>
        </div>
        <div class="box">
        <i class="fas fa-quote-left quote"></i>
            <p>
            Skuyyy yang doyan minum kopi beli disini!!!!
            </p>
            <img src="../img/testi/rangga.jpg" alt="" class="user">
            <h3>Rangga Pranendra</h3>
            <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-half-alt"></i>
            </div>
        </div>
        <div class="box">
        <i class="fas fa-quote-left quote"></i>
            <p>
            Buat Aku yang gasuka kopi, ini ada rasa kesukaan aku yang cocok banget itu matcha latte with honey 😊
            </p>
            <img src="../img/testi/testi2.jpg" alt="" class="user">
            <h3>Jeann</h3>
            <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-half-alt"></i>
            </div>
        </div>
    </div>
</section>

  <!-- Contact Section start -->
  <section id="contact" class="contact">
    <h2><span>Kontak</span> Kami</h2>
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Veritatis, provident.
    </p>

    <div class="row">
      <form action="">
        <div class="input-group">
          <i data-feather="user"></i>
          <input type="text" placeholder="nama">
        </div>
        <div class="input-group">
          <i data-feather="mail"></i>
          <input type="text" placeholder="email">
        </div>
        <div class="input-group">
          <i data-feather="phone"></i>
          <input type="text" placeholder="no hp">
        </div>
        <div class="input-group">
          <i data-feather="keterangan"></i>
          <input type="text" placeholder="keterangan">
        </div>
        <button type="submit" class="btn">kirim pesan</button>
      </form>

    </div>
  </section>
  <!-- Contact Section end -->

 <!-- Footer start -->
 <footer>
    <div class="socials">
      <a href="#"><i data-feather="instagram"></i></a>
      <a href="#"><i data-feather="twitter"></i></a>
      <a href="#"><i data-feather="facebook"></i></a>
    </div>

    <div class="links">
      <a href="#home">Home</a>
      <a href="#about">Tentang Kami</a>
      <a href="#menu">Menu</a>
      <a href="#contact">Kontak</a>
    </div>

    <div class="credit">
      <p>Created by <a href="">FreshDay 1.0</a>. | &copy; 2024.</p>
    </div>
  </footer>
  <!-- Footer end -->

  <!-- Modal Box Item Detail start -->
  <div class="modal" id="item-detail-modal">
    <div class="modal-container">
      <a href="#" class="close-icon"><i data-feather="x"></i></a>
      <div class="modal-content">
        <img src="../img/products/produk1.jpg" alt="Product 1">
        <div class="product-content">
          <h3>Product 1</h3>
          <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Provident, tenetur cupiditate facilis obcaecati
            ullam maiores minima quos perspiciatis similique itaque, esse rerum eius repellendus voluptatibus!</p>
          <div class="product-price">IDR 30K <span>IDR 55K</span></div>
          <a href="#"><i data-feather="shopping-cart"></i> <span>add to cart</span></a>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal Box Item Detail end -->

  <!-- Feather Icons -->
  <script>
    feather.replace()
  </script>

  <!-- My Javascript -->
  <script src="../js/script.js"></script>
</body>

</html>