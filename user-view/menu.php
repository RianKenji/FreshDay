<html>
  <head>
      <style>

</style>


  </head>
<?php include "header.php";?>
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
</section>
<!-- Products Section end -->

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


<!-- Products Section end -->
 <?php include 'footer.php';?>
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



</html>