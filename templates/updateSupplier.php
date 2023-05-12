<?php
if (!isset($_SESSION['logged'])) {
    header('location: ../supplierMenu.php');
}
$supplier = $_SESSION['supplier'];
?>

<div class="modal fade modal-lg" id="editSupplier" tabindex="-1" aria-labelledby="supplierEditModal" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="supplierEditModal">Editar proveedor</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form-horizontal" method="post">
                <div class="modal-body">
                    <div class="container bg-8 text-center">
                        <div class="panel panel-primary">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="control-label col-sm">Nombre(s):<span style='color:red'>*</span></label>
                                    <div class="col-sm">
                                        <input class="form-control" value="<?= $supplier->supplierName ?>" type="text" name="supplierName" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Apellido(s):<span style='color:red'>*</span></label>
                                    <div class="col-sm">
                                        <input class="form-control" value="<?= $supplier->supplierLastName ?>" type="text" name="supplierLastName" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col">Dirección:</label>
                                    <div class="col-sm">
                                        <input class="form-control" value="<?= $supplier->supplierAddress ?>" type="address" name="supplierAddress">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Telefono:<span style='color:red'>*</span></label>
                                    <div class="col-sm">
                                        <input class="form-control" value="<?= $supplier->supplierCellphone ?>" type="number" name="supplierCellphone" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Correo:</label>
                                    <div class="col-sm">
                                        <input class="form-control" value="<?= $supplier->supplierEmail ?>" type="email" name="supplierEmail">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Compañia:</label>
                                    <div class="col-sm">
                                        <input class="form-control" value="<?= $supplier->supplierBrand ?>" type="brand" name="supplierBrand" required>
                                    </div>
                                    <input type="hidden" value="<?= $supplier->supplierId ?>" name="supplierId">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" name="updateSupplier" value="Update">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>