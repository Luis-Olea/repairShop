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
require('db/clientPaymentClass.php');

$error_modal = false;

try {
  $obj = new clientPaymentClass();
} catch (Exception $e) {
  $error_modal = true;
  $errormsg = $e->getMessage();
}

if (isset($_POST['clientId'])) {
  $_SESSION['totalPay'] = 0.00;
  $_SESSION['actualQuantity'] = 0.00;
  $_SESSION['creditToUse'] = 0.00;
  $_SESSION['cartClient'] = $_POST['clientId'];
}

if (isset($_POST['creditToUse'])) {
  if ($_POST['creditToUse'] > $_SESSION['clientDue']) {
    $_SESSION['creditToUse'] = $_SESSION['clientDue'];
  } else {
    $_SESSION['creditToUse'] = $_POST['creditToUse'];
  }
}

if (isset($_POST['inputMoney'])) {
  $_SESSION['actualQuantity'] = $_POST['inputMoney'];
}

if (isset($_POST['finishShopping'])) {
  try {
    $repply = $obj->createClientPayment($conn);
    if ($repply != TRUE) {
      $error_modal = true;
      $errormsg = $repply;
    }
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  } finally {
    unset($_SESSION['totalPay']);
    unset($_SESSION['actualQuantity']);
    unset($_SESSION['creditToUse']);
    unset($_SESSION['cartClient']);
  }
}

if (isset($_POST['searchClientPayment'])) {
  try {
    $sql = "SELECT * FROM clientpayments WHERE cPaymentAmount LIKE ? OR cPaymentDate LIKE ? ORDER BY cPaymentDate DESC";
    $stmt = $conn->prepare($sql);
    $data = "%" . $_POST['data'] . "%";
    $stmt->bind_param("ss", $data, $data);
    $stmt->execute();
    $clientPayments = $stmt->get_result();
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  }
} else {
  try {
    $clientPayments = $obj->getClientPayments($conn);
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
    <h3 class="text-center">Abonos clientes</h3>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addClientPayment">
      Registrar abono
    </button>
  </div>
  <form class="form-search" method="post">
    <div class="input-group mb-3">
      <input type="text" class="form-control" placeholder="Buscar abono" aria-label="Buscar abono" name="data">
      <button class="btn btn-secondary" type="submit" name="searchClientPayment">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
          <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
        </svg>
      </button>
    </div>
  </form>

  <?php try { ?>
    <?php while ($clientPayment = mysqli_fetch_object($clientPayments)) :
      try {
        $sql = "SELECT * FROM users WHERE userId = '" . $clientPayment->cPaymentUserId . "'";
        $usersP = $conn->query($sql);
        $userP = mysqli_fetch_object($usersP);
        $sql = "SELECT * FROM clients WHERE clientId = '" . $clientPayment->cPaymentClientId . "'";
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
              <th>Abono</th>
              <th>Cliente</th>
              <th>Usuario</th>
            </tr>
          </thead>
          <tbody class="table-light">
            <tr align="center">
              <td><?= date("H:i:s - d/m/Y", strtotime($clientPayment->cPaymentDate)) ?></td>
              <td>$<?= number_format($clientPayment->cPaymentAmount, 2) ?></td>
              <td><?= $clientP->clientName ?> <?= $clientP->clientLastName ?></td>
              <td><?= $userP->userName ?> <?= $userP->userLastName ?></td>
            </tr>
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
include("templates/addClientPayment.php");
include("templates/footer.php");
?>

<script>
  document.getElementById("clientName").addEventListener("change", function() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "clientPayment.php");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
      if (xhr.status === 200) {
        var parser = new DOMParser();
        var doc = parser.parseFromString(xhr.responseText, "text/html");
        var newContent = doc.querySelector(".clientInfo").innerHTML;
        document.querySelector(".clientInfo").innerHTML = newContent;
        var newContent = doc.querySelector(".changeInfo").innerHTML;
        document.querySelector(".changeInfo").innerHTML = newContent;
      }
    };
    xhr.send("clientId=" + encodeURIComponent(document.getElementById("clientName").value));
  });
  // Selecciona el contenedor del modal
  const clientPaymentModal = document.querySelector('#addClientPayment');

  // Agrega un controlador de eventos para detectar cambios en cualquier campo de entrada dentro del modal
  clientPaymentModal.addEventListener('change', function(event) {
    // Verifica si el elemento que cambió es un campo de entrada
    if (event.target.matches('#inputMoneyId')) {
      // Obtén el valor del campo de entrada
      let inputMoney = event.target.value;

      // Verifica si el valor del campo de entrada está vacío
      if (inputMoney === '') {
        inputMoney = 0;
      } else {
        inputMoney = parseInt(inputMoney);
      }
      // Crea una solicitud AJAX para enviar los datos al servidor
      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'clientPayment.php');
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.onload = function() {
        // Actualiza el contenido del modal con la información del carrito actualizada
        $.get('templates/addClientPayment.php', function(data) {
          var newContent = $(data).find('.changeInfo').html();
          $('#addClientPayment .changeInfo').html(newContent);
        });
        // Actualiza el contenido del modal con la información del carrito actualizada
        $.get('templates/addClientPayment.php', function(data) {
          var newContent = $(data).find('.modal-footer-cart2').html();
          $('#addClientPayment .modal-footer-cart2').html(newContent);
        });
      };
      xhr.send(`inputMoney=${inputMoney}`);
    }
    if (event.target.matches('#creditToUseId')) {
      // Obtén el valor del campo de entrada
      const creditToUse = parseInt(event.target.value);

      // Crea una solicitud AJAX para enviar los datos al servidor
      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'clientPayment.php');
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.onload = function() {
        // Actualiza el contenido del modal con la información del carrito actualizada
        $.get('templates/addClientPayment.php', function(data) {
          var newContent = $(data).find('.changeInfo').html();
          $('#addClientPayment .changeInfo').html(newContent);
        });
        // Actualiza el contenido del modal con la información del carrito actualizada
        $.get('templates/addClientPayment.php', function(data) {
          var newContent = $(data).find('.modal-footer-cart2').html();
          $('#addClientPayment .modal-footer-cart2').html(newContent);
        });
      };
      xhr.send(`creditToUse=${creditToUse}`);
    }
  });
</script>

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