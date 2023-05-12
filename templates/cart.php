<?php
if (!isset($_SESSION['logged'])) {
    header('location: ../pos.php');
}
?>

<div class="modal fade modal-xl" id="cart" tabindex="-1" aria-labelledby="cartModal" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="cartModal">Carrito</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="cart1">
                <div class="modal-body">
                    <div class="table table-responsive">
                        <?php
                        $total = 0;
                        $id = 0;
                        if (isset($_SESSION['cart'])) : ?>
                            <table class='table table-bordered'>
                                <thead class='table-light'>
                                    <tr align='center' style='vertical-align:middle;'>
                                        <th>N°</th>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Quitar</th>
                                        <th>Precio</th>
                                        <th>Subtotal</th>
                                    </tr>
                                    <?php foreach ($_SESSION['cart'] as $product_id => $quantity) :
                                        // Obtener información del producto de la base de datos
                                        $stmt = $conn->prepare("SELECT * FROM products WHERE productId = ?");
                                        $stmt->bind_param("i", $product_id);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        if ($result->num_rows > 0) :
                                            $product = $result->fetch_object();
                                            // Calcular el subtotal y agregarlo al total
                                            $subtotal = $product->productPrice * $quantity;
                                            $total += $subtotal;
                                            $id += 1; ?>
                                            <tr align="center" style="vertical-align:middle;">
                                                <td><?= $id ?></td>
                                                <td><?= $product->productName ?></td>
                                                <td style="min-width:170px;">
                                                    <form method="post" class="deleteProductCart">
                                                        <input type="hidden" name="productId" value="<?= $product->productId ?>">
                                                        <button type="submit" name="remove_from_cart" class="btn btn-danger btn-sm">-</button>
                                                    </form>
                                                    <div class="changeProductCart">
                                                        <input type="hidden" name="productId" value="<?= $product->productId ?>">
                                                        <input type="number" min="1" class="inputCartQuantity" value="<?= $quantity ?>">
                                                    </div>
                                                    <form method="post" class="addOneProductCart">
                                                        <input type="hidden" name="productId" value="<?= $product->productId ?>">
                                                        <button type="submit" name="add_from_cart" class="btn btn-success btn-sm">+</button>
                                                    </form>
                                                </td>
                                                <td>
                                                    <form method="post" class="delete_from_cart">
                                                        <input type="hidden" name="productId" value="<?= $product->productId ?>">
                                                        <button type="submit" name="delete_from_cart" class="btn btn-danger btn-sm">X</button>
                                                    </form>
                                                </td>
                                                <td><?= $product->productPrice ?></td>
                                                <td><?= $subtotal ?></td>
                                            </tr>
                                    <?php endif;
                                    endforeach
                                    ?>
                                    <tr style='vertical-align:middle;'>
                                        <td colspan='5'>Total</td>
                                        <td align='center'><?= $total ?></td>
                                        <?php $_SESSION['totalCart'] = $total; ?>
                                    </tr>
                                </thead>
                            </table>
                        <?php else : ?>
                            <div class="container-emptyCart">
                                <svg xmlns="http://www.w3.org/2000/svg" width="155" height="155" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
                                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                                </svg>
                                <h4><br>El carrito está vacío.</h4>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                    <?php if (isset($_SESSION['cart'])) : ?>
                        <button type="submit" class="btn btn-success" data-bs-target="#cart2" data-bs-toggle="modal">Siguente</button>
                    <?php else : ?>
                        <a href="#" class="btn btn-success disabled" tabindex="-1" role="button" aria-disabled="true">Siguente</a>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-xl" id="cart2" tabindex="-1" aria-labelledby="cartModal2" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="cartModal2">Finalizar venta</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="clientName">Cliente</label>
                        <select class="form-select" id="clientName" name="clientId" required>
                            <option selected>Selecciona un cliente...</option>
                            <?php
                            $clients = $conn->query("SELECT * FROM clients");
                            try { ?>
                                <?php while ($client = mysqli_fetch_object($clients)) : ?>
                                    <option value="<?= $client->clientId ?>"><?= $client->clientName ?> <?= $client->clientLastName ?></option>
                                <?php endwhile; ?>
                            <?php } catch (Exception $e) {
                                $error_modal = true;
                                $errormsg = $e->getMessage();
                            } ?>
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
                        ?>
                                <div class="card">
                                    <h5 class="card-header">Cliente</h5>
                                    <div class="card-body">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="basic-addon1">Nombre</span>
                                            <span class="form-control"><?= $client->clientName ?> <?= $client->clientLastName ?></span>
                                        </div>
                                        <?php if ($client->clientCreditLimit > 0) : ?>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text" id="basic-addon1">Limite de credito</span>
                                                <span class="form-control">$<?= $client->clientCreditLimit ?></span>
                                            </div>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text" id="basic-addon1">Deuda</span>
                                                <span class="form-control">$<?= $client->clientDue ?></span>
                                            </div>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text" id="basic-addon1">Credito disponible</span>
                                                <span class="form-control">$<?= $client->clientCreditLimit - $client->clientDue ?></span>
                                            </div>
                                        <?php
                                            $_SESSION['creditAvailable'] = $client->clientCreditLimit - $client->clientDue;
                                        endif;
                                        ?>
                                    </div>
                                </div>
                                <div class="changeInfo">
                                    <div class="form-group" style="margin-top: 15px;">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="basic-addon1">Total a pagar</span>
                                            <span class="form-control">$<?= $_SESSION['totalCart'] ?></span>
                                        </div>
                                        <?php if ($_SESSION['creditAvailable'] > 0) : ?>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text" id="basic-addon1">Pago a credito</span>
                                                <input type="number" class="form-control" id="creditToUseId" name="creditToUse" step="100" min="0" value="<?= $_SESSION['creditToUse'] ?>" />
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($_SESSION['totalCart'] - $_SESSION['actualQuantity'] - $_SESSION['creditToUse'] > 0) : ?>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text" id="basic-addon1">Faltante</span>
                                                <span class="form-control">$<?= $_SESSION['totalCart'] - $_SESSION['actualQuantity'] - $_SESSION['creditToUse'] ?></span>
                                            </div>
                                        <?php else : ?>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text" id="basic-addon1">Cambio</span>
                                                <span class="form-control">$<?= ($_SESSION['totalCart'] - $_SESSION['actualQuantity'] - $_SESSION['creditToUse']) - 2 * ($_SESSION['totalCart'] - $_SESSION['actualQuantity'] - $_SESSION['creditToUse']) ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">Pago</span>
                                            <input type="number" class="form-control" id="inputMoneyId" name="inputMoney" step="100" min="0" value="<?= $_SESSION['actualQuantity'] ?>" />
                                        </div>
                                        <?php if ($_SESSION['creditToUse'] > 0 and $_SESSION['totalCart'] - $_SESSION['actualQuantity'] - $_SESSION['creditToUse'] <= 0) : ?>
                                            <div class="card">
                                                <h5 class="card-header">Venta con credito</h5>
                                                <div class="card-body">
                                                    <div class="input-group mb-3">
                                                        <span class="input-group-text" id="basic-addon1">Nombre</span>
                                                        <span class="form-control"><?= $client->clientName ?> <?= $client->clientLastName ?></span>
                                                    </div>
                                                    <div class="input-group mb-3">
                                                        <span class="input-group-text" id="basic-addon1">Credito a utilizar</span>
                                                        <span class="form-control">$<?= $_SESSION['creditToUse'] ?></span>
                                                    </div>
                                                    <h5>Validar venta</h5>
                                                    <form method="post" class="validateNIP">
                                                        <div class="input-group mb-3">
                                                            <span class="input-group-text" id="basic-addon1">Ingrese su NIP</span>
                                                            <input class="form-control" placeholder="Ingrese su NIP de 4 digitos" type="password" pattern="[0-9]*" inputmode="numeric" name="clientNIP" minlength="4" maxlength="4" required>
                                                            <button class="btn btn-success" type="submit" id="button-addon2">Validar</button>
                                                        </div>
                                                    </form>
                                                    <div class="repplyNIPM" style="text-align: center;">
                                                        <?php if (isset($_SESSION['repplyNIP'])) : ?>
                                                            <h3>NIP incorrecto</h3>
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" fill="red" class="bi bi-x-circle" viewBox="0 0 16 16">
                                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                                                            </svg>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer-cart2">
                <form class="form-horizontal" method="post">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cart">Anterior</button>
                        <?php if (isset($_SESSION['actualQuantity'])) : ?>
                            <?php if ($_SESSION['creditToUse'] <= 0 and $_SESSION['totalCart'] - $_SESSION['actualQuantity'] - $_SESSION['creditToUse'] <= 0) : ?>
                                <button type="submit" class="btn btn-success" name="addClient">Finalizar venta</button>
                            <?php else : ?>
                                <a href="#" class="btn btn-success disabled" tabindex="-1" role="button" aria-disabled="true">Finalizar venta</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>