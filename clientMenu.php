<?php
session_start();
if (!isset($_SESSION['logged'])) {
  header('location: index.php');
}

require('db/conection.php');
require('db/clientClass.php');

$updateClient_modal = false;
$deleteClient_modal = false;
$error_modal = false;

try {
  $obj = new ClientClass();
} catch (Exception $e) {
  $error_modal = true;
  $errormsg = $e->getMessage();
}

if (isset($_POST['addClient']) and !empty($_POST['addClient'])) {
  try {
    $obj->createClient($conn);
    if (mysqli_affected_rows($conn) > 0) {
      echo '<script type="text/javascript">window.location.reload();</script>';
    } else {
      $error_modal = true;
      $errormsg = 'Ocurrio un error';
    }
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  }
}

if (isset($_POST['getIdUpdateC'])) {
  try {
    $client = $obj->getClientById($conn);
    $_SESSION['client'] = mysqli_fetch_object($client);
    $updateClient_modal = true;
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  }
}

if (isset($_POST['updateClient']) and !empty($_POST['updateClient'])) {
  try {
    $obj->updateClient($conn);
    if (mysqli_affected_rows($conn) > 0) {
      echo '<script type="text/javascript">window.location.reload();</script>';
    } else {
      $error_modal = true;
      $errormsg = 'No se realizo ningun cambio, o un dato es invalido';
    }
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  }
}

if (isset($_POST['getIdDeleteC'])) {
  try {
    $client = $obj->getClientById($conn);
    $_SESSION['client'] = mysqli_fetch_object($client);
    $deleteClient_modal = true;
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  }
}

if (isset($_POST['deleteClient'])) {
  try {
    $obj->deleteClient($conn);
    if (mysqli_affected_rows($conn) > 0) {
      echo '<script type="text/javascript">window.location.reload();</script>';
    } else {
      $error_modal = true;
      $errormsg = 'Ocurrio un error o no se encontro al cliente';
    }
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  }
}

if (isset($_POST['searchSuppliers'])) {
  try {
    $sql = "SELECT * FROM clients WHERE clientName LIKE '%" . $_POST['data'] . "%'  or clientLastName LIKE '%" . $_POST['data'] . "%' or clientCellphone LIKE '%" . $_POST['data'] . "%' or clientEmail LIKE '%" . $_POST['data'] . "%' ";
    $clients = $conn->query($sql);
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  }
} else {
  try {
    $clients = $obj->getClients($conn);
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  }
}

///<!-- header -->
include('templates/header.php');
///<!-- sidebar -->
include('layouts/sidebar.php'); ?>

<div class="base-users">
  <div class="container-bottom">
    <h3 class="text-center">Clientes</h3>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addClient">
      Agregar
    </button>
  </div>

  <form class="form-search" method="post">
    <div class="input-group mb-3">
      <input type="text" class="form-control" placeholder="Buscar cliente" aria-label="Buscar cliente" name="data">
      <button class="btn btn-secondary" type="submit" name="searchSuppliers">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
          <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
        </svg>
      </button>
    </div>
  </form>

  <div class="table table-responsive">
    <table class="table table-bordered">
      <thead class="table-light">
        <tr align="center" class="active">
          <th class="d-md-none">Nombre</th>
          <th class="d-none d-md-table-cell">Nombre(s)</th>
          <th class="d-none d-md-table-cell">Apellidos(s)</th>
          <th class="d-none d-md-table-cell">Dirección</th>
          <th>Telefono</th>
          <th class="d-none d-md-table-cell">Correo</th>
          <th>Deuda</th>
          <th class="d-none d-md-table-cell">Limite de credito</th>
          <th>Editar</th>
          <th class="d-none d-md-table-cell">Eliminar</th>
        </tr>
      </thead>
      <tbody class="table-light">
        <?php try { ?>
          <?php while ($client = mysqli_fetch_object($clients)) : ?>
            <tr align="center">
              <td class="d-md-none"><?= $client->clientName ?> <?= $client->clientLastName ?></td>
              <td class="d-none d-md-table-cell"><?= $client->clientName ?></td>
              <td class="d-none d-md-table-cell"><?= $client->clientLastName ?></td>
              <td class="d-none d-md-table-cell"><?= $client->clientAddress ?></td>
              <td><?= $client->clientCellphone ?></td>
              <td class="d-none d-md-table-cell"><?= $client->clientEmail ?></td>
              <td><?= $client->clientDue ?></td>
              <td class="d-none d-md-table-cell"><?= $client->clientCreditLimit ?></td>
              <td>
                <form method="post">
                  <input type="submit" class="btn btn-primary" name="getIdUpdateC" value="Editar">
                  <input type="hidden" value="<?= $client->clientId ?>" name="clientId">
                </form>
              </td>
              <td class="d-none d-md-table-cell">
                <form method="post">
                  <input type="submit" class="btn btn-danger" name="getIdDeleteC" value="Eliminar">
                  <input type="hidden" value="<?= $client->clientId ?>" name="clientId">
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php } catch (Exception $e) {
          $error_modal = true;
          $errormsg = $e->getMessage();
        } ?>
      </tbody>
    </table>
  </div>
</div>

<!-- HTML END BODY -->
<?php include "templates/footer.php";

///<!-- addClient Modal -->
include('templates/addClient.php');

if ($updateClient_modal) :
  $updateClient_modal = false;
  ///<!-- editClient Modal -->
  include "templates/updateClient.php";
?>
  <script>
    $(function() {
      $('#editClient').modal('show');
    })
  </script>;
<?php endif;
if ($deleteClient_modal) :
  $deleteClient_modal = false;
  ////<!-- deleteClient Modal --> 
  include "templates/deleteClient.php";
?>
  <script>
    $(function() {
      $('#deleteClient').modal('show');
    })
  </script>;
<?php endif;
if ($error_modal) :
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