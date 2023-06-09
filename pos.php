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
require('db/productClass.php');
require('db/clientReceiptClass.php');

$error_modal = false;
$correct_modal = false;
$errormsg = NULL;

try {
  $obj = new ProductClass();
  $obj2 = new clientReceiptClass();
} catch (Exception $e) {
  $error_modal = true;
  $errormsg = $e->getMessage();
}

function printPDF() {
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
  }  catch (Exception $e) {
    return $e->getMessage();
  } 
  return TRUE;
}

function resetSessionVars()
{
  ///Sirve para comprobar si se realizo un cambio en el carrito y si ya se elegio un cliente.
  if (isset($_SESSION['cartClient'])) {
    unset($_SESSION['actualQuantity']);
    unset($_SESSION['creditToUse']);
    unset($_SESSION['cartClient']);
    unset($_SESSION['creditToUse']);
    unset($_SESSION['totalCart']);
    unset($_SESSION['creditAvailable']);
    unset($_SESSION['repplyNIP']);
    unset($_SESSION['TryrepplyNIP']);
  }
}

// Agregar un producto al carrito
if (isset($_POST['add_to_cart'])) {
  $productId = $_POST['productId'];
  $productPrice = $_POST['productPrice'];
  if (isset($_SESSION['cart'][$productId])) {
    $newQuantity = $_SESSION['cart'][$productId]['quantity'] += 1;
    $maxQuantity = $obj->checkStock($conn, $newQuantity, $productId);
    $_SESSION['cart'][$productId]['quantity'] = $maxQuantity;
  } else {
    $_SESSION['cart'][$productId] = [
      'quantity' => 1,
      'price' => $productPrice
    ];
  }
  resetSessionVars();
}

// Eliminar un producto del carrito
if (isset($_POST['remove_from_cart'])) {
  $productId = $_POST['productId'];
  if (isset($_SESSION['cart'][$productId])) {
    $_SESSION['cart'][$productId]['quantity'] -= 1;
    if ($_SESSION['cart'][$productId]['quantity'] == 0) {
      unset($_SESSION['cart'][$productId]);
    }
  }
  resetSessionVars();
}

// Agregar un producto del carrito
if (isset($_POST['add_from_cart'])) {
  $productId = $_POST['productId'];
  if (isset($_SESSION['cart'][$productId])) {
    $newQuantity = $_SESSION['cart'][$productId]['quantity'] += 1;
    $maxQuantity = $obj->checkStock($conn, $newQuantity, $productId);
    $_SESSION['cart'][$productId]['quantity'] = $maxQuantity;
  }
  resetSessionVars();
}

// Cambiar un producto del carrito
if (isset($_POST['productId']) && isset($_POST['quantity'])) {
  // Obtén el ID del producto y la cantidad
  $productId = $_POST['productId'];
  $quantity = $_POST['quantity'];

  // Verifica si hay suficiente stock
  $maxQuantity = $obj->checkStock($conn, $quantity, $productId);
  $_SESSION['cart'][$productId]['quantity'] = $maxQuantity;
  resetSessionVars();
}

// Eliminar un producto del carrito
if (isset($_POST['delete_from_cart'])) {
  $productId = $_POST['productId'];
  if (isset($_SESSION['cart'][$productId])) {
    unset($_SESSION['cart'][$productId]);
  }
  resetSessionVars();
}

if (isset($_POST['clientId'])) {
  $_SESSION['creditAvailable'] = 0;
  $_SESSION['actualQuantity'] = 0;
  $_SESSION['creditToUse']  = 0;
  $_SESSION['repplyNIP'] = false;
  $_SESSION['cartClient'] = $_POST['clientId'];
}

if (isset($_POST['inputMoney'])) {
  $_SESSION['actualQuantity'] = $_POST['inputMoney'];
}

if (isset($_POST['creditToUse'])) {
  if ($_POST['creditToUse'] > $_SESSION['creditAvailable']) {
    $_SESSION['creditToUse'] = $_SESSION['creditAvailable'];
  } else {
    $_SESSION['creditToUse'] = $_POST['creditToUse'];
  }
  if ($_SESSION['creditToUse'] > $_SESSION['totalCart']) {
    $_SESSION['creditToUse'] = $_SESSION['totalCart'];
  }
}

