<?php
if (!isset($_SESSION['logged'])) {
    header('location: ../supplierPayment.php');
}
?>

<div class="modal fade modal-xl" id="addSupplierPayment" tabindex="-1" aria-labelledby="addSupplierPaymentModal" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addSupplierPaymentModal">Registrar un abono</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="supplierName">Proveedor</label>
                        <select class="form-select" id="supplierName" name="supplierId" required>
                            <option selected>Selecciona un proveedor...</option>
                            <?php
                            try {
                                $stmt = $conn->prepare("SELECT supplierId, supplierName, supplierLastName FROM suppliers WHERE supplierDue > 0");
                                $stmt->execute();
                                $suppliers = $stmt->get_result();
                                while ($supplier = $suppliers->fetch_object()) : ?>
                                    <option value="<?= htmlspecialchars($supplier->supplierId) ?>"><?= htmlspecialchars($supplier->supplierName) ?> <?= htmlspecialchars($supplier->supplierLastName) ?></option>
                            <?php endwhile;
                            } catch (Exception $e) {
                                $error_modal = true;
                                $errormsg = $e->getMessage();
                            }
                            ?>
                        </select>
                    </div>
                    <div class="supplierInfo">
                        <?php if (isset($_SESSION['cartSupplier'])) :
                            // Obtener información del producto de la base de datos
                            $stmt = $conn->prepare("SELECT * FROM suppliers WHERE supplierId = ?");
                            $stmt->bind_param("i", $_SESSION['cartSupplier']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            if ($result->num_rows > 0) :
                                $supplier = $result->fetch_object();
                                $_SESSION['supplierDue'] = $supplier->supplierDue;
                        ?>
                                <div class="card">
                                    <h5 class="card-header">Proveedor</h5>
                                    <div class="card-body">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="basic-addon1">Nombre</span>
                                            <span class="form-control"><?= $supplier->supplierName ?> <?= $supplier->supplierLastName ?></span>
                                        </div>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="basic-addon1">Deuda</span>
                                            <span class="form-control">$<?= number_format($supplier->supplierDue, 2) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="paymentInfo">
                        <?php if (isset($_SESSION['cartSupplier'])) : ?>
                            <div class="form-group" style="margin-top: 15px;">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">Cantidad a abonar</span>
                                    <input type="number" class="form-control" id="creditToPayId" name="creditToPay" step="100" min="0" value="<?= $_SESSION['creditToPay'] ?>"/>
                                </div>
                                <?php if ($_SESSION['creditToPay'] - $_SESSION['actualQuantity']  > 0) : ?>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1">Faltante</span>
                                        <span class="form-control">$<?= number_format($_SESSION['creditToPay'] - $_SESSION['actualQuantity'], 2) ?></span>
                                    </div>
                                <?php else : ?>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1">Cambio</span>
                                        <span class="form-control">$<?= number_format(($_SESSION['creditToPay'] - $_SESSION['actualQuantity']) - 2 * ($_SESSION['creditToPay'] - $_SESSION['actualQuantity']), 2) ?></span>
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
                        <?php if (isset($_SESSION['cartSupplier'])) : ?>
                            <?php if ($_SESSION['creditToPay'] - $_SESSION['actualQuantity'] <= 0) : ?>
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