<head>
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
</head>
<?php include 'header.php'?>
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
    // Query untuk mendapatkan produk dengan promo
    $sql = "
      SELECT 
        m.menuID, 
        m.namaMenu, 
        m.harga AS originalPrice, 
        m.gambar, 
        m.deskripsi, 
        IF(p.hargaPromo IS NOT NULL, p.hargaPromo, NULL) AS promoPrice 
      FROM menu m
      LEFT JOIN promo_items pi ON m.menuID = pi.menuID
      LEFT JOIN promos p ON pi.promoID = p.promoID AND CURRENT_DATE BETWEEN p.tanggalMulai AND p.tanggalSelesai
      LIMIT 6
    ";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        // Validasi gambar
        $imagePath = "../img/" . ($row['gambar'] ?: "default.jpg");
        if (!file_exists($imagePath)) {
          $imagePath = "../img/default.jpg";
        }

        // Hitung harga diskon (harga original dikurangi harga promo)
        $discountedPrice = $row['promoPrice'] !== null 
          ? max(0, $row['originalPrice'] - $row['promoPrice']) // Pastikan tidak negatif
          : $row['originalPrice'];
        ?>
        <!-- Form untuk menampilkan produk -->
        <form class="product-card" 
              data-menuid="<?php echo $row['menuID']; ?>" 
              data-namamenu="<?php echo $row['namaMenu']; ?>" 
              data-harga="<?php echo $discountedPrice; ?>" 
              data-gambar="<?php echo $imagePath; ?>" 
              data-deskripsi="<?php echo htmlspecialchars($row['deskripsi'], ENT_QUOTES, 'UTF-8'); ?>">
          <div class="product-icons">
            <!-- Tombol tambah ke keranjang -->
            <button type="button" class="add-to-cart"><i data-feather="shopping-cart"></i></button>
            <!-- Tombol detail produk -->
            <a href="#" class="item-detail-button"><i data-feather="eye"></i></a>
          </div>
          <div class="product-image">
            <img src="<?php echo $imagePath; ?>" alt="<?php echo $row['namaMenu']; ?>">
          </div>
          <div class="product-content">
            <h3><?php echo $row['namaMenu']; ?></h3>
            <div class="product-price">
              <!-- Tampilkan harga asli jika ada promo -->
              <?php if ($row['promoPrice'] !== null) { ?>
                <span class="original-price" style="text-decoration: line-through; margin-right: 10px;">
                  IDR <?php echo number_format($row['originalPrice'], 0, ',', '.'); ?>
                </span>
              <?php } ?>
              <!-- Tampilkan harga diskon -->
              IDR <?php echo number_format($discountedPrice, 0, ',', '.'); ?>
            </div>
          </div>
          <input type="hidden" name="jumlah" value="1">
        </form>
        <?php
      }
    } else {
      echo "<p>Tidak ada produk yang tersedia.</p>";
    }
    ?>
  </div>
  <!-- Tombol "See More" -->
  <div class="see-more-container" style="text-align:center; margin-top: 20px;">
    <a href="menu.php" class="see-more-button" style="text-decoration:none; background-color:#333; color:#fff; padding:10px 20px; border-radius:5px; display:inline-block;">See More</a>
  </div>
</section>
<!-- Products Section end -->


<!-- TESTIMONIALS SECTION -->
<section class="review" id="review">
    <h1 class="heading"><span>Testimo</span>ni</h1>
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
    <form action="process_contact.php" method="POST">
    <div class="input-group">
          <i data-feather="user"></i>
          <input type="text" name="nama" placeholder="nama">
        </div>
        <div class="input-group">
          <i data-feather="mail"></i>
          <input type="text" name="email" placeholder="email">
        </div>
        <div class="input-group">
          <i data-feather="phone"></i>
          <input type="text" name="noHP" placeholder="no hp">
        </div>
        <div class="input-group-wide">
        <textarea id="testimonial" name="testimonial" rows="5" placeholder="Reviews" required></textarea>
      </div>

        <button type="submit" class="btn">kirim pesan</button>
      </form>

    </div>
  </section>
  <!-- Contact Section end -->

  <!-- Modal Box Item Detail start -->
<div class="modal" id="item-detail-modal">
  <div class="modal-container">
    <a href="#" class="close-icon"><i data-feather="x"></i></a>
    <div class="modal-content">
      <img id="modal-image" src="" alt="Product Image">
      <div class="product-content">
        <h3 id="modal-title"></h3>
        <p id="modal-description"></p>
        <div class="product-price" id="modal-price"></div>
        <a href="#" id="modal-add-to-cart"><i data-feather="shopping-cart"></i> <span>add to cart</span></a>
      </div>
    </div>
  </div>
</div>
<!-- Modal Box Item Detail end -->
<?php include 'footer.php'?>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("item-detail-modal");
  const modalImage = document.getElementById("modal-image");
  const modalTitle = document.getElementById("modal-title");
  const modalDescription = document.getElementById("modal-description");
  const modalPrice = document.getElementById("modal-price");
  const modalAddToCart = document.getElementById("modal-add-to-cart");
  const closeModal = document.querySelector(".close-icon");

  document.querySelectorAll(".item-detail-button").forEach(button => {
    button.addEventListener("click", function (event) {
      event.preventDefault();

      // Ambil data dari elemen produk
      const productCard = this.closest(".product-card");
      const menuName = productCard.getAttribute("data-namamenu");
      const menuPrice = productCard.getAttribute("data-harga");
      const menuImage = productCard.getAttribute("data-gambar");
      const menuDescription = productCard.getAttribute("data-deskripsi");

      // Isi modal dengan data produk
      modalImage.src = menuImage;
      modalImage.alt = menuName;
      modalTitle.textContent = menuName;
      modalDescription.textContent = menuDescription; // Masukkan deskripsi ke modal
      modalPrice.innerHTML = "IDR " + new Intl.NumberFormat("id-ID").format(menuPrice);
      modalAddToCart.setAttribute("href", "#");

      // Tampilkan modal
      modal.style.display = "flex"; // Gunakan flex agar tetap di tengah
    });
  });

  // Tutup modal ketika klik tombol close
  closeModal.addEventListener("click", function (event) {
    event.preventDefault();
    modal.style.display = "none";
  });

  // Tutup modal ketika klik di luar container modal
  window.addEventListener("click", function (event) {
    if (event.target === modal) {
      modal.style.display = "none";
    }
  });
});
</script>