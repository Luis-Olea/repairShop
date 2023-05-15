<?php
if (!isset($_SESSION['logged'])) {
    header('location: ../suppPayments.php');
}
?>

<div class="modal fade modal-xl" id="addSuppPayment" tabindex="-1" aria-labelledby="addSuppPaymentModal" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addSuppPaymentModal">Nuevo pedido</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container bg-8 text-center">
                    <div class="panel panel-primary">
                        <div class="panel-body">
                            <form method="post" class="sendAddPayment">
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <label class="input-group-text" for="suppliersNames">Proveedor</label>
                                        <select class="form-select" id="suppliersNames" name="productSupplier" required>
                                        <option selected>Selecciona un proveedor...</option>
                                            <?php
                                            $suppliers = $conn->query("SELECT * FROM suppliers");
                                            try { ?>
                                                <?php while ($supplier = mysqli_fetch_object($suppliers)) : ?>
                                                    <option value="<?= $supplier->supplierId ?>"><?= $supplier->supplierName ?> <?= $supplier->supplierLastName ?></option>
                                                <?php endwhile; ?>
                                            <?php } catch (Exception $e) {
                                                $error_modal = true;
                                                $errormsg = $e->getMessage();
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                            </form>
                            <form method="post" class="addProductSuppCart">
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <label class="input-group-text" for="productsList">Producto</label>
                                        <select class="form-select" id="productsList" name="productId" required>
                                            <option selected>Selecciona un producto...</option>
                                            <?php
                                            $products = $conn->query("SELECT * FROM products WHERE productSupplier = '".$_SESSION['currentSupplierCart']."'");
                                            try { ?>
                                                <?php while ($product = mysqli_fetch_object($products)) : ?>
                                                    <option value="<?= $product->productId ?>"><?= $product->productName ?></option>
                                                <?php endwhile; ?>
                                            <?php } catch (Exception $e) {
                                                $error_modal = true;
                                                $errormsg = $e->getMessage();
                                            } ?>
                                        </select>
                                        <button class="btn btn-success" type="submit" name="add_Product_Supp_Cart">Agregar</button>
                                    </div>
                                </div>
                            </form>
                            <div class="tableOrderSupp">
                                <div class="table table-responsive">
                                    <?php
                                    $total = 0;
                                    $id = 0;
                                    if (isset($_SESSION['cartS'])) : ?>
                                        <table class='table table-bordered'>
                                            <thead class='table-light'>
                                                <tr align='center' style='vertical-align:middle;'>
                                                    <th>N°</th>
                                                    <th>Producto</th>
                                                    <th>Cantidad</th>
                                                    <th>Quitar</th>
                                                    <th>Precio compra</th>
                                                    <th>Precio venta</th>
                                                    <th>Subtotal</th>
                                                </tr>
                                                <?php foreach ($_SESSION['cartS'] as $product_id => $item) :
                                                    $quantity = $item['quantity'];
                                                    $purchasePrice = $item['price'];
                                                    // Obtener información del producto de la base de datos
                                                    $stmt = $conn->prepare("SELECT * FROM products WHERE productId = ?");
                                                    $stmt->bind_param("i", $product_id);
                                                    $stmt->execute();
                                                    $result = $stmt->get_result();
                                                    if ($result->num_rows > 0) :
                                                        $product = $result->fetch_object();
                                                        // Calcular el subtotal y agregarlo al total
                                                        $subtotal = $purchasePrice * $quantity;
                                                        $total += $subtotal;
                                                        $id += 1; ?>
                                                        <tr align="center" style="vertical-align:middle;">
                                                            <td><?= $id ?></td>
                                                            <td><?= $product->productName ?></td>
                                                            <td>
                                                                <div class="changeProductCart">
                                                                    <input type="hidden" name="productId" value="<?= $product->productId ?>">
                                                                    <input type="number" min="1" class="inputCartQuantity" value="<?= $quantity ?>">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <form method="post" class="delete_from_cart">
                                                                    <input type="hidden" name="productId" value="<?= $product->productId ?>">
                                                                    <button type="submit" name="delete_from_cart" class="btn btn-danger btn-sm">X</button>
                                                                </form>
                                                            </td>
                                                            <td>
                                                                <div class="changeProductCart2">
                                                                    <input type="hidden" name="productId" value="<?= $product->productId ?>">
                                                                    <input type="number" class="inputPurchasePrice" value="<?= $purchasePrice ?>">
                                                                </div>
                                                            </td>
                                                            <td>$<?= number_format($product->productPrice, 2)?></td>
                                                            <td>$<?= number_format($subtotal, 2) ?></td>
                                                        </tr>
                                                <?php endif;
                                                endforeach
                                                ?>
                                                <tr style='vertical-align:middle;'>
                                                    <?php $_SESSION['totalPaymentSupp'] = $total ?>
                                                    <td colspan='6'>Total</td>
                                                    <td align='center'>$<?= number_format($total, 2) ?></td>
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
                        </div>
                    </div>
                </div>
            </div>
            <form method="post">
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" name="addSuppPayments" value="Add">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>