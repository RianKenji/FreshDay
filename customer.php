<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer List</title>
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        .action-buttons a {
            margin-right: 10px;
        }
        .add-user-btn {
            background-color: #b49a7d!important;
            color: #fff!important;
        }
        .header-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<?php include "header.php";?>

<div class="container mt-5">
    <h1 class="mb-4">Daftar Pelanggan</h1>

    <!-- Header Controls with "Tambah Pelanggan" and Search Form -->
    <div class="header-controls">
        <!-- Button to Add User -->
        <a href="add_user.php" class="btn add-user-btn">+ Tambah Pelanggan</a>
        <!-- Search Form -->
        <form method="GET" action="" class="form-inline">
            <input type="text" name="search" class="form-control mr-2" placeholder="Cari pelanggan..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
    
    <!-- User Table -->
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th scope="col">No</th>
                <th scope="col">User ID</th>
                <th scope="col">Nama Pelanggan</th>
                <th scope="col">Email</th>
                <th scope="col">Nomor Telepon</th>
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Database connection
            include "config.php";

            // Default query to fetch all users with user_type = 'customer'
            $dataUsers = "SELECT * FROM users WHERE userType = 'User'";

            // Check if search query exists
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search = $conn->real_escape_string($_GET['search']);
                $dataUsers = "SELECT * FROM users WHERE userType = 'User' AND (nama LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%')";
            }

            $result = mysqli_query($conn, $dataUsers);

            if ($result->num_rows > 0) {
                $no = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['userID']}</td>
                        <td>{$row['username']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['phone']}</td>
                        <td class='action-buttons'>
    <button type='button' class='btn btn-sm btn-primary' data-toggle='modal' data-target='#editCustomerModal' data-id='{$row['userID']}' data-username='{$row['username']}' data-email='{$row['email']}' data-phone='{$row['phone']}'>
        Edit </button>                            
        <a href='#' data-id='{$row['userID']}' class='btn btn-sm btn-danger delete-btn'>Delete</a>
                        </td>
                    </tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='7'>No customers found</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS and dependencies (Popper.js and jQuery) -->

<?php include "footer.php";?>

</body>


<!-- Modal for Editing Pelanggan -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" role="dialog" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCustomerModalLabel">Edit Pelanggan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editCustomerForm" method="POST" action="edit_pelanggan.php">
                    <input type="hidden" name="userID" id="editUserID">
                    <div class="form-group">
                        <label for="editUsername">Username Pelanggan</label>
                        <input type="text" class="form-control" id="editUsername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="editEmail">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="editPhone">Nomor Telepon</label>
                        <input type="text" class="form-control" id="editPhone" name="phone" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
        <!--Modal for Confirmation deleting Users -->
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
                    Apakah Anda yakin ingin menghapus Pelanggan ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <a href="#" class="btn btn-danger" id="confirmDeleteBtn">Hapus</a>
                </div>
            </div>
        </div>
    </div>

    <script>
    $('#editCustomerModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var userID = button.data('id'); // Extract info from data-* attributes
        var username = button.data('username');
        var email = button.data('email');
        var phone = button.data('phone');

        // Update the modal's content
        var modal = $(this);
        modal.find('#editUserID').val(userID);
        modal.find('#editUsername').val(username);
        modal.find('#editEmail').val(email);
        modal.find('#editPhone').val(phone);
    });
</script>

<script>
    //delete customer
  $(document).ready(function() {
    $('.delete-btn').click(function(event) {
      event.preventDefault();
      const userID = $(this).data('id');
      $('#confirmDeleteBtn').attr('href', 'delete_user.php?hapus=' + userID);
      $('#confirmDeleteModal').modal('show');
    });
  });
</script>

</html>
