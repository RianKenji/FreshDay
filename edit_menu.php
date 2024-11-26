<!DOCTYPE html>
<html lang="en">
<?php
ob_start();
// Include database connection
include 'header.php';
include 'config.php';

// Check if an ID was passed to the edit page
if (isset($_GET['edit'])) {
    $menuID = $_GET['edit'];

    // Retrieve menu data for the selected menu ID
    $sql = "SELECT * FROM menu WHERE menuID = '$menuID'";
    $result = mysqli_query($conn, $sql);

    // Check if the menu exists
    if ($result->num_rows > 0) {
        $row = mysqli_fetch_assoc($result);
        $namaMenu = $row['namaMenu'];
        $harga = $row['harga'];
        $deskripsi = $row['deskripsi'];
        $gambar = $row['gambar'];
    } else {
        echo "Menu not found!";
        exit;
    }
}

// Update menu information when the form is submitted
if (isset($_POST['update'])) {
    $namaMenu = $_POST['namaMenu'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];

    // Handle image upload
    if ($_FILES['gambar']['name']) {
        $targetDir = "img/";
        $gambar = basename($_FILES['gambar']['name']);
        $targetFilePath = $targetDir . $gambar;
        move_uploaded_file($_FILES['gambar']['tmp_name'], $targetFilePath);
    }

    // Update query
    $updateQuery = "UPDATE menu SET namaMenu='$namaMenu', harga='$harga', deskripsi='$deskripsi', gambar='$gambar' WHERE menuID='$menuID'";

    if (mysqli_query($conn, $updateQuery)) {
        header("Location: menu.php"); // Redirect to menu list after update
        exit;
    } else {
        echo "Error updating menu: " . mysqli_error($conn);
    }
}

?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Edit Menu</h1>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="namaMenu">Nama Menu</label>
            <input type="text" name="namaMenu" class="form-control" value="<?php echo $namaMenu; ?>" required>
        </div>

        <div class="form-group">
            <label for="harga">Harga</label>
            <input type="number" name="harga" class="form-control" value="<?php echo $harga; ?>" required>
        </div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="4" required><?php echo $deskripsi; ?></textarea>
        </div>

        <div class="form-group">
            <label for="gambar">Upload Foto Menu</label><br>
            <img src="img/<?php echo $gambar; ?>" alt="Menu Image" class="img-thumbnail" width="150px">
            <input type="file" name="gambar" class="form-control-file mt-2">
        </div>

        <button type="submit" name="update" class="btn btn-success">Update Menu</button>
        <a href="menu.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php ob_end_flush(); // End output buffering?>
<!-- Bootstrap JS and dependencies (Popper.js and jQuery) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<?php include 'footer.php';?>
</body>
</html>
