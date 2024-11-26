<?php

@include 'config.php';

session_start();

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5($_POST['password']);  // Menggunakan md5 seperti yang sekarang

    // Cek apakah user ada berdasarkan email dan password
    $select = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);

        if ($row['userType'] == 'Admin') {
            $_SESSION['admin_name'] = $row['username'];
            header('location:index.php');
            exit();
        } elseif ($row['userType'] == 'User') {
            $_SESSION['user_name'] = $row['username'];
            $_SESSION['userID'] = $row['userID'];
            header('location:user-view/index.php');
            exit();
        }
    } else {
        $error[] = 'Email atau password salah!';
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login Form</title>
   <link rel="icon" type="image/x-icon" href="img/favicon.ico">


   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/login/style.css">

</head>
<body>
   
<div class="form-container">

   <form action="" method="post">
      <h3>Login Sekarang</h3>
      <?php
      if (isset($error)) {
         foreach ($error as $msg) {
            echo '<span class="error-msg">' . $msg . '</span>';
         }
      }
      ?>
      <input type="email" name="email" required placeholder="Masukkan email Anda">
      <div class="password-container">
            <input type="password" name="password" id="password" class="password-input" required placeholder="Masukkan password Anda">
        </div>
      <input type="submit" name="submit" value="Login sekarang" class="form-btn">
      <p>Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
   </form>

</div>

<script>
</script>

</body>
</html>
