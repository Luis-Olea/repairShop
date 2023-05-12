<?php
    require 'conection.php';
    session_start();
    session_destroy();
    mysqli_close($conn);
    header("location: ../index.php");
    exit();
?>