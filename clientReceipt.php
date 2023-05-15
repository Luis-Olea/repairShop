<?php
session_start();
if (!isset($_SESSION['logged'])) {
  header('location: index.php');
}

if ($_SESSION['roleId'] == 3) {
  header('Location: secundaryDashboard.php');
  exit;
}


require('db/conection.php');
require('db/clientReceiptClass.php');

$error_modal = false;
$correct_modal = false;

try {
  $obj = new clientReceiptClass();
} catch (Exception $e) {
  $error_modal = true;
  $errormsg = $e->getMessage();
}

function printPDFTReceipt()
{
  try {
    require_once __DIR__ . '/vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf();
    // Ejecuta el código PHP para generar el contenido HTML
    ob_start();
    include 'templateTicket.php'; // Aquí debes poner la ruta al archivo que contiene el código PHP
    $html = ob_get_clean();
    // Agrega el código CSS desde un archivo externo
    $stylesheet = file_get_contents('stylesheet/bill.css');
    $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
    // Agrega el contenido HTML al archivo PDF
    $mpdf->WriteHTML($html);
    // Genera y muestra el archivo PDF
    $route = 'data/pdf/nota.pdf';
    $mpdf->Output($route, 'F');
  } catch (Exception $e) {
    return $e->getMessage();
  }
  return TRUE;
}

if (isset($_POST['generatePDF'])) {
  $_SESSION['cReceiptId'] = $_POST['cReceiptId'];
  $repplyPDF = printPDFTReceipt();
  if ($repplyPDF != TRUE) {
    $error_modal = true;
    $errormsg = $repplyPDF;
  } else {
    $correct_modal = true;
  }
  unset($_SESSION['cReceiptId']);
}

if (isset($_POST['searchClientReceipt'])) {
  try {
    $sql = "SELECT * FROM clientreceipt WHERE creceipttotal LIKE ? OR creceiptdate LIKE ? OR creceiptid LIKE ? ORDER BY cReceiptDate DESC";
    $stmt = $conn->prepare($sql);
    $data = "%" . $_POST['data'] . "%";
    $stmt->bind_param("sss", $data, $data, $data);
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
    <a href="clientPayment.php" class="btn btn-primary">
      Ver abonos
    </a>
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
              <th>Factura</th>
            </tr>
          </thead>
          <tbody class="table-light">
            <tr align="center">
              <td><?=  date("H:i:s - d/m/Y", strtotime($clientReceipt->cReceiptDate)) ?></td>
              <td>$<?= number_format($clientReceipt->cReceiptTotal, 2) ?></td>
              <td>$<?= number_format($clientReceipt->cReceiptCreditAmount, 2) ?></td>
              <td>$<?= number_format($clientReceipt->cReceiptAmount, 2) ?></td>
              <td>$<?= number_format($clientReceipt->cReceiptChange, 2) ?></td>
              <td><?= $clientP->clientName ?> <?= $clientP->clientLastName ?></td>
              <td><?= $userP->userName ?> <?= $userP->userLastName ?></td>
              <td>
                <form method="post">
                  <input type="hidden" name="cReceiptId" value="<?= $clientReceipt->cReceiptId  ?>">
                  <button class="btn btn-secondary" type="submit" name="generatePDF">Generar pdf</button>
                </form>
              </td>
            </tr>

            <tr align="center" class="active">
              <th colspan="5">Producto</th>
              <th>Cantidad</th>
              <th>Precio</th>
              <th>Subtotal</th>
            </tr>
            <?php
            try {
              $sql = "SELECT products.*, clientreceiptproducts.cReceiptPrice, clientreceiptproducts.cReceiptQuantity FROM clientreceiptproducts JOIN products ON clientreceiptproducts.cReceiptProductId = products.productId WHERE clientreceiptproducts.cReceipReceiptId = '" . $clientReceipt->cReceiptId . "'";
              $products = $conn->query($sql);
              while ($product = mysqli_fetch_object($products)) :
            ?>
                <tr align="center">
                  <td colspan="5  "><?= $product->productName ?></td>
                  <td><?= number_format($product->cReceiptQuantity) ?></td>
                  <td>$<?= number_format($product->cReceiptPrice, 2) ?></td>
                  <td>$<?= number_format($product->cReceiptQuantity * $product->cReceiptPrice, 2) ?></td>
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
include('templates/correct_modalGeneratePDF.php');
include("templates/error_modal.php");
include("templates/footer.php");
?>

<?php if ($correct_modal) :
  $correct_modal = false;
?>
  <script>
    $(function() {
      $('#correctBC').modal('show');
    })
  </script>;
<?php endif; ?>

<?php if ($error_modal) :
  $error_modal = false;
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