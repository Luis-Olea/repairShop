<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- CSS -->
    <link rel="stylesheet" href="stylesheet/login.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,600,0,0" />

    <title>Coravi</title>

</head>

<body>
    <!-- HTML BODY -->
    <div class="login-card-container">
        <div class="login-card">
            <div class="login-card-logo">
                <img src="images/logo.png" alt="logo">
            </div>
            <div class="login-card-header">
                <h1>Coravi</h1>
                <?php
                if (isset($_SESSION['error'])) {
                    echo "<font color='red'><h3>" . $_SESSION['error'] . "</h3>";
                    unset($_SESSION['error']);
                } else {
                    echo "<h3>Inicie sesion para continuar</h3>";
                }
                ?>
            </div>
            <form action="db/session.php" method="POST" class="login-card-form">
                <div class="form-item">
                    <span class="form-item-icon material-symbols-rounded">mail</span>
                    <input autocomplete="username" name="email" type="text" placeholder="Correo" id="emailForm" autofocus required>
                </div>
                <div class="form-item">
                    <span class="form-item-icon material-symbols-rounded">lock</span>
                    <input autocomplete="current-password" name="password" type="password" placeholder="Contraseña" id="passwordForm" required>
                </div>
                <!--
                <div class="form-item-other">
                    <div class="checkbox">
                        <input type="checkbox" id="rememberMeCheckbox" checked>
                        <label for="rememberMeCheckbox" style="color:#000000";>Recuerdame</label>
                    </div>
                </div>
                -->
                <button type="submit">Iniciar Sesión</button>
            </form>
        </div>
    </div>

</body>

</html>