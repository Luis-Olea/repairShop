<?php
    require 'conection.php';
    session_start();
    $userEmail = $_POST['email'];
    $userPassword = $_POST['password'];
 
    $sql = "SELECT * ";
    $sql .= "FROM users ";
    $sql .= "WHERE userEmail = '". $userEmail ."'";
 
    $resultados = $conn->query($sql);
    $fila = mysqli_fetch_assoc($resultados);
 
    $passwordHash = $fila['userPassword'];

    if(password_verify($userPassword, $passwordHash)){
        $_SESSION['currentEmail'] = $userEmail;
        $_SESSION['logged'] = true;     
        header("Location: ../dashboard.php");
    } else {
        $_SESSION['error'] = "Credenciales incorrectas";
        header("Location: ../index.php");
    }
?>