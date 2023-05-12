<?php
session_start();
if (!isset($_SESSION['logged'])) {
  header('location: index.php');
}

require('db/conection.php');
require('db/suppPaymentsClass.php');

$error_modal = false;

try {
  $obj = new SuppPaymentsClass();
} catch (Exception $e) {
  $error_modal = true;
  $errormsg = $e->getMessage();
}

// Agregar un producto al carrito
if (isset($_POST['add_Product_Supp_Cart'])) {
  $productId = $_POST['productId'];
  $purchasePrice = 0; // Obtener el precio del producto desde la solicitud
  if (isset($_SESSION['cartS'][$productId])) {
    $_SESSION['cartS'][$productId]['quantity'] += 1;
  } else {
    $_SESSION['cartS'][$productId] = [
      'quantity' => 1,
      'price' => $purchasePrice
    ];
  }
}
if (isset($_POST['productId']) && isset($_POST['quantity'])) {
  // Obtén el ID del producto y la cantidad
  $productId = $_POST['productId'];
  $quantity = $_POST['quantity'];

  // Actualiza la cantidad en el carrito de compras
  if (isset($_SESSION['cartS'][$productId])) {
    $_SESSION['cartS'][$productId]['quantity'] = $quantity;
  } else {
    $_SESSION['cartS'][$productId] = [
      'quantity' => $quantity
    ];
  }
}

// Cambiar un producto del carrito
if (isset($_POST['productId']) && isset($_POST['purchasePrice'])) {
  // Obtén el ID del producto y la cantidad
  $productId = $_POST['productId'];
  $purchasePrice = $_POST['purchasePrice'];

  // Actualiza la cantidad en el carrito de compras
  if (isset($_SESSION['cartS'][$productId])) {
    $_SESSION['cartS'][$productId]['price'] = $purchasePrice;
  } else {
    $_SESSION['cartS'][$productId] = [
      'quantity' => 1, // Establece una cantidad predeterminada o busca la cantidad real en la base de datos
      'price' => $purchasePrice
    ];
  }
}

// Eliminar un producto del carrito
if (isset($_POST['delete_from_cart'])) {
  $productId = $_POST['productId'];
  if (isset($_SESSION['cartS'][$productId])) {
    unset($_SESSION['cartS'][$productId]);
  }
}

