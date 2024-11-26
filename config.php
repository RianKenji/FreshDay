<?php

    $conn = mysqli_connect("localhost","root","","sistem_pemesanan2");
    // Check connection
    if (mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
?>