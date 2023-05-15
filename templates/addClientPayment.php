<?php
if (!isset($_SESSION['logged'])) {
    header('location: ../clientPayment.php');
}
?>

<div class="modal fade modal-xl" id="addClientPayment" tabindex="-1" aria-labelledby="addClientPaymentModal" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addClientPaymentModal">Registrar un abono</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="clientName">Cliente</label>
                        <select class="form-select" id="clientName" name="clientId" required>
                            <option selected>Selecciona un cliente...</option>
                            <?php
                            try {
                                $stmt = $conn->prepare("SELECT clientId, clientName, clientLastName FROM clients WHERE clientDue > 0");
                                $stmt->execute();
                                $clients = $stmt->get_result();
                                while ($client = $clients->fetch_object()) : ?>
                                    <option value="<?= htmlspecialchars($client->clientId) ?>"><?= htmlspecialchars($client->clientName) ?> <?= htmlspecialchars($client->clientLastName) ?></option>
                            <?php endwhile;
                            } catch (Exception $e) {
                                $error_modal = true;
                                $errormsg = $e->getMessage();
                            }
                            ?>
                        </select>
                    </div>
                    <div class="clientInfo">
                        <?php if (isset($_SESSION['cartClient'])) :
                            // Obtener información del producto de la base de datos
                            $stmt = $conn->prepare("SELECT * FROM clients WHERE clientId = ?");
                            $stmt->bind_param("i", $_SESSION['cartClient']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            if ($result->num_rows > 0) :
                                $client = $result->fetch_object();
                                $_SESSION['clientDue'] = $client->clientDue;
                        ?>
                                <div class="card">
                                    <h5 class="card-header">Cliente</h5>
                                    <div class="card-body">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="basic-addon1">Nombre</span>
                                            <span class="form-control"><?= $client->clientName ?> <?= $client->clientLastName ?></span>
                                        </div>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="basic-addon1">Limite de credito</span>
                                            <span class="form-control">$<?= number_format($client->clientCreditLimit, 2) ?></span>
                                        </div>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="basic-addon1">Deuda</span>
                                            <span class="form-control">$<?= number_format($client->clientDue, 2) ?></span>
                                        </div>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="basic-addon1">Credito disponible</span>
                                            <span class="form-control">$<?= number_format($client->clientCreditLimit - $client->clientDue, 2) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="changeInfo">
                        <?php if (isset($_SESSION['cartClient'])) : ?>

                        <div class="form-group" style="margin-top: 15px;">
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Cantidad a abonar</span>
                                <input type="number" class="form-control" id="creditToUseId" name="creditToUse" step="100" min="0" value="<?= $_SESSION['creditToUse'] ?>"/>
                            </div>
                            <?php if ($_SESSION['creditToUse'] - $_SESSION['actualQuantity']  > 0) : ?>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">Faltante</span>
                                    <span class="form-control">$<?= number_format($_SESSION['creditToUse'] - $_SESSION['actualQuantity'], 2) ?></span>
                                </div>
                            <?php else : ?>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">Cambio</span>
                                    <span class="form-control">$<?= number_format(($_SESSION['creditToUse'] - $_SESSION['actualQuantity']) - 2 * ($_SESSION['creditToUse'] - $_SESSION['actualQuantity']), 2) ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Pago</span>
                                <input type="number" class="form-control" id="inputMoneyId" name="inputMoney" step="100" min="0" value="<?= $_SESSION['actualQuantity'] ?>" />
                            </div>
                        </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer-cart2">
                <form class="form-horizontal" method="post">
                    <div class="modal-footer">
                        <?php if (isset($_SESSION['cartClient'])) : ?>
                            <?php if ($_SESSION['creditToUse'] - $_SESSION['actualQuantity'] <= 0) : ?>
                                <button type="submit" class="btn btn-success" name="finishShopping">Agregar abono</button>
                            <?php else : ?>
                                <a href="#" class="btn btn-success disabled" tabindex="-1" role="button" aria-disabled="true">Agregar abono</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>