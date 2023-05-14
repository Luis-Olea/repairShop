<?php
session_start();
if (!isset($_SESSION['logged'])) {
    header('location: index.php');
}

require('db/conection.php');

try {
    $sql = "SELECT * FROM clientreceipt ORDER BY cReceiptDate DESC LIMIT 1";
    $clientReceipts = $conn->query($sql);
} catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="stylesheet/bill.css" type="text/css">
</head>

<body>
    <div class="container">
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
                <div class="table table-bordered">
                    <br>
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th colspan="4" style="text-align:center;">
                                    <h1>CORAVI</h1>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="4">Fecha: <?= $clientReceipt->cReceiptDate ?></th>
                            </tr>
                            <tr>
                                <th colspan="4">
                                    <h3>CLIENTE</h3>
                                </th>
                            </tr>
                            <tr>
                                <th>Nombre: <?= $clientP->clientName ?> <?= $clientP->clientLastName ?></th>
                                <th>Direccion: <?= $clientP->clientAddress ?></th>
                                <th>Telefono: <?= $clientP->clientCellphone ?></th>
                                <th>Correo: <?= $clientP->clientEmail ?></th>
                            </tr>
                            <tr>
                                <th colspan="4">
                                    <h3>VENDEDOR</h3>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="2">Nombre: <?= $userP->userName ?> <?= $userP->userLastName ?></th>
                                <th>Telefono: <?= $userP->userCellphone ?></th>
                                <th>Correo: <?= $userP->userEmail ?></th>
                            </tr>
                            <tr>
                                <th colspan="4">
                                    <h3>PRODUCTOS</h3>
                                </th>
                            </tr>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio unitario</th>
                                <th>Subtotal</th>
                            </tr>
                            <?php
                            try {
                                $sql = "SELECT products.*, clientreceiptproducts.cReceiptPrice, clientreceiptproducts.cReceiptQuantity FROM clientreceiptproducts JOIN products ON clientreceiptproducts.cReceiptProductId = products.productId WHERE clientreceiptproducts.cReceiptId = '" . $clientReceipt->cReceiptId . "'";
                                $products = $conn->query($sql);
                                while ($product = mysqli_fetch_object($products)) :
                            ?>
                                    <tr align="center">
                                        <td><?= $product->productName ?></td>
                                        <td><?= $product->cReceiptQuantity ?></td>
                                        <td>$<?= $product->cReceiptPrice ?></td>
                                        <td>$<?= $product->cReceiptQuantity * $product->cReceiptPrice ?></td>
                                    </tr>
                            <?php endwhile;
                            } catch (Exception $e) {
                                $error_modal = true;
                                $errormsg = $e->getMessage();
                            }
                            ?>
                            <tr>
                                <th colspan="4">
                                    <h3>RESUMEN</h3>
                                </th>
                            </tr>
                            <tr>
                                <th>Total</th>
                                <th>Pago a credito</th>
                                <th>Pago con efectivo</th>
                                <th>Cambio</th>
                            </tr>
                            <tr>
                                <td>$<?= $clientReceipt->cReceiptTotal ?></td>
                                <td>$<?= $clientReceipt->cReceiptCreditAmount ?></td>
                                <td>$<?= $clientReceipt->cReceiptAmount ?></td>
                                <td>$<?= $clientReceipt->cReceiptChange ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endwhile; ?>
        <?php } catch (Exception $e) {
            $error_modal = true;
            $errormsg = $e->getMessage();
        } ?>
        <br>
    </div>
</body>

</html>