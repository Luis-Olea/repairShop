<?php
if ($_SESSION['roleId'] != 1) {
    header('Location: secundaryDashboard.php');
    exit;
}

require('db/conection.php');

try {
    $sql = "SELECT * FROM clientreceipts WHERE cReceiptId = '" . $_SESSION['cReceiptId'] . "'";
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
                    <table>
                        <tbody>
                            <tr>
                                <th colspan="4" style="text-align:center;">
                                    <h1><?= $_SESSION['storeName'] ?></h1>
                                </th>
                            </tr>
                            <tr>
                                <td colspan="2">Nota: N<?= $_SESSION['cReceiptId'] ?></td>
                                <td colspan="2">Fecha: <?= date("H:i:s - d/m/Y", strtotime($clientReceipt->cReceiptDate)) ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <table>
                        <tbody>
                            <tr>
                                <th colspan="4">
                                    <h3>CLIENTE</h3>
                                </th>
                            </tr>
                            <tr>
                                <td>Nombre: <?= $clientP->clientName ?> <?= $clientP->clientLastName ?></td>
                                <td>Direccion: <?= $clientP->clientAddress ?></td>
                                <td>Telefono: <?= $clientP->clientCellphone ?></td>
                                <td>Correo: <?= $clientP->clientEmail ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <table>
                        <tbody>
                            <tr>
                                <th colspan="4">
                                    <h3>VENDEDOR</h3>
                                </th>
                            </tr>
                            <tr>
                                <td colspan="2">Nombre: <?= $userP->userName ?> <?= $userP->userLastName ?></td>
                                <td>Telefono: <?= $userP->userCellphone ?></td>
                                <td>Correo: <?= $userP->userEmail ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <table>
                        <tbody>
                            <tr>
                                <th colspan="4">
                                    <h3>PRODUCTOS</h3>
                                </th>
                            </tr>
                            <tr>
                                <th>Nombre</th>
                                <th>Cantidad</th>
                                <th>Precio unitario</th>
                                <th>Subtotal</th>
                            </tr>
                            <?php
                            try {
                                $sql = "SELECT products.*, clientreceiptproducts.cReceiptPrice, clientreceiptproducts.cReceiptQuantity FROM clientreceiptproducts JOIN products ON clientreceiptproducts.cReceiptProductId = products.productId WHERE clientreceiptproducts.cReceipReceiptId = '" . $clientReceipt->cReceiptId . "'";
                                $products = $conn->query($sql);
                                while ($product = mysqli_fetch_object($products)) :
                            ?>
                                    <tr align="center">
                                        <td><?= $product->productName ?></td>
                                        <td><?= number_format($product->cReceiptQuantity) ?></td>
                                        <td>$<?= number_format($product->cReceiptPrice, 2) ?></td>
                                        <td>$<?= number_format($product->cReceiptQuantity * $product->cReceiptPrice, 2) ?></td>
                                    </tr>
                            <?php endwhile;
                            } catch (Exception $e) {
                                $error_modal = true;
                                $errormsg = $e->getMessage();
                            }
                            ?>
                        </tbody>
                    </table>
                    <table>
                        <tbody>
                            <tr>
                                <th colspan="4">
                                    <h3>RESUMEN</h3>
                                </th>
                            </tr>
                            <tr>
                                <th>Cambio</th>
                                <th>Pago con efectivo</th>
                                <th>Pago a credito</th>
                                <th>Total</th>
                            </tr>
                            <tr>
                                <td>$<?= number_format($clientReceipt->cReceiptChange, 2) ?></td>
                                <td>$<?= number_format($clientReceipt->cReceiptAmount, 2) ?></td>
                                <td>$<?= number_format($clientReceipt->cReceiptCreditAmount, 2) ?></td>
                                <td>$<?= number_format($clientReceipt->cReceiptTotal, 2) ?></td>
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