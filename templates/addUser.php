<div class="modal fade modal-lg" id="addUser" tabindex="-1" aria-labelledby="userAddModal" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="userAddModal">Agregar usuario</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form-horizontal" method="post">
                <div class="modal-body">
                    <div class="container bg-8 text-center">
                        <div class="panel panel-primary">
                            <div class="panel-body">
                                <div class="input-group mb-4">
                                    <span class="input-group-text">Nombres(s)<span class="red-text-modal">*</span></span>
                                    <input type="text" aria-label="First name" class="form-control" name="name" required>
                                </div>
                                <div class="input-group mb-4">
                                    <span class="input-group-text">Apellido(s)<span class="red-text-modal">*</span></span>
                                    <input type="text" aria-label="Last name" class="form-control" name="last-name" required>
                                </div>
                                <div class="input-group mb-4">
                                    <span class="input-group-text">Dirección</span>
                                    <textarea class="form-control" type="text" name="address" rows="2"></textarea>
                                </div>
                                <div class="input-group mb-4">
                                    <span class="input-group-text">Celular<span class="red-text-modal">*</span></span>
                                    <input class="form-control" type="number" name="mobileno">
                                </div>
                                <div class="input-group mb-4">
                                    <span class="input-group-text">Correo<span class="red-text-modal">*</span></span>
                                    <input class="form-control" type="email" name="email" required>
                                </div>
                                <div class="input-group mb-4">
                                    <label class="input-group-text" for="roles">Rol<span class="red-text-modal">*</span></label>
                                    <select class="form-select" id="roles" name="userRoleId" required>
                                        <option selected>Seleccione un rol...</option>
                                        <option value="1">Administrador</option>
                                        <option value="2">Vendedor</option>
                                        <option value="3">Almacen</option>
                                    </select>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">Contraseña<span class="red-text-modal">*</span></span>
                                    <input class="form-control" autocomplete="off" type="password" name="password" required>
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" name="addUser" value="Add">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>