if (isset($_POST['validate_NIP'])) {
  $stmt = $conn->prepare("SELECT * FROM clients WHERE clientId = ?");
  $stmt->bind_param("s", $_SESSION['cartClient']);
  $stmt->execute();
  $result = $stmt->get_result();
  $client = $result->fetch_object();
  $currentNIP = $_POST['clientNIP'];
  if (password_verify($currentNIP, $client->clientNIP)) {
    $_SESSION['repplyNIP'] = true;
  } else {
    $_SESSION['repplyNIP'] = false;
  }
  $_SESSION['TryrepplyNIP'] = true;
}

if (isset($_POST['finishShopping'])) {
  try {
    $repply = $obj2->createClientReceipt($conn);
    if ($repply == TRUE) {
      $repplyPDF = printPDF();
      if ($repplyPDF != TRUE) {
        $error_modal = true;
        $errormsg = $repplyPDF;
      } else {
        $correct_modal = true;
      }
    } else {
      $error_modal = true;
      $errormsg = $repply;
    }
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  } finally {
    resetSessionVars();
    unset($_SESSION['cart']);
    unset($_SESSION['cReceiptId']);
  }
}

if (isset($_POST['searchProducts'])) {
  try {
    $sql = "SELECT * FROM products WHERE productQuantity > 0 AND (productName LIKE '%" . $_POST['data'] . "%'  or productBrand LIKE '%" . $_POST['data'] . "%' or productCategory LIKE '%" . $_POST['data'] . "%' or productCodeBar LIKE '%" . $_POST['data'] . "%')";
    $products = $conn->query($sql);
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  }
} else {
  try {
    $sql = "SELECT * FROM products WHERE productQuantity > 0";
    $products = $conn->query($sql);
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  }
}

//<!-- HTML HEADER -->
include("templates/header.php");
include("layouts/sidebarPos.php"); ?>

<div class="base-pos">
  <div class="container-bottom">
    <h3 class="text-center">POS</h3>
  </div>

  <form class="form-search" method="post">
    <div class="input-group mb-3">
      <input type="text" class="form-control" placeholder="Buscar producto" aria-label="Buscar producto" name="data">
      <button class="btn btn-secondary" type="submit" name="searchProducts">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
          <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
        </svg>
      </button>
    </div>
  </form>

  <div class="container-fluid text-center">
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
      <?php try {
        while ($product = mysqli_fetch_object($products)) : ?>
          <div class="col">
            <div class="card">
              <div class="container-photo-pos">
                <img src="data/images/<?= $product->productImage ?>" class="card-img-top-t" alt="...">
              </div>
              <div class="card-body">
                <h5 class="card-title"><?= substr($product->productName, 0, 28); ?></h5>
                <p class="card-text">Marca: <?= $product->productBrand ?><br>Codigo: <?= $product->productCodebar ?><br>Precio: $<?= number_format($product->productPrice, 2) ?><br>Pzs: <?= number_format($product->productQuantity) ?></p>
              </div>
              <div class="card-footer">
                <form method="post" class="addProductCart">
                  <div class="d-grid gap-2">
                    <input type="hidden" name="productPrice" value="<?= $product->productPrice ?>">
                    <input type="hidden" name="productId" value="<?= $product->productId ?>">
                    <button type="submit" name="add_to_cart" class="btn btn-primary">Agregar al carrito</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php } catch (Exception $e) {
        $error_modal = true;
        $errormsg = $e->getMessage();
      } ?>
    </div>
  </div>
</div>

<!-- HTML END BODY -->

<?php
include('templates/correct_modal.php');
include("templates/error_modal.php");
include("templates/cart.php");
include("templates/footer.php"); ?>

