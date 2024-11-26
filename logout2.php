<?php

@include 'config.php';

session_start();
session_unset();
session_destroy();

header('location:user-view/index2.php');

?>