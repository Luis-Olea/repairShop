<?php
session_start();
if (!isset($_SESSION['logged'])) {
  header('location: index.php');
}

require('db/conection.php');

$error_modal = false;

function getPreferencesConf($conn)
{
  try {
    $sql = "SELECT * FROM preferences WHERE storeId = 1";
    $result = $conn->query($sql);
    $store = $result->fetch_assoc();
  } catch (Exception $e) {
    return false;
  }
  return $store;
}

if (isset($_POST['updatePreferences']) and !empty($_POST['updatePreferences'])) {
  if (isset($_FILES['logoImage']) && $_FILES['logoImage']['size'] > 0) {
    $file = $_FILES['logoImage'];
    $allowedTypes = ['image/png'];
    if (in_array($file['type'], $allowedTypes)) {
      // Cambiar el nombre del archivo
      $newFileName = "logo.png";
      $destination = 'data/default/' . $newFileName;
      move_uploaded_file($file['tmp_name'], $destination);
      $_POST['logoImage'] = $newFileName;
    } else {
      $error_modal = true;
      $errormsg = 'El tipo de archivo es invalido';
    }
  }
  if (isset($_FILES['defaultImage']) && $_FILES['defaultImage']['size'] > 0) {
    $file = $_FILES['defaultImage'];
    $allowedTypes = ['image/png'];
    if (in_array($file['type'], $allowedTypes)) {
      // Cambiar el nombre del archivo
      $newFileName = "default.png";
      $destination = 'data/images/' . $newFileName;
      move_uploaded_file($file['tmp_name'], $destination);
      $_POST['defaultImage'] = $newFileName;
    } else {
      $error_modal = true;
      $errormsg = 'El tipo de archivo es invalido';
    }
  }

  $repply = getPreferencesConf($conn);
  if ($repply == false) {
    $error_modal = true;
    $errormsg = "Algo salio mal al leer las preferencias";
  } else {
    $store = $repply;
  }

  if (isset($_SESSION['storeName']) && isset($_POST['nameStore']) && isset($_POST['quantityNotification']) && isset($store['quantityNotification']) && isset($_POST['daysNotification']) && isset($store['daysNotification'])) {
    if ($store['storeName'] != $_POST['nameStore'] || $_POST['quantityNotification'] != $store['quantityNotification'] || $_POST['daysNotification'] != $store['daysNotification']) {
      try {
        $storeName = $_POST['nameStore'];
        $quantityNotification = $_POST['quantityNotification'];
        $daysNotification = $_POST['daysNotification'];
        $sql = "UPDATE preferences SET storeName = ?, quantityNotification = ?, daysNotification = ? WHERE storeId = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdi", $storeName, $quantityNotification, $daysNotification);
        $stmt->execute();
        $_SESSION['storeName'] = $storeName;
        if (mysqli_affected_rows($conn) <= 0) {
          $error_modal = true;
          $errormsg = 'No se realizo ningun cambio, o un dato es invalido';
        }
      } catch (Exception $e) {
        $error_modal = true;
        $errormsg = $e->getMessage();
      } finally {
        $repply = getPreferencesConf($conn);
        if ($repply == false) {
          $error_modal = true;
          $errormsg = "Algo salio mal al leer las preferencias";
        } else {
          $store = $repply;
        }
      }
    }
  }
}

$repply = getPreferencesConf($conn);
if ($repply == false) {
  $error_modal = true;
  $errormsg = "Algo salio mal al leer las preferencias";
} else {
  $store = $repply;
}

//<!-- HTML HEADER -->
include("templates/header.php");
include("layouts/sidebarConfiguration.php"); ?>

<div class="base-conf">

  <div class="container-bottom mb-4">
    <h3>Ajustes</h3>
  </div>

  <?php if ($_SESSION['roleId'] == 1) : ?>

    <div class="card" style="width: 90%;">
      <form method="post" enctype="multipart/form-data">
        <div class="card-header">
          <ul class="nav nav-pills card-header-pills" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Generales</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Imagenes</button>
            </li>
          </ul>
        </div>
        <div class="card-body text-center">
          <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
              <div class="input-group mb-3">
                <h4>Tienda</h4>
              </div>
              <div class="input-group mb-3">
                <span class="input-group-text" id="inputGroup-sizing-default">Nombre:</span>
                <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" name="nameStore" value="<?= $_SESSION['storeName'] ?>" required>
              </div>
              <div class="input-group mb-3">
                <h4>Notificaciones</h4>
              </div>
              <div class="input-group mb-3">
                <span class="input-group-text" id="inputGroup-sizing-default">Dias:</span>
                <input type="number" class="form-control" step="1" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" name="daysNotification" value="<?= $store['daysNotification'] ?>" required>
              </div>
              <div class="input-group">
                <span class="input-group-text" id="inputGroup-sizing-default">$:</span>
                <input type="number" class="form-control" step="1" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" name="quantityNotification" value="<?= $store['quantityNotification'] ?>" required>
              </div>
            </div>
            <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" tabindex="0">
              <div class="input-group mb-3">
                <h4>Imagenes</h4>
              </div>
              <div class="input-group mb-3">
                <input type="file" class="form-control" id="inputGroupFile02" name="logoImage">
                <label class="input-group-text" for="inputGroupFile02">Logo</label>
              </div>
              <div class="input-group">
                <input type="file" class="form-control" id="inputGroupFile02" name="defaultImage">
                <label class="input-group-text" for="inputGroupFile02">POS</label>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <div class="button-right-conf">
            <button type="submit" class="btn btn-primary" name="updatePreferences" value="Add">Actualizar valores</button>
          </div>
        </div>
      </form>
    </div>

  <?php else : ?>

    <div class="card" style="width: 90%;">
      <div class="card-header">
        <h4>Configuraciones</h4>
      </div>
      <div class="card-body text-center">
        <br>
        <h4>Actualmente solo puedes cambiar el tema con el boton de arriba.</h4>
        <br>
      </div>
    </div>

  <?php endif; ?>

</div>

<!-- HTML END BODY -->
<?php
include("templates/error_modal.php");
include("templates/footer.php");

if ($error_modal) :
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

<!-- Prevent data forwarding -->
<script type="text/javascript">
  /// DON'T DELETE.
  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
  }
</script>