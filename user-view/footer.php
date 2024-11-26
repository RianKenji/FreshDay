<!-- Footer start -->
<footer>
    <div class="socials">
      <a href="https://www.instagram.com/freshday1.0?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" target="_blank"><i data-feather="instagram"></i></a>
      <a href="#"><i class="fa fa-whatsapp" aria-hidden="true"></i></a>
    </div>

    <div class="links">
      <a href="#home">Home</a>
      <a href="#about">Tentang Kami</a>
      <a href="#products">Produk</a>
    </div>

    <div class="credit">
      <p>Created by <a href="">FreshDay 1.0</a>. | &copy; 2024.</p>
    </div>
  </footer>
  <!-- Footer end -->

  <!-- Tambahkan script ini di bawah section produk -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
// Mengambil elemen-elemen yang dibutuhkan
const notificationButton = document.getElementById('notification-button');
const notificationBadge = document.getElementById('notification-badge');
const notificationDropdown = document.getElementById('notification-dropdown');
const notificationList = document.getElementById('notification-list');

// Fungsi untuk mengambil notifikasi dari database

function fetchNotifications() {
    fetch('fetch_notifications.php')
        .then(response => response.json())
        .then(data => {
            notificationList.innerHTML = ''; // Hapus notifikasi lama
            const limitedData = data.slice(0, 5); // Batasi jumlah notifikasi yang ditampilkan (misal 5 notifikasi)
            let unreadCount = 0; // Menghitung jumlah notifikasi yang belum dibaca

            if (limitedData.length > 0) {
                limitedData.forEach(notification => {
                    // Cek jika notifikasi sudah dibaca (dari database)
                    if (!notification.isRead) {
                        unreadCount++; // Tambah jumlah notifikasi yang belum dibaca
                    }

                    const li = document.createElement("li");
                    li.innerHTML = ` 
                        <strong>Order ID: ${notification.pesananID}</strong><br>
                        ${notification.detailPesanan}<br>
                        <span style="color: green;">(${notification.status})</span>
                    `;
                    
                    // Menambahkan event listener untuk menandai notifikasi sebagai telah dibaca
                    li.addEventListener('click', function() {
                        markOrderAsRead(notification.pesananID, li); // Mark the order as read
                    });

                    notificationList.appendChild(li);
                });

                // Tampilkan atau sembunyikan badge berdasarkan jumlah notifikasi yang belum dibaca
                if (unreadCount > 0) {
                    notificationBadge.style.display = 'inline'; // Tampilkan badge
                    notificationBadge.textContent = unreadCount; // Set jumlah notifikasi yang belum dibaca
                } else {
                    notificationBadge.style.display = 'none'; // Sembunyikan badge jika tidak ada notifikasi yang belum dibaca
                }
            } else {
                notificationBadge.style.display = 'none'; // Sembunyikan badge jika tidak ada notifikasi
            }

            feather.replace(); // Update Feather icons
        })
        .catch(error => console.error("Error fetching notifications:", error));
}

function checkNotifications() {
    fetch('check_notifications.php') // File PHP untuk mengecek jumlah notifikasi
        .then(response => response.json())
        .then(data => {
            const unreadCount = data.unreadCount;
            const badge = document.getElementById('notification-badge');

            if (unreadCount > 0) {
                badge.style.display = 'inline'; // Tampilkan badge
                badge.textContent = unreadCount; // Tampilkan jumlah
            } else {
                badge.style.display = 'none'; // Sembunyikan badge
            }
        })
        .catch(error => console.error('Error:', error));
}

// Panggil fungsi saat halaman dimuat
document.addEventListener('DOMContentLoaded', checkNotifications);


// Fungsi untuk menandai order sebagai telah dibaca
function markOrderAsRead(pesananID, liElement) {
    fetch('update_order_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `pesananID=${encodeURIComponent(pesananID)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Update tampilan notifikasi
            liElement.style.opacity = 0.5; // Tampilkan bahwa notifikasi sudah dibaca
            updateNotificationBadge(); // Update badge setelah menandai notifikasi sebagai dibaca
        } else {
            console.error('Error:', data.message);
        }
    })
    .catch(error => console.error("Error updating order:", error));
}

// Fungsi untuk menandai semua notifikasi sebagai dibaca ketika lonceng diklik
function markAllNotificationsAsRead() {
    fetch('update_all_notifications_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
    })
    .then(response => response.json())
    .then(data => {
        console.log('Response from server:', data); // Log the response from the server

        if (data.status === 'success') {
            // Update the UI to show that notifications have been read
            notificationList.querySelectorAll('li').forEach(li => {
                li.style.opacity = 0.5; // Update all notifications' opacity to show they're read
            });
            updateNotificationBadge(); // Update badge
        } else {
            console.error('Error:', data.message);
        }
    })
    .catch(error => console.error("Error marking all notifications as read:", error));
}

// Memuat notifikasi pertama kali ketika halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    fetchNotifications(); // Ambil notifikasi dari server saat halaman pertama kali dimuat
});

