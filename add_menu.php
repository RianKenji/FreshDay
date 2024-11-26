<!DOCTYPE html>
<html lang="en">
  <body>
<?php 
    ob_start(); 
    require "header.php";
    require "config.php";

    if (isset($_POST['submit'])) {
        if (empty($_POST['namaMenu']) || empty($_POST['harga']) || empty($_POST['deskripsi']) || empty($_POST['stok'])) {
            echo "<script> alert('One or more fields are empty !!')</script>";
        } else {
            $namaMenu = $_POST['namaMenu'];
            $harga = $_POST['harga'];

            // Generate menuID
            $query = "SELECT menuID FROM menu ORDER BY menuID DESC LIMIT 1";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                // Extract numeric part from menuID
                $lastId = intval(substr($row['menuID'], 1)); // Remove "M" and convert to int
                if ($lastId > 9) {
                  $newId = 'M' . ($lastId + 1); // Tidak melakukan padding jika lebih besar dari 9
              } else {
                  $newId = 'M' . str_pad($lastId + 1, 2, '0', STR_PAD_LEFT); // Padding nol jika kurang dari 9
              }
                          
            } else {
                $newId = 'M01'; // If no records, start from M01
            }

            // Check if 'image' key exists and there is no file upload error
            if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                $image = $_FILES['image']['name'];
                $img_directory = "img/" . basename($image);

                // Check if the file was successfully uploaded before proceeding
                if (move_uploaded_file($_FILES['image']['tmp_name'], $img_directory)) {
                    $deskripsi = $_POST['deskripsi'];
                    $stok = $_POST['stok'];

                    // Use prepared statement
                    $stmt = $conn->prepare("INSERT INTO menu (menuID, namaMenu, gambar, deskripsi, harga, stok) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssii", $newId, $namaMenu, $image, $deskripsi, $harga, $stok);
                    $stmt->execute();
                    header("location: menu.php");
                } else {
                    echo "<script> alert('File upload failed!')</script>";
                }
            } else {
                echo "<script> alert('Image not provided or file upload error occurred!')</script>";
            }
        }
    }
?>
    <div class="container-fluid">
       <div class="row">
        <div class="col">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title mb-5 d-inline">Buat Menu</h5>
          <form method="POST" action="add_menu.php" enctype="multipart/form-data">
                <!-- Email input -->
                <div class="form-outline mb-4 mt-4">
                  <input type="text" name="namaMenu" id="form2Example1" class="form-control" placeholder="Product Title" />
                 
                </div>
                <div class="form-outline mb-4 mt-4">
                  <input type="text" name="harga" id="form2Example1" class="form-control" placeholder="harga" />
                 
                </div>
                <div class="form-outline mb-4 mt-4">
                  <input type="file" name="image" id="form2Example1" class="form-control"  />
                 
                </div>
                <div class="form-group">
                  <label for="exampleFormControlTextarea1">Description</label>
                  <textarea name="deskripsi" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                </div>
               
                <div class="form-outline mb-4 mt-4">
                  <input type="text" name="stok" id="form2Example1" class="form-control"  placeholder="Stok"/>
                 
                </div>

                <br>
            
      
                <!-- Submit button -->
                <button type="submit" name="submit" class="btn btn-primary  mb-4 text-center">create</button>

          
              </form>

            </div>
          </div>
        </div>
      </div>
  </div>
<?php require "footer.php"?>
<script type="text/javascript">
 <?php 
 mysqli_close($connection);
 ob_end_flush();
?>
</script>
</body>
</html>