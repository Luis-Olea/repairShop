<?php
session_start();
if (!isset($_SESSION['logged'])) {
  header('location: index.php');
}
//<!-- HTML HEADER -->
include "templates/header.php";
?>

<?php include('layouts/sidebar.php'); ?>

<div class="base-dashboard">
  <h1>Bienvenido <?=$_SESSION['currentEmail']?></h1>

  <div class="container">
    <div class="row">
        <div class="col-sm">
            <div class="card">
                <div class="card-body">
                    Usuarios conectados en este momento: <strong><span id="connected_users">0</span></strong>
                </div>
            </div>
        </div>
        <div class="col-sm">
            <div class="card">
                <div class="card-body">
                    Facturación del día: <strong>$ <span id="daily_revenue">0.00</span></strong>
                </div>
            </div>  
        </div>
    </div>
</div>



</div>

<!-- HTML END BODY -->
<?php include "templates/footer.php"; ?>