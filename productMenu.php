<?php
session_start();
if (!isset($_SESSION['logged'])) {
  header('location: index.php');
}

require('db/conection.php');
require('db/productClass.php');

$updateProduct_modal = false;
$deleteProduct_modal = false;
$error_modal = false;

try {
  $obj = new ProductClass();
} catch (Exception $e) {
  $error_modal = true;
  $errormsg = $e->getMessage();
}

if (isset($_POST['addProduct']) and !empty($_POST['addProduct'])) {
  if (isset($_FILES['productImage'])) {
    $file = $_FILES['productImage'];
    $allowedTypes = ['image/jpeg', 'image/png'];
    if (in_array($file['type'], $allowedTypes)) {
      $destination = 'data/images/' . $file['name'];
      move_uploaded_file($file['tmp_name'], $destination);
      $_POST['productImage'] = $file['name'];
    } else {
      $error_modal = true;
      $errormsg = 'El tipo de archivo es invalido';
    }
  } else {
    $_POST['productImage'] = 'default.png';
  }
  try {
    $obj->createProduct($conn);
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

if (isset($_POST['getIdUpdateP'])) {
  try {
    $product = $obj->getProductsById($conn);
    $_SESSION['product'] = mysqli_fetch_object($product);
    $updateProduct_modal = true;
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  }
}

if (isset($_POST['updateProduct']) and !empty($_POST['updateProduct'])) {
  if (isset($_FILES['productImage'])) {
    $file = $_FILES['productImage'];
    $allowedTypes = ['image/jpeg', 'image/png'];
    if (in_array($file['type'], $allowedTypes)) {
      $destination = 'data/images/' . $file['name'];
      move_uploaded_file($file['tmp_name'], $destination);
      $_POST['productImage'] = $file['name'];
    } else {
      $error_modal = true;
      $errormsg = 'El tipo de archivo es invalido';
    }
  }
  try {
    $obj->updateProduct($conn);
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

if (isset($_POST['getIdDeleteP'])) {
  try {
    $product = $obj->getProductsById($conn);
    $_SESSION['product'] = mysqli_fetch_object($product);
    $deleteProduct_modal = true;
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  }
}

if (isset($_POST['deleteProduct'])) {
  try {
    $obj->deleteProduct($conn);
    if (mysqli_affected_rows($conn) > 0) {
      echo '<script type="text/javascript">window.location.reload();</script>';
    } else {
      $error_modal = true;
      $errormsg = 'Ocurrio un error o no se encontro el producto';
    }
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  }
}

if (isset($_POST['searchSuppliers'])) {
  try {
    $sql = "SELECT * FROM products WHERE productName LIKE '%" . $_POST['data'] . "%'  or productBrand LIKE '%" . $_POST['data'] . "%' or productCategory LIKE '%" . $_POST['data'] . "%' or productCodeBar LIKE '%" . $_POST['data'] . "%' ";
    $products = $conn->query($sql);
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  }
} else {
  try {
    $products = $obj->getProducts($conn);
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
    <h3 class="text-center">Productos</h3>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProduct">
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
        <tr align="center" style="vertical-align:middle;">
          <th>Nombre</th>
          <th class="d-none d-md-table-cell">Marca</th>
          <th class="d-none d-md-table-cell">Descripción</th>
          <th class="d-none d-md-table-cell">Categoría</th>
          <th>Stock</th>
          <th>Precio</th>
          <th class="d-none d-md-table-cell">Proveedor</th>
          <th class="d-none d-md-table-cell">Cod. barras</th>
          <th class="d-none d-md-table-cell">Imagen</th>
          <th>Editar</th>
          <th class="d-none d-md-table-cell">Eliminar</th>
        </tr>
      </thead>
      <tbody class="table-light">
        <?php try { ?>
          <?php while ($product = mysqli_fetch_object($products)) : ?>
            <tr align="center" style="vertical-align:middle;">
              <td><?= $product->productName ?></td>
              <td class="d-none d-md-table-cell"><?= $product->productBrand ?></td>
              <td class="d-none d-md-table-cell"><?= $product->productDescription ?></td>
              <td class="d-none d-md-table-cell"><?= $product->productCategory ?></td>
              <td><?= $product->productQuantity ?></td>
              <td>$<?= $product->productPrice ?></td>
              <td class="d-none d-md-table-cell"><?= $product->productSupplier ?></td>
              <td class="d-none d-md-table-cell"><?= $product->productCodebar ?></td>
              <td class="d-none d-md-table-cell"><img class="img-table" src="data/images/<?=$product->productImage ?>"></td>
              <td>
                <form method="post">
                  <input type="submit" class="btn btn-primary" name="getIdUpdateP" value="Editar">
                  <input type="hidden" value="<?=$product->productId?>" name="productId">
                </form>
              </td>
              <td class="d-none d-md-table-cell">
                <form method="post">
                  <input type="submit" class="btn btn-danger" name="getIdDeleteP" value="Eliminar">
                  <input type="hidden" value="<?=$product->productId?>" name="productId">
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

///<!-- addProduct Modal -->
include('templates/addProduct.php');

if ($updateProduct_modal) :
  $updateProduct_modal = false;
  ///<!-- editProduct Modal -->
  include "templates/updateProduct.php";
?>
  <script>
    $(function() {
      $('#editProduct').modal('show');
    })
  </script>;

<?php endif;
if ($deleteProduct_modal) :
  $deleteProduct_modal = false;
  ////<!-- deleteProduct Modal --> 
  include "templates/deleteProduct.php";
?>
  <script>
    $(function() {
      $('#deleteProduct').modal('show');
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