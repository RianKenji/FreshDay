<!DOCTYPE html>
<html lang="en">
  <body>
<?php 
    ob_start(); 
    require "header.php";
    require "config.php";

    if (isset($_POST['submit'])) {
        if (
            empty($_POST['username']) || 
            empty($_POST['password']) || 
            empty($_POST['email']) || 
            empty($_POST['alamat']) || 
            empty($_POST['phone']) || 
            empty($_POST['namaDepan']) || 
            empty($_POST['namaBelakang'])
        ) {
            echo "<script> alert('One or more fields are empty !!')</script>";
        } else {
            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encrypt the password
            $email = $_POST['email'];
            $alamat = $_POST['alamat'];
            $phone = $_POST['phone'];
            $namaDepan = $_POST['namaDepan'];
            $namaBelakang = $_POST['namaBelakang'];
            $userType = 'admin';

            // Generate userID
            $query = "SELECT userID FROM users ORDER BY userID DESC LIMIT 1";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                // Extract numeric part from userID
                $lastId = intval(substr($row['userID'], 3)); // Remove "USR" and convert to int
                $newId = 'U' . str_pad($lastId + 1, 2, '0', STR_PAD_LEFT); // Padding dengan nol
            } else {
                $newId = 'U001'; // Start from U001 if no records exist
            }

            // Insert data into the table
            $stmt = $conn->prepare("INSERT INTO users (username, password, userType, email, userID, alamat, phone, namaDepan, namaBelakang) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssss", $username, $password, $userType, $email, $newId, $alamat, $phone, $namaDepan, $namaBelakang);
            if ($stmt->execute()) {
                echo "<script> alert('Admin added successfully!')</script>";
                header("location: admin.php");
            } else {
                echo "<script> alert('Error adding admin!')</script>";
            }
        }
    }
?>
    <div class="container-fluid">
       <div class="row">
        <div class="col">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title mb-5 d-inline">Add Admin</h5>
              <form method="POST" action="add_admin.php">
                <div class="form-outline mb-4 mt-4">
                  <input type="text" name="username" class="form-control" placeholder="Username" />
                </div>
                <div class="form-outline mb-4 mt-4">
                  <input type="password" name="password" class="form-control" placeholder="Password" />
                </div>
                <div class="form-outline mb-4 mt-4">
                  <input type="email" name="email" class="form-control" placeholder="Email" />
                </div>
                <div class="form-outline mb-4 mt-4">
                  <input type="text" name="alamat" class="form-control" placeholder="Alamat" />
                </div>
                <div class="form-outline mb-4 mt-4">
                  <input type="text" name="phone" class="form-control" placeholder="Phone" />
                </div>
                <div class="form-outline mb-4 mt-4">
                  <input type="text" name="namaDepan" class="form-control" placeholder="Nama Depan" />
                </div>
                <div class="form-outline mb-4 mt-4">
                  <input type="text" name="namaBelakang" class="form-control" placeholder="Nama Belakang" />
                </div>
                <button type="submit" name="submit" class="btn btn-primary mb-4 text-center">Add Admin</button>
              </form>
            </div>
          </div>
        </div>
      </div>
  </div>
<?php require "footer.php"?>
<script type="text/javascript">
 <?php 
 mysqli_close($conn);
 ob_end_flush();
?>
</script>
</body>
</html>