if (isset($_POST['addSuppPayments'])) {
  foreach ($_SESSION['cartS'] as $product_id => $item) {
    $quantity = $item['quantity'];
    $purchasePrice = $item['price'];
    $idProduct = $product_id;
    try {
      $sql = "UPDATE products SET productQuantity = productQuantity + ?, productPricePurchase = ? WHERE productId = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("idi", $quantity, $purchasePrice, $idProduct);
      $stmt->execute();
      if ($stmt->affected_rows > 0) {
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
  try {
    $obj->createSuppPayment($conn);
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
  unset($_SESSION['cartS']);
}

if (isset($_POST['productSupplier'])) {
  unset($_SESSION['cartS']);
  $_SESSION['currentSupplierCart'] = $_POST['productSupplier'];
}

if (isset($_POST['searchSuppPayment'])) {
  try {
    $sql = "SELECT * FROM supppayments WHERE paymentDate LIKE '%" . $_POST['data'] . "%'  or paymentAmount LIKE '%" . $_POST['data'] . "%' ";
    $suppPayments = $conn->query($sql);
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  }
} else {
  try {
    $suppPayments = $obj->getSuppPayments($conn);
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
    <h3 class="text-center">Pedidos</h3>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSuppPayment">
      Agregar
    </button>
  </div>

  <form class="form-search" method="post">
    <div class="input-group mb-3">
      <input type="text" class="form-control" placeholder="Buscar pedido" aria-label="Buscar pedido" name="data">
      <button class="btn btn-secondary" type="submit" name="searchSuppPayment">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
          <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
        </svg>
      </button>
    </div>
  </form>

  <?php try { ?>
    <?php while ($suppPayment = mysqli_fetch_object($suppPayments)) :
      try {
        $sql = "SELECT * FROM users WHERE userId = '" . $suppPayment->paymentUserId . "'";
        $usersP = $conn->query($sql);
        $userP = mysqli_fetch_object($usersP);
        $sql = "SELECT * FROM suppliers WHERE supplierId = '" . $suppPayment->paymentSupplierId . "'";
        $suppliersP = $conn->query($sql);
        $supplierP = mysqli_fetch_object($suppliersP);
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
              <th>Proveedor</th>
              <th>Usuario</th>
            </tr>
          </thead>
          <tbody class="table-light">
            <tr align="center">
              <td><?= $suppPayment->paymentDate ?></td>
              <td>$<?= $suppPayment->paymentAmount ?></td>
              <td><?= $supplierP->supplierName ?> <?= $supplierP->supplierLastName ?></td>
              <td><?= $userP->userName ?> <?= $userP->userLastName ?></td>
            </tr>

            <tr align="center" class="active">
              <th>Producto</th>
              <th>Cantidad</th>
              <th>Precio</th>
              <th>Subtotal</th>
            </tr>
            <?php
            try {
              $sql = "SELECT products.*, supppaymentproducts.purchasePrice, supppaymentproducts.quantity FROM supppaymentproducts JOIN products ON supppaymentproducts.suppProductId = products.productId WHERE supppaymentproducts.supppaymentId = '" . $suppPayment->paymentId . "'";
              $products = $conn->query($sql);
              while ($product = mysqli_fetch_object($products)) :
            ?>
                <tr align="center">
                  <td><?= $product->productName ?></td>
                  <td><?= $product->quantity ?></td>
                  <td>$<?= $product->purchasePrice ?></td>
                  <td>$<?= $product->quantity * $product->purchasePrice ?></td>

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
include("templates/addSuppPayments.php");
include("templates/footer.php");
?>
<script>
  $(".addProductSuppCart").on("submit", function(event) {
    event.preventDefault();
    var productId = $(this).find("select[name='productId']").val();
    $.ajax({
      type: "POST",
      url: "suppPayments.php",
      data: {
        add_Product_Supp_Cart: true,
        productId: productId
      },
      success: function(response) {
        // Actualiza el contenido del modal con la información del carrito actualizada
        $("#addSuppPayment .tableOrderSupp").load("templates/addSuppPayments.php .tableOrderSupp");
      }
    });
  });
  $('.tableOrderSupp').on('submit', '.delete_from_cart', function(event) {
    event.preventDefault();
    var productId = $(this).find("input[name='productId']").val();
    $.ajax({
      type: "POST",
      url: "suppPayments.php",
      data: {
        delete_from_cart: true,
        productId: productId
      },
      success: function(response) {
        // Actualiza el contenido del modal con la información del carrito actualizada
        $("#addSuppPayment .tableOrderSupp").load("templates/addSuppPayments.php .tableOrderSupp");
      }
    });
  });
  // Selecciona el contenedor del modal
  const modal = document.querySelector('#addSuppPayment');

  // Agrega un controlador de eventos para detectar cambios en cualquier campo de entrada dentro del modal
  modal.addEventListener('change', function(event) {
    // Verifica si el elemento que cambió es un campo de entrada
    if (event.target.matches('.inputCartQuantity')) {
      // Obtén el valor del campo de entrada
      const quantity = parseInt(event.target.value);

      // Obtén el ID del producto
      const productId = event.target.previousElementSibling.value;

      // Crea una solicitud AJAX para enviar los datos al servidor
      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'suppPayments.php');
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.onload = function() {

        // Actualiza el contenido del modal con la información del carrito actualizada
        $.get('templates/addSuppPayments.php', function(data) {
          var newContent = $(data).find('.tableOrderSupp').html();
          $('#addSuppPayment .tableOrderSupp').html(newContent);
        });
      };
      xhr.send(`productId=${productId}&quantity=${quantity}`);
    }
  });

  modal.addEventListener('change', function(event) {
    // Verifica si el elemento que cambió es un campo de entrada
    if (event.target.matches('.inputPurchasePrice')) {
      // Obtén el valor del campo de entrada
      const purchasePrice = parseInt(event.target.value);

      // Obtén el ID del producto
      const productId = event.target.previousElementSibling.value;

      // Crea una solicitud AJAX para enviar los datos al servidor
      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'suppPayments.php');
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.onload = function() {

        // Actualiza el contenido del modal con la información del carrito actualizada
        $.get('templates/addSuppPayments.php', function(data) {
          var newContent = $(data).find('.tableOrderSupp').html();
          $('#addSuppPayment .tableOrderSupp').html(newContent);
        });
      };
      xhr.send(`productId=${productId}&purchasePrice=${purchasePrice}`);
    }
  });
  document.getElementById("suppliersNames").addEventListener("change", function() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "suppPayments.php");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
      if (xhr.status === 200) {
        var parser = new DOMParser();
        var doc = parser.parseFromString(xhr.responseText, "text/html");
        var newContent = doc.querySelector(".addProductSuppCart").innerHTML;
        document.querySelector(".addProductSuppCart").innerHTML = newContent;
      }
    };
    xhr.send("productSupplier=" + encodeURIComponent(document.getElementById("suppliersNames").value));
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