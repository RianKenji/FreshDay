<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu List</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    
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
        .menu-image{
            width: 80%;
            height: 100px;
        }
        
    </style>
</head>
<body>
<?php include "header.php";?>

<div class="container-fluid"> 
    <div class="card shadow mb-4">
        <!-- Konten lainnya di sini -->


<div class="container mt-5">
    <h1 class="mb-4">Daftar Menu</h1>

    <!-- Header Controls with "Tambah Menu" and Search Form -->
    <div class="header-controls">
        <!-- Button to Add Menu -->
        <a href="add_menu.php" class="btn add-menu-btn">+ Tambah Menu</a>
        <!-- Search Form -->
        <form method="GET" action="" class="form-inline">
            <input type="text" name="search" class="form-control mr-2" placeholder="Cari menu..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
    
    <!-- Menu Table -->
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th scope="col">No</th>
                <th scope="col">Menu ID</th>
                <th scope="col">Foto Menu</th>
                <th scope="col">Nama Menu</th>
                <th scope="col">Harga</th>
                <th scope="col">Keterangan</th>
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Database connection
            include "config.php";

            // Default query to fetch all data
            $dataMenu = "SELECT * FROM menu";

            // Check if search query exists
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search = $conn->real_escape_string($_GET['search']);
                $dataMenu = "SELECT * FROM menu WHERE namaMenu LIKE '%$search%' OR deskripsi LIKE '%$search%' OR harga LIKE '%$search%'";
            }

            $result = mysqli_query($conn, $dataMenu);

            if ($result->num_rows > 0) {
                $no = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['menuID']}</td>
                        <td><img src='img/{$row['gambar']}' alt='Menu Image' class='menu-image'></td>
                        <td>{$row['namaMenu']}</td>
                        <td>Rp " . number_format($row['harga'], 2, ',', '.') . "</td>
                        <td>{$row['deskripsi']}</td>
                        <td class='action-buttons'>
                            <a href='edit_menu.php?edit={$row['menuID']}' class='btn btn-sm btn-primary'>Edit</a>
                            <a href='#' data-id='{$row['menuID']}' class='btn btn-sm btn-danger delete-btn'>Delete</a>
                        </td>
                    </tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='7'>No menu found</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus menu ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <a href="#" class="btn btn-danger" id="confirmDeleteBtn">Hapus</a>
                </div>
            </div>
        </div>
    </div>

</div>

</div>
</div> <!--penutup container fluid-->

<!-- Modal untuk Notifikasi -->
<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notifikasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php
                // Menampilkan notifikasi jika ada
                if (isset($_SESSION['notification'])) {
                    // Mengambil jenis dan pesan dari session
                    $type = $_SESSION['notification']['type'];
                    $message = $_SESSION['notification']['message'];

                    // Menampilkan pesan
                    echo '<div class="alert alert-' . $type . '">' . $message . '</div>';

                    unset($_SESSION['notification']); // Hapus pesan setelah ditampilkan
                }
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies (Popper.js and jQuery) -->

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> -->

<script>
  $(document).ready(function() {
    $('.delete-btn').click(function(event) {
      event.preventDefault();
      const menuID = $(this).data('id');
      $('#confirmDeleteBtn').attr('href', 'delete_menu.php?hapus=' + menuID);
      $('#confirmDeleteModal').modal('show');
    });
  });
</script>

<script>
$(document).ready(function() {
    <?php if (isset($_SESSION['notification'])): ?>
        $('#notificationModal').modal('show'); // Tampilkan modal jika ada notifikasi
    <?php endif; ?>
});
</script>

<?php include "footer.php";?>

</body>
</html>
