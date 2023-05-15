<?php
session_start();
if (!isset($_SESSION['logged'])) {
  header('location: db/exit.php');
  exit;
}

if ($_SESSION['roleId'] != 1) {
  header('Location: secundaryDashboard.php');
  exit;
}

date_default_timezone_set('America/Mexico_City');

require('db/conection.php');

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

$repply = getPreferencesConf($conn);
if ($repply == false) {
} else {
  $store = $repply;
}

$days = $store['daysNotification'];
$minDue = $store['quantityNotification'];

try {
    $sql = "SELECT clients.clientId, clients.clientName, clients.clientLastName, clients.clientDue
    FROM clients
    LEFT JOIN (
        SELECT cReceiptClientId, MAX(cReceiptDate) AS max_date
        FROM clientreceipts
        GROUP BY cReceiptClientId
    ) AS receipts
    ON clients.clientId = receipts.cReceiptClientId
    WHERE clients.clientDue >= '" . $minDue . "' AND DATEDIFF(CURDATE(), receipts.max_date) > '" . $days . "'";    
    $clients = $conn->query($sql);
} catch (Exception $e) {
    $error = $e;
}

//<!-- HTML HEADER -->
include "templates/header.php";
include('layouts/sidebar.php'); ?>

<div class="base-users">
    <div class="container-bottom">
        <h3 class="text-center">Notificaciones</h3>
    </div>
    <?php
    $clientCount = 0;
    try { ?>
        <?php while ($client = mysqli_fetch_object($clients)) : 
            $clientCount += 1?>
                <div class="alert-bell">
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill=#b70000 class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                        </svg>
                        &nbsp&nbsp&nbsp
                        <div class="text-notifications">
                            El cliente <strong><?= $client->clientName . ' ' . $client->clientLastName?></strong> lleva <strong><?= $days ?></strong> dias sin dar ningun abono, su deuda actual es de <strong>$<?= number_format($client->clientDue, 2) ?></strong>.
                        </div>
                    </div>
                </div>
        <?php endwhile; ?>
    <?php } catch (Exception $e) {
        echo $e->getMessage();
    }
    if ($clientCount == 0) : ?>
        <div class="alert-bell">
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="#008f39" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                </svg>
                &nbsp&nbsp&nbsp
                <div class="text-notifications">
                    No tienes ninguna notificacion, todos los clientes estan al corriente con sus pagos.
                </div>
            </div>
        </div>
    <?php endif ?>
</div>

<!-- HTML END BODY -->
<?php include "templates/footer.php"; ?>