<?php
if (!isset($_SESSION['logged'])) {
    header('location: ../clientMenu.php');
}
?>

<div class="modal fade modal-lg" id="addClient" tabindex="-1" aria-labelledby="clientAddModal" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="clientAddModal">Agregar cliente</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form-horizontal" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="container bg-8 text-center">
                        <div class="panel panel-primary">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="control-label col-sm">Nombre(s):<span class="red-text-modal">*</span></label>
                                    <div class="col-sm">
                                        <input class="form-control" type="name" name="clientName" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Apellido(s):<span class="red-text-modal">*</span></label>
                                    <div class="col-sm">
                                        <input class="form-control" type="last-name" name="clientLastName" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Dirección:</label>
                                    <div class="col-sm">
                                        <textarea class="form-control" type="address" name="clientAddress" rows="2"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Telefono:<span class="red-text-modal">*</span></label>
                                    <div class="col-sm">
                                        <input class="form-control" type="number" name="clientCellphone" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Correo:</label>
                                    <div class="col-sm">
                                        <input class="form-control" type="email" name="clientEmail">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">NIP:<span class="red-text-modal">*</span></label>
                                    <div class="col-sm">
                                        <input class="form-control" placeholder="Solo numeros del 0 al 9" type="password" pattern="[0-9]*" inputmode="numeric" name="clientNIP" minlength="4" maxlength="4" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" name="addClient" value="Add">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>