<?php
if (!isset($_SESSION['logged'])) {
    header('location: ../supplierMenu.php');
}
?>

<div class="modal fade modal-lg" id="addSupplier" tabindex="-1" aria-labelledby="supplierAddModal" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="supplierAddModal">Agregar proveedor</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form-horizontal" method="post">
                <div class="modal-body">
                    <div class="container bg-8 text-center">
                        <div class="panel panel-primary">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="control-label col-sm">Nombre(s):<span class="red-text-modal">*</span></label>
                                    <div class="col-sm">
                                        <input class="form-control" type="text" name="name" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Apellido(s):<span class="red-text-modal">*</span></label>
                                    <div class="col-sm">
                                        <input class="form-control" type="text" name="last-name" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Dirección:</label>
                                    <div class="col-sm">
                                        <textarea class="form-control" type="text" name="address" rows="2"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Telefono:<span class="red-text-modal">*</span></label>
                                    <div class="col-sm">
                                        <input class="form-control" type="number" name="mobileno" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Correo:</label>
                                    <div class="col-sm">
                                        <input class="form-control" type="email" name="email">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Compañia:<span class="red-text-modal">*</span></label>
                                    <div class="col-sm">
                                        <input class="form-control" type="text" name="brand" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" name="addSupplier" value="Add">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>