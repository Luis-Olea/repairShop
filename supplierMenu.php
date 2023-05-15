<?php
session_start();
if (!isset($_SESSION['logged'])) {
  header('location: index.php');
}

if ($_SESSION['roleId'] == 2) {
  header('Location: secundaryDashboard.php');
  exit;
}

require('db/conection.php');
require('db/supplierClass.php');

$updateSupplier_modal = false;
$deleteSupplier_modal = false;
$error_modal = false;

try {
  $obj = new SupplierClass();
} catch (Exception $e) {
  $error_modal = true;
  $errormsg = $e->getMessage();
}

if (isset($_POST['addSupplier']) and !empty($_POST['addSupplier'])) {
  try {
    $obj->createSuppplier($conn);
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

if (isset($_POST['getIdUpdateS'])) {
  try {
    $supplier = $obj->getSupplierById($conn);
    $_SESSION['supplier'] = mysqli_fetch_object($supplier);
    $updateSupplier_modal = true;
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  }
}

if (isset($_POST['updateSupplier']) and !empty($_POST['updateSupplier'])) {
  try {
    $obj->updateSupplier($conn);
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

if (isset($_POST['getIdDeleteS'])) {
  try {
    $supplier = $obj->getSupplierById($conn);
    $_SESSION['supplier'] = mysqli_fetch_object($supplier);
    $deleteSupplier_modal = true;
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  }
}

if (isset($_POST['deleteSupplier'])) {
  try {
    $obj->deleteSupplier($conn);
    if (mysqli_affected_rows($conn) > 0) {
      echo '<script type="text/javascript">window.location.reload();</script>';
    } else {
      $error_modal = true;
      $errormsg = 'Ocurrio un error o no se encontro al proveedor';
    }
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  }
}

if (isset($_POST['searchSuppliers'])) {
  try {
    $sql = "SELECT * FROM suppliers WHERE supplierName LIKE '%" . $_POST['data'] . "%'  or supplierLastName LIKE '%" . $_POST['data'] . "%' or supplierCellphone LIKE '%" . $_POST['data'] . "%' or supplierCellphone LIKE '%" . $_POST['data'] . "%' ";
    $suppliers = $conn->query($sql);
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  }
} else {
  try {
    $suppliers = $obj->getSupppliers($conn);
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
    <h3 class="text-center">Proveedores</h3>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSupplier">
      Agregar
    </button>
  </div>

  <form class="form-search" method="post">
    <div class="input-group mb-3">
      <input type="text" class="form-control" placeholder="Buscar proveedor" aria-label="Buscar proveedor" name="data">
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
          <th class="d-none d-md-table-cell">Compañia</th>
          <th>Deuda</th>
          <th>Editar</th>
          <th class="d-none d-md-table-cell">Eliminar</th>
        </tr>
      </thead>
      <tbody class="table-light">
        <?php try { ?>
          <?php while ($supplier = mysqli_fetch_object($suppliers)) : ?>
            <tr align="center">
              <td class="d-md-none"><?= $supplier->supplierName ?> <?= $supplier->supplierLastName ?></td>
              <td class="d-none d-md-table-cell"><?= $supplier->supplierName ?></td>
              <td class="d-none d-md-table-cell"><?= $supplier->supplierLastName ?></td>
              <td class="d-none d-md-table-cell"><?= $supplier->supplierAddress ?></td>
              <td><?= $supplier->supplierCellphone ?></td>
              <td class="d-none d-md-table-cell"><?= $supplier->supplierEmail ?></td>
              <td class="d-none d-md-table-cell"><?= $supplier->supplierBrand ?></td>
              <td>$<?= number_format($supplier->supplierDue, 2) ?></td>
              <td>
                <form method="post">
                  <input type="submit" class="btn btn-primary" name="getIdUpdateS" value="Editar">
                  <input type="hidden" value="<?= $supplier->supplierId ?>" name="supplierId">
                </form>
              </td>
              <td class="d-none d-md-table-cell">
                <form method="post">
                  <input type="submit" class="btn btn-danger" name="getIdDeleteS" value="Eliminar">
                  <input type="hidden" value="<?= $supplier->supplierId ?>" name="supplierId">
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

///<!-- addSupplier Modal -->
include('templates/addSupplier.php');

if ($updateSupplier_modal) :
  $updateSupplier_modal = false;
  ///<!-- editSupplier Modal -->
  include "templates/updateSupplier.php";
?>
  <script>
    $(function() {
      $('#editSupplier').modal('show');
    })
  </script>;

<?php endif;
if ($deleteSupplier_modal) :
  $deleteSupplier_modal = false;
  ////<!-- deleteSupplier Modal --> 
  include "templates/deleteSupplier.php";
?>
  <script>
    $(function() {
      $('#deleteSupplier').modal('show');
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