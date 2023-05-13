<?php
session_start();
if (!isset($_SESSION['logged'])) {
    header('location: index.php');
}

require('db/conection.php');

try {
    $sql = "SELECT * FROM clientReceipt ORDER BY cReceiptDate DESC LIMIT 1";
    $clientReceipts = $conn->query($sql);
} catch (Exception $e) {
    $error_modal = true;
    $errormsg = $e->getMessage();
}
?>

<!DOCTYPE html>

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
                <div class="table-container">
                    <br>
                    <table>
                        <thead>
                            <th colspan="2" style="text-align:center;">
                                <h1>CORAVI</h1>
                            </th>
                        </thead>
                        <thead>
                            <th>Cliente: <?= $clientP->clientName ?> <?= $clientP->clientLastName ?></th>
                            <th>Fecha: <?= $clientReceipt->cReceiptDate ?></th>
                        </thead>
                    </table>
                    <br>
                    <table>
                        <thead>
                            <tr align="center" class="active">
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Credito</th>
                                <th>Efectivo</th>
                                <th>Cambio</th>
                                <th>Cliente</th>
                                <th>Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr align="center">
                                <td><?= $clientReceipt->cReceiptDate ?></td>
                                <td>$<?= $clientReceipt->cReceiptTotal ?></td>
                                <td>$<?= $clientReceipt->cReceiptCreditAmount ?></td>
                                <td>$<?= $clientReceipt->cReceiptAmount ?></td>
                                <td>$<?= $clientReceipt->cReceiptChange ?></td>
                                <td><?= $clientP->clientName ?> <?= $clientP->clientLastName ?></td>
                                <td><?= $userP->userName ?> <?= $userP->userLastName ?></td>
                            </tr>

                            <tr align="center" class="active">
                                <th colspan="4">Producto</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Subtotal</th>
                            </tr>
                            <?php
                            try {
                                $sql = "SELECT products.*, clientreceiptproducts.cReceiptPrice, clientreceiptproducts.cReceiptQuantity FROM clientreceiptproducts JOIN products ON clientreceiptproducts.cReceiptProductId = products.productId WHERE clientreceiptproducts.cReceiptId = '" . $clientReceipt->cReceiptId . "'";
                                $products = $conn->query($sql);
                                while ($product = mysqli_fetch_object($products)) :
                            ?>
                                    <tr align="center">
                                        <td colspan="4"><?= $product->productName ?></td>
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