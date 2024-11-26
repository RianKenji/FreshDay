<?php

@include 'config.php';

// Fungsi untuk menghasilkan userID otomatis dengan format U01, U02, dst.
function generateUserID($conn) {
    // Query untuk mendapatkan userID terakhir
    $query = "SELECT userID FROM users ORDER BY userID DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    $lastID = mysqli_fetch_assoc($result)['userID'];

    // Jika tidak ada ID, mulai dari U01
    if (!$lastID) {
        return 'U01';
    }

    // Ekstrak angka dari ID terakhir dan tambah 1
    $num = (int)substr($lastID, 1);  // Hilangkan 'U' di depan
    $num++;  // Tambah 1

    // Buat ID baru dengan format U01, U02, dst.
    $newID = 'U' . str_pad($num, 2, '0', STR_PAD_LEFT);

    return $newID;
}

if(isset($_POST['submit'])){

   $name = mysqli_real_escape_string($conn, $_POST['username']);
   $fname = mysqli_real_escape_string($conn, $_POST['fname']);
   $lname = mysqli_real_escape_string($conn, $_POST['lname']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = md5($_POST['password']);
   $cpass = md5($_POST['cPassword']);
   $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
   $noHP = mysqli_real_escape_string($conn, $_POST['noHP']);
   $user_type = 'User';

   // Cek apakah email dan password sudah ada di database
   $select = "SELECT * FROM users WHERE email = '$email'";
   $result = mysqli_query($conn, $select);

   if(mysqli_num_rows($result) > 0){
      $error[] = 'User already exists!';
   } else {
      if($pass != $cpass){
         $error[] = 'Password not matched!';
      } else {
         // Panggil fungsi generateUserID untuk mendapatkan ID baru
         $userID = generateUserID($conn);

         // Insert data ke database
         $insert = "INSERT INTO users(userID, username, email, password, userType, namaDepan, namaBelakang, alamat, phone) 
                    VALUES('$userID','$name','$email','$pass','$user_type', '$fname' , '$lname', '$alamat' , '$noHP')";

         mysqli_query($conn, $insert);
         header('location:login.php');
      }
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register Form</title>
   <link rel="icon" type="image/x-icon" href="img/favicon.ico">


   <!-- custom css file link -->
   <link rel="stylesheet" href="css/login/style.css">

</head>
<body>
   
<div class="form-container">

   <form action="" method="post">
      <h3>Register Now</h3>
      <?php
      if(isset($error)){
         foreach($error as $error){
            echo '<span class="error-msg">'.$error.'</span>';
         };
      };
      ?>
      <input type="text" name="username" required placeholder="Enter your Username">
      <input type="text" name="fname" required placeholder="Enter your first name">
      <input type="text" name="lname" required placeholder="Enter your last name">
      <input type="email" name="email" required placeholder="Enter your email">
      <input type="password" name="password" required placeholder="Enter your password">
      <input type="password" name="cPassword" required placeholder="Confirm your password">
      <input type="text" name="alamat" required placeholder="Alamat">
      <input type="text" name="noHP" required placeholder="No HP">
      <input type="submit" name="submit" value="Register Now" class="form-btn">
      <p>Already have an account? <a href="login.php">Login Now</a></p>
   </form>

</div>

<?php 
 mysqli_close($conn);
 ob_end_flush();
?>

</body>
</html>
