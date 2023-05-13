<?php
session_start();
if (!isset($_SESSION['logged'])) {
  header('location: index.php');
}

require('db/conection.php');
require('db/clientReceiptClass.php');

$error_modal = false;

try {
  $obj = new clientReceiptClass();
} catch (Exception $e) {
  $error_modal = true;
  $errormsg = $e->getMessage();
}

if (isset($_POST['searchClientReceipt'])) {
  try {
      $sql = "SELECT * FROM clientreceipt WHERE creceipttotal LIKE ? OR creceiptdate LIKE ? ORDER BY cReceiptDate DESC";
      $stmt = $conn->prepare($sql);
      $data = "%" . $_POST['data'] . "%";
      $stmt->bind_param("ss", $data, $data);
      $stmt->execute();
      $clientReceipts = $stmt->get_result();
  } catch (Exception $e) {
      $error_modal = true;
      $errormsg = $e->getMessage();
  }
} else {
  try {
      $clientReceipts = $obj->getClientReceipts($conn);
  } catch (Exception $e) {
      $error_modal = true;
      $errormsg = $e->getMessage();
  }
}

//<!-- HTML HEADER -->
include("templates/header.php");
include("layouts/sidebar.php"); ?>

<div class="base-users">
  <div class="container-bottom">
    <h3 class="text-center">Ventas</h3>
  </div>

  <form class="form-search" method="post">
    <div class="input-group mb-3">
      <input type="text" class="form-control" placeholder="Buscar venta" aria-label="Buscar venta" name="data">
      <button class="btn btn-secondary" type="submit" name="searchClientReceipt">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
          <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
        </svg>
      </button>
    </div>
  </form>

  <?php try { ?>
    <?php while ($clientReceipt = mysqli_fetch_object($clientReceipts)) :
      try {
        $sql = "SELECT * FROM users WHERE userId = '" . $clientReceipt->cReceiptUserId . "'";
        $usersP = $conn->query($sql);
        $userP = mysqli_fetch_object($usersP);
        $sql = "SELECT * FROM clients WHERE clientId = '" . $clientReceipt->cReceiptClientId . "'";
        $clientsP = $conn->query($sql);
        $clientP = mysqli_fetch_object($clientsP);
      } catch (Exception $e) {
        $error_modal = true;
        $errormsg = $e->getMessage();
      }
    ?>
      <div class="table table-responsive">
        <table class="table table-bordered">
          <thead class="table-light">
            <tr align="center" class="active">
              <th>Fecha</th>
              <th>Total</th>
              <th>Credito</th>
              <th>Efectivo</th>
              <th>Cambio</th>
              <th>Cliente</th>
              <th>Usuario</th>
            </tr>
          </thead>
          <tbody class="table-light">
            <tr align="center">
              <td><?= $clientReceipt->cReceiptDate ?></td>
              <td>$<?= $clientReceipt->cReceiptTotal ?></td>
              <td>$<?= $clientReceipt->cReceiptCreditAmount ?></td>
              <td>$<?= $clientReceipt->cReceiptAmount ?></td>
              <td>$<?= $clientReceipt->cReceiptChange ?></td>
              <td><?= $clientP->clientName ?> <?= $clientP->clientLastName ?></td>
              <td><?= $userP->userName ?> <?= $userP->userLastName ?></td>
            </tr>

            <tr align="center" class="active">
              <th colspan="4">Producto</th>
              <th>Cantidad</th>
              <th>Precio</th>
              <th>Subtotal</th>
            </tr>
            <?php
            try {
              $sql = "SELECT products.*, clientreceiptproducts.cReceiptPrice, clientreceiptproducts.cReceiptQuantity FROM clientreceiptproducts JOIN products ON clientreceiptproducts.cReceiptProductId = products.productId WHERE clientreceiptproducts.cReceiptId = '" . $clientReceipt->cReceiptId . "'";
              $products = $conn->query($sql);
              while ($product = mysqli_fetch_object($products)) :
            ?>
                <tr align="center">
                  <td colspan="4"><?= $product->productName ?></td>
                  <td><?= $product->cReceiptQuantity ?></td>
                  <td>$<?= $product->cReceiptPrice ?></td>
                  <td>$<?= $product->cReceiptQuantity * $product->cReceiptPrice ?></td>
                </tr>
            <?php endwhile;
            } catch (Exception $e) {
              $error_modal = true;
              $errormsg = $e->getMessage();
            }
            ?>
          </tbody>
        </table>
      </div>
    <?php endwhile; ?>
  <?php } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  } ?>
</div>
<!-- HTML END BODY -->
<?php
include("templates/footer.php");
?>

<?php if ($error_modal) :
  $error_modal = false;
  ///<!-- Error Modal -->
  include "templates/error_modal.php";
?>
  <script>
    $(function() {
      $('#error_modal').modal('show');
      setTimeout(() => {
        $('#error_modal').modal('hide');
        window.location.reload();
      }, 4000);
    })
  </script>
<?php endif; ?>