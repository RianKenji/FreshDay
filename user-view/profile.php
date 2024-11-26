<?php
session_start();
include '../config.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

// Ambil userID dari sesi
$userID = $_SESSION['userID'];

// Query untuk mendapatkan data pengguna
$sql = "SELECT username, email, userType, namaDepan, namaBelakang, phone, alamat, gambar FROM users WHERE userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha1/dist/css/bootstrap.min.css">
</head>
<style>
    body {
        background: rgb(99, 39, 120);
    }
    .form-control:focus {
        box-shadow: none;
        border-color: #BA68C8;
    }
    .profile-button {
        background: rgb(99, 39, 120);
        box-shadow: none;
        border: none;
    }
    .profile-button:hover {
        background: #682773;
    }
    .back-button {
        background: #BA68C8;
        color: #fff;
        border: none;
    }
</style>
<body>

<div class="container rounded bg-white mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center">
    <a href="index.php" class="btn back-button m-3">Back</a>
    </div>
    <form action="update_profile.php" method="POST" enctype="multipart/form-data">
        <div class="row">
            <!-- Kolom Kiri -->
            <div class="col-md-4 border-right">
                <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                    <img class="rounded-circle mt-5" width="150px" id="profilePic" src="<?php echo htmlspecialchars($user['gambar'] ?? 'https://st3.depositphotos.com/15648834/17930/v/600/depositphotos_179308454-stock-illustration-unknown-person-silhouette-glasses-profile.jpg'); ?>" alt="Profile Picture">
                    <label class="btn btn-sm btn-primary mt-3" for="profilePicture">
                        Change Photo
                    </label>
                    <input type="file" id="profilePicture" name="profilePicture" accept="image/*" style="display: none;">
                    <span class="font-weight-bold"><?php echo htmlspecialchars($user['namaDepan'] . ' ' . $user['namaBelakang']); ?></span>
                    <span class="text-black-50"><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
            </div>
            
            <!-- Kolom Kanan -->
            <div class="col-md-8">
                <div class="p-3 py-5">
                    <h4 class="text-left">Profile Settings</h4>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="labels">First Name</label>
                            <input type="text" class="form-control" name="namaDepan" value="<?php echo htmlspecialchars($user['namaDepan']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="labels">Last Name</label>
                            <input type="text" class="form-control" name="namaBelakang" value="<?php echo htmlspecialchars($user['namaBelakang']); ?>">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="labels">Mobile Number</label>
                            <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="labels">Address</label>
                            <input type="text" class="form-control" name="alamat" value="<?php echo htmlspecialchars($user['alamat']); ?>">
                        </div>
                    </div>
                    <div class="mt-5 text-center">
                        <button class="btn btn-primary profile-button" type="submit">Save Profile</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // When the file input changes (image selection), update the displayed image
    document.getElementById('profilePicture').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profilePic').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>

</body>
</html>
