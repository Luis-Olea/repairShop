<?php
require 'conection.php';
session_start();


var_dump($_SESSION['csrf_token']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar token CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        // Mostrar error
        $_SESSION['error'] = "Token CSRF inválido";
        exit;
    }

    $userEmail = $_POST['email'];
    $userPassword = $_POST['password'];

    $sql = "SELECT * FROM users WHERE userEmail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $resultados = $stmt->get_result();
    $fila = mysqli_fetch_assoc($resultados);

    $passwordHash = $fila['userPassword'];

    if (password_verify($userPassword, $passwordHash)) {
        $_SESSION['currentEmail'] = $userEmail;
        $_SESSION['logged'] = true;
        header("Location: ../dashboard.php");
    } else {
        $_SESSION['error'] = "Credenciales incorrectas";
        header("Location: ../index.php");
    }
}
?>