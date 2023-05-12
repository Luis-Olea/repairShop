<?php
if (!isset($_SESSION['logged'])) {
    header('location: ../clientMenu.php');
}
$client = $_SESSION['client'];
?>

<div class="modal fade modal-lg" id="editClient" tabindex="-1" aria-labelledby="clientEditModal" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="clientEditModal">Editar cliente</h1>
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
                                        <input class="form-control" value="<?= $client->clientName ?>" type="text" name="clientName" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Apellido(s):<span style='color:red'>*</span></label>
                                    <div class="col-sm">
                                        <input class="form-control" value="<?= $client->clientLastName ?>" type="text" name="clientLastName" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col">Dirección:</label>
                                    <div class="col-sm">
                                        <input class="form-control" value="<?= $client->clientAddress ?>" type="text" name="clientAddress">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Telefono:</label>
                                    <div class="col-sm">
                                        <input class="form-control" value="<?= $client->clientCellphone ?>" type="number" name="clientCellphone">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Correo:<span style='color:red'>*</span></label>
                                    <div class="col-sm">
                                        <input class="form-control" value="<?= $client->clientEmail ?>" type="email" name="clientEmail" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Limite de credito:</label>
                                    <div class="col-sm">
                                        <input placeholder="$" class="form-control" value="<?= $client->clientCreditLimit ?>" type="number" name="clientCreditLimit" step="100" min="0">
                                    </div>
                                </div>
                                <input type="hidden" value="<?= $client->clientId ?>" name="clientId">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" name="updateClient" value="Update">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>