<script>
  $(".addProductCart").on("submit", function(event) {
    event.preventDefault();
    var productId = $(this).find("input[name='productId']").val();
    var productPrice = $(this).find("input[name='productPrice']").val();
    $.ajax({
      type: "POST",
      url: "pos.php",
      data: {
        add_to_cart: true,
        productId: productId,
        productPrice: productPrice
      },
      success: function(response) {
        // Actualiza el contenido del modal con la información del carrito actualizada
        $("#cart .cart1").load("templates/cart.php .cart1", function() {
          // Calcula el total de artículos en el carrito sumando los valores de todos los campos de entrada .inputCartQuantity
          const cartCount = Array.from($('#cart .cart1').find('.inputCartQuantity'))
            .reduce((total, input) => total + parseInt(input.value), 0);

          // Actualiza el total de artículos en el carrito
          $("#art-count").data("count", cartCount);
          $("#art-count").text("(" + cartCount + ")");
        });
        // Actualiza el contenido del otro modal con la información del carrito actualizada
        $.get('templates/cart.php', function(data) {
          var newContent = $(data).find('.clientInfo').html();
          $('#cart2 .clientInfo').html(newContent);
        });
        $.get('templates/cart.php', function(data) {
          var newContent = $(data).find('#clientName').html();
          $('#cart2 #clientName').html(newContent);
        });
        // Actualiza el contenido del modal con la información del carrito actualizada
        $.get('templates/cart.php', function(data) {
          var newContent = $(data).find('.modal-footer-cart').html();
          $('#cart .modal-footer-cart').html(newContent);
        });
      }
    });
  });

  $('body').on('submit', '.deleteProductCart', function(event) {
    event.preventDefault();
    var productId = $(this).find("input[name='productId']").val();
    $.ajax({
      type: "POST",
      url: "pos.php",
      data: {
        remove_from_cart: true,
        productId: productId
      },
      success: function(response) {
        // Actualiza el total de artículos en el carrito
        var cartCount = parseInt($("#art-count").data("count"));
        $("#art-count").data("count", cartCount - 1);
        $("#art-count").text("(" + (cartCount - 1) + ")");

        // Actualiza el contenido del modal con la información del carrito actualizada
        $.get('templates/cart.php', function(data) {
          var newContent = $(data).find('.cart1').html();
          $('#cart .cart1').html(newContent);
        });
        // Actualiza el contenido del otro modal con la información del carrito actualizada
        $.get('templates/cart.php', function(data) {
          var newContent = $(data).find('.clientInfo').html();
          $('#cart2 .clientInfo').html(newContent);
        });
        $.get('templates/cart.php', function(data) {
          var newContent = $(data).find('#clientName').html();
          $('#cart2 #clientName').html(newContent);
        });
      }
    });
  });
  $('body').on('submit', '.addOneProductCart', function(event) {
    event.preventDefault();
    var productId = $(this).find("input[name='productId']").val();
    $.ajax({
      type: "POST",
      url: "pos.php",
      data: {
        add_from_cart: true,
        productId: productId
      },
      success: function(response) {
        // Actualiza el contenido del modal con la información del carrito actualizada
        $.get('templates/cart.php', function(data) {
          var newContent = $(data).find('.cart1').html();
          $('#cart .cart1').html(newContent);

          // Calcula el total de artículos en el carrito sumando los valores de todos los campos de entrada .inputCartQuantity
          const cartCount = Array.from($('#cart .cart1').find('.inputCartQuantity'))
            .reduce((total, input) => total + parseInt(input.value), 0);

          // Actualiza el total de artículos en el carrito
          $("#art-count").data("count", cartCount);
          $("#art-count").text("(" + cartCount + ")");
        });
        // Actualiza el contenido del otro modal con la información del carrito actualizada
        $.get('templates/cart.php', function(data) {
          var newContent = $(data).find('.clientInfo').html();
          $('#cart2 .clientInfo').html(newContent);
        });
        $.get('templates/cart.php', function(data) {
          var newContent = $(data).find('#clientName').html();
          $('#cart2 #clientName').html(newContent);
        });
      }
    });
  });
  $('body').on('submit', '.delete_from_cart', function(event) {
    event.preventDefault();
    var productId = $(this).find("input[name='productId']").val();
    $.ajax({
      type: "POST",
      url: "pos.php",
      data: {
        delete_from_cart: true,
        productId: productId
      },
      success: function(response) {
        // Actualiza el contenido del modal con la información del carrito actualizada
        $.get('templates/cart.php', function(data) {
          var newContent = $(data).find('.cart1').html();
          $('#cart .cart1').html(newContent);

          // Calcula el total de artículos en el carrito sumando los valores de todos los campos de entrada .inputCartQuantity
          const cartCount = Array.from($('#cart .cart1').find('.inputCartQuantity'))
            .reduce((total, input) => total + parseInt(input.value), 0);

          // Actualiza el total de artículos en el carrito
          $("#art-count").data("count", cartCount);
          $("#art-count").text("(" + cartCount + ")");
        });
        // Actualiza el contenido del otro modal con la información del carrito actualizada
        $.get('templates/cart.php', function(data) {
          var newContent = $(data).find('.clientInfo').html();
          $('#cart2 .clientInfo').html(newContent);
        });
        $.get('templates/cart.php', function(data) {
          var newContent = $(data).find('#clientName').html();
          $('#cart2 #clientName').html(newContent);
        });
      }
    });
  });
  // Selecciona el contenedor del modal
  const modal = document.querySelector('#cart');

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
      xhr.open('POST', 'pos.php');
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.onload = function() {
        // Actualiza el contenido del modal con la información del carrito actualizada
        $.get('templates/cart.php', function(data) {
          var newContent = $(data).find('.cart1').html();
          $('#cart .cart1').html(newContent);

          // Calcula el total de artículos en el carrito sumando los valores de todos los campos de entrada .inputCartQuantity
          const cartCount = Array.from(modal.querySelectorAll('.inputCartQuantity'))
            .reduce((total, input) => total + parseInt(input.value), 0);

          // Actualiza el total de artículos en el carrito
          $("#art-count").data("count", cartCount);
          $("#art-count").text("(" + cartCount + ")");
        });
      };
      // Actualiza el contenido del otro modal con la información del carrito actualizada
      $.get('templates/cart.php', function(data) {
        var newContent = $(data).find('.clientInfo').html();
        $('#cart2 .clientInfo').html(newContent);
      });
      $.get('templates/cart.php', function(data) {
        var newContent = $(data).find('#clientName').html();
        $('#cart2 #clientName').html(newContent);
      });
      xhr.send(`productId=${productId}&quantity=${quantity}`);
    }
  });
  document.getElementById("clientName").addEventListener("change", function() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "pos.php");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
      if (xhr.status === 200) {
        var parser = new DOMParser();
        var doc = parser.parseFromString(xhr.responseText, "text/html");
        var newContent = doc.querySelector(".clientInfo").innerHTML;
        document.querySelector(".clientInfo").innerHTML = newContent;
        var newContent = doc.querySelector(".modal-footer-cart2").innerHTML;
        document.querySelector(".modal-footer-cart2").innerHTML = newContent;
      }
    };
    xhr.send("clientId=" + encodeURIComponent(document.getElementById("clientName").value));
  });
  // Selecciona el contenedor del modal
  const modal2 = document.querySelector('#cart2');

  // Agrega un controlador de eventos para detectar cambios en cualquier campo de entrada dentro del modal
  modal2.addEventListener('change', function(event) {
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
      xhr.open('POST', 'pos.php');
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.onload = function() {
        // Actualiza el contenido del modal con la información del carrito actualizada
        $.get('templates/cart.php', function(data) {
          var newContent = $(data).find('.changeInfo').html();
          $('#cart2 .changeInfo').html(newContent);
        });
        // Actualiza el contenido del modal con la información del carrito actualizada
        $.get('templates/cart.php', function(data) {
          var newContent = $(data).find('.modal-footer-cart2').html();
          $('#cart2 .modal-footer-cart2').html(newContent);
        });
      };
      xhr.send(`inputMoney=${inputMoney}`);
    }
    if (event.target.matches('#creditToUseId')) {
      // Obtén el valor del campo de entrada
      const creditToUse = parseInt(event.target.value);

      // Crea una solicitud AJAX para enviar los datos al servidor
      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'pos.php');
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.onload = function() {
        // Actualiza el contenido del modal con la información del carrito actualizada
        $.get('templates/cart.php', function(data) {
          var newContent = $(data).find('.changeInfo').html();
          $('#cart2 .changeInfo').html(newContent);
        });
        // Actualiza el contenido del modal con la información del carrito actualizada
        $.get('templates/cart.php', function(data) {
          var newContent = $(data).find('.modal-footer-cart2').html();
          $('#cart2 .modal-footer-cart2').html(newContent);
        });
      };
      xhr.send(`creditToUse=${creditToUse}`);
    }
  });
  $('body').on('submit', '.validateNIP', function(event) {
    event.preventDefault();
    var clientNIP = $(this).find("input[name='clientNIP']").val();
    $.ajax({
      type: "POST",
      url: "pos.php",
      data: {
        validate_NIP: true,
        clientNIP: clientNIP
      },
      success: function(response) {
        console.log('actualizando la modal rey');
        // Actualiza el contenido del otro modal con la información del carrito actualizada
        $.get('templates/cart.php', function(data) {
          var newContent = $(data).find('.repplyNIPM').html();
          $('#cart2 .repplyNIPM').html(newContent);
        });
        // Actualiza el contenido del modal con la información del carrito actualizada
        $.get('templates/cart.php', function(data) {
          var newContent = $(data).find('.modal-footer-cart2').html();
          $('#cart2 .modal-footer-cart2').html(newContent);
        });
      }
    });
  });
</script>

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