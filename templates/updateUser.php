<?php
if (!isset($_SESSION['logged'])) {
    header('location: ../userMenu.php');
}
$user = $_SESSION['user'];
?>

<div class="modal fade modal-lg" id="editUser" tabindex="-1" aria-labelledby="userEditModal" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="userEditModal">Editar usuario</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form-horizontal" method="post">
                <div class="modal-body">
                    <div class="container bg-8 text-center">
                        <div class="panel panel-primary">
                            <div class="panel-body">
                                <div class="input-group mb-4">
                                    <span class="input-group-text">Nombres(s)<span class="red-text-modal">*</span></span>
                                    <input class="form-control" value="<?= $user->userName ?>" type="text" name="userName" required>
                                </div>
                                <div class="input-group mb-4">
                                    <span class="input-group-text">Apellido(s)<span class="red-text-modal">*</span></span>
                                    <input class="form-control" value="<?= $user->userLastName ?>" type="text" name="userLastName" required>
                                </div>
                                <div class="input-group mb-4">
                                    <span class="input-group-text">Dirección</span>
                                    <textarea class="form-control" type="text" name="userAdress"><?= $user->userAddress ?></textarea>
                                </div>
                                <div class="input-group mb-4">
                                    <span class="input-group-text">Celular<span class="red-text-modal">*</span></span>
                                    <input class="form-control" value="<?= $user->userCellphone ?>" type="number" name="userCellphone">
                                </div>
                                <div class="input-group mb">
                                    <span class="input-group-text">Correo<span class="red-text-modal">*</span></span>
                                    <input class="form-control" value="<?= $user->userEmail ?>" type="email" name="userEmail" required>
                                    <input type="hidden" value="<?= $user->userId ?>" name="userId">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" name="updateUser" value="Update">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>