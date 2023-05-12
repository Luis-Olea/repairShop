<?php
session_start();
if (!isset($_SESSION['logged'])) {
  header('location: index.php');
}

//<!-- HTML HEADER -->
include "templates/header.php";
?>

<?php include('layouts/sidebarConfiguration.php'); ?>

<div class="base">

Aqui van las configuraciones

</div>

<!-- HTML END BODY -->
<?php include "templates/footer.php"; ?>