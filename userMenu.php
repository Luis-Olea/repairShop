<?php
session_start();
if (!isset($_SESSION['logged'])) {
  header('location: index.php');
}

require('db/conection.php');
require('db/usersClass.php');

$updateUser_modal = false;
$deleteUser_modal = false;
$error_modal = false;

try {
  $obj = new UsersClass();
} catch (Exception $e) {
  $error_modal = true;
  $errormsg = $e->getMessage();
}

if (isset($_POST['addUser']) and !empty($_POST['addUser'])) {
  try {
    $obj->createUser($conn);
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

if (isset($_POST['getIdUpdate'])) {
  try {
    $user = $obj->getUserById($conn);
    $_SESSION['user'] = mysqli_fetch_object($user);
    $updateUser_modal = true;
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  }
}

if (isset($_POST['updateUser']) and !empty($_POST['updateUser'])) {
  try {
    $obj->updateUser($conn);
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

if (isset($_POST['getIdDelete'])) {
  try {
    $user = $obj->getUserById($conn);
    $_SESSION['user'] = mysqli_fetch_object($user);
    $deleteUser_modal = true;
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  }
}

if (isset($_POST['deleteUser'])) {
  try {
    $obj->deleteuser($conn);
    if (mysqli_affected_rows($conn) > 0) {
      echo '<script type="text/javascript">window.location.reload();</script>';
    } else {
      $error_modal = true;
      $errormsg = 'Ocurrio un error o no se encontro al usuario';
    }
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  }
}

if (isset($_POST['searchUsers'])) {
  try {
    $sql = "SELECT * FROM users WHERE userName LIKE '%" . $_POST['userName'] . "%'  or userLastName LIKE '%" . $_POST['userName'] . "%' or userCellphone LIKE '%" . $_POST['userName'] . "%' or userEmail LIKE '%" . $_POST['userName'] . "%' ";
    $users = $conn->query($sql);
  } catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
  }
} else {
  try {
    $users = $obj->getUsers($conn);
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
    <h3 class="text-center">Usuarios</h3>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUser">
      Agregar
    </button>
  </div>

  <form class="form-search" method="post">
    <div class="input-group mb-3">
      <input type="text" class="form-control" placeholder="Buscar usuario" aria-label="Buscar usuario" name="userName">
      <button class="btn btn-secondary" type="submit" name="searchUsers">
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
          <th>Editar</th>
          <th class="d-none d-md-table-cell">Eliminar</th>
        </tr>
      </thead>
      <tbody class="table-light">
        <?php try { ?>
          <?php while ($user = mysqli_fetch_object($users)) : ?>
            <tr align="center">
              <td class="d-md-none"><?= $user->userName ?> <?= $user->userLastName ?></td>
              <td class="d-none d-md-table-cell"><?= $user->userName ?></td>
              <td class="d-none d-md-table-cell"><?= $user->userLastName ?></td>
              <td class="d-none d-md-table-cell"><?= $user->userAddress ?></td>
              <td><?= $user->userCellphone ?></td>
              <td class="d-none d-md-table-cell"><?= $user->userEmail ?></td>
              <td>
                <form method="post">
                  <input type="submit" class="btn btn-primary" name="getIdUpdate" value="Editar">
                  <input type="hidden" value="<?= $user->userId ?>" name="userId">
                </form>
              </td>
              <td class="d-none d-md-table-cell">
                <form method="post">
                  <input type="submit" class="btn btn-danger" name="getIdDelete" value="Eliminar">
                  <input type="hidden" value="<?= $user->userId ?>" name="userId">
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

///<!-- addUser Modal -->
include('templates/addUser.php');

if ($updateUser_modal) :
  $updateUser_modal = false;
  ///<!-- editUser Modal -->
  include "templates/updateUser.php";
?>
  <script>
    $(function() {
      $('#editUser').modal('show');
    })
  </script>;

<?php endif;
if ($deleteUser_modal) :
  $deleteUser_modal = false;
  ////<!-- deleteUser Modal --> 
  include "templates/deleteUser.php";
?>
  <script>
    $(function() {
      $('#deleteUser').modal('show');
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