// Fungsi untuk memperbarui badge notifikasi
function updateNotificationBadge() {
    fetch('fetch_notifications.php')
        .then(response => response.json())
        .then(data => {
            let unreadCount = 0;
            data.forEach(notification => {
                if (!notification.isRead) {
                    unreadCount++;
                }
            });

            if (unreadCount > 0) {
                notificationBadge.style.display = 'inline'; // Tampilkan badge
                notificationBadge.textContent = unreadCount; // Set jumlah notifikasi yang belum dibaca
            } else {
                notificationBadge.style.display = 'none'; // Sembunyikan badge jika tidak ada yang belum dibaca
            }
        })
        .catch(error => console.error("Error fetching notifications:", error));
}

// Ketika ikon notifikasi diklik
notificationButton.addEventListener('click', function(e) {
    e.preventDefault();
    
    // Toggle tampilan dropdown notifikasi
    if (notificationDropdown.style.display === 'none') {
        notificationDropdown.style.display = 'block';
    } else {
        notificationDropdown.style.display = 'none';
    }

    // Menandai semua notifikasi sebagai dibaca saat lonceng diklik
    markAllNotificationsAsRead();

    // Menghilangkan badge setelah dropdown dibuka
    if (notificationBadge.style.display !== 'none') {
        notificationBadge.style.display = 'none'; // Hilangkan badge
    }
});
</script>



  <!-- Feather Icons -->
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    feather.replace();  // Panggil hanya setelah DOM selesai dimuat
  });
</script>


  <!-- My Javascript -->
  <script src="../js/script.js"></script>
<script>
  $(document).ready(function() {
        // Function to update the shopping cart badge on page load
        function updateCartBadge() {
        $.ajax({
            url: 'get_cart_quantity.php', // PHP script to fetch cart quantity
            type: 'GET',
            success: function(response) {
                // $('#shopping-cart-button .quantity-badge').text(response.totalQuantity); // Update quantity badge
                $('#shopping-cart-btn .quantity-badge').text(response.totalQuantity); // Update quantity badge

            },
            error: function() {
                console.error('Failed to fetch cart quantity.');
            }
        });
    }
        // Call the function to update the cart badge on page load
        updateCartBadge();

    $('.add-to-cart').click(function() {
      // Mendapatkan data produk dari elemen form
      var form = $(this).closest('.product-card');
      var menuID = form.data('menuid');
      var namaMenu = form.data('namamenu');
      var harga = form.data('harga');
      var gambar = form.data('gambar');
      var jumlah = form.find('input[name="jumlah"]').val();
      var userID = $('#userID').val(); // Assuming you have a hidden input for userID

      // Mengirimkan data melalui AJAX
      $.ajax({
    url: 'add_to_cart.php', // File PHP untuk memproses data
    type: 'POST',
    data: {
        menuID: menuID,
        namaMenu: namaMenu,
        harga: harga,
        jumlah: jumlah,
        userID: $('#userID').val() // Include userID from the hidden input
    },
    success: function(response) {
        // Ensure the response is parsed as JSON
        alert(response.message); // Access the message property directly
        // $('#shopping-cart-button .quantity-badge').text(response.totalQuantity); // Update quantity badge
        $('#shopping-cart-btn .quantity-badge').text(response.totalQuantity); // Update quantity badge

    },
    error: function() {
        alert('Terjadi kesalahan, coba lagi.');
    }
});
    });
  });
</script>

<script>
$(document).ready(function() {
    // Function to fetch cart data via AJAX
    function updateCart() {
        $.ajax({
            url: 'fetch_cart.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                let cartHTML = '';
                let totalItems = 0;

                if (data.length > 0) {
                    data.forEach(function(item) {
                        totalItems += parseInt(item.kuantitasProduk);
                        cartHTML += `
                            <div class="cart-item" data-menuid="${item.menuID}">
                                <img src="${item.gambar || '../img/default.jpg'}" alt="${item.namaMenu}">
                                <div class="item-detail">
                                    <h3>${item.namaMenu}</h3>
                                    <div class="item-price">
                                        <span>Rp ${parseFloat(item.harga).toLocaleString()}</span> &times;
                                        <button class="remove-item" data-action="decrease">−</button>
                                        <span class="quantity">${item.kuantitasProduk}</span>
                                        <button class="add-item" data-action="increase">+</button>
                                        = <span class="item-total">Rp ${parseFloat(item.totalHarga).toLocaleString()}</span>
                                    </div>
                                </div>
                            </div>`;
                    });
                } else {
                    cartHTML = '<p>Keranjang Anda kosong.</p>';
                }

                $('#shopping-cart').html(cartHTML);
                $('#cart-button').text(`Cart (${totalItems})`);
            },
            error: function() {
                console.error('Failed to fetch cart data');
            }
        });
    }

    // Trigger cart update on button click
    $('#cart-button').on('click', function() {
        updateCart();
        $('#shopping-cart').toggle(); // Toggle the cart display
    });
});
</script>


</body>

</html>