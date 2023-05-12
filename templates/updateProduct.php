<?php
if (!isset($_SESSION['logged'])) {
    header('location: ../prodcutMenu.php');
}
$product = $_SESSION['product'];
?>

<div class="modal fade modal-lg" id="editProduct" tabindex="-1" aria-labelledby="editProductModal" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editProductModal">Editar producto</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form-horizontal" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="container bg-8 text-center">
                        <div class="panel panel-primary">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="control-label col-sm">Nombre:<span class="red-text-modal">*</span></label>
                                    <div class="col-sm">
                                        <input class="form-control" type="text" name="productName" value="<?= $product->productName ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Marca:<span class="red-text-modal">*</span></label>
                                    <div class="col-sm">
                                        <input class="form-control" type="text" name="productBrand" value="<?= $product->productBrand ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Descripción:</label>
                                    <div class="col-sm">
                                        <textarea class="form-control" type="text" name="productDescription" rows="3"><?= $product->productDescription ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Categoría:<span class="red-text-modal">*</span></label>
                                    <div class="col-sm">
                                        <input class="form-control" type="text" name="productCategory" autocomplete="off" list="categorys" value="<?= $product->productCategory ?>" required>
                                        <datalist id="categorys">
                                            <?php
                                            $sql = "SELECT * FROM products";
                                            $productsL = $conn->query($sql);
                                            try { ?>
                                                <?php while ($productL = mysqli_fetch_object($products)) : ?>
                                                    <option value="<?= $productL->productCategory ?>"></option>
                                                <?php endwhile; ?>
                                            <?php } catch (Exception $e) {
                                                $error_modal = true;
                                                $errormsg = $e->getMessage();
                                            } ?>
                                        </datalist>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Precio:<span class="red-text-modal">*</span></label>
                                    <div class="col-sm">
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input class="form-control" type="number" name="productPrice" step="any" value="<?= $product->productPrice ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Proveedor:<span class="red-text-modal">*</span></label>
                                    <div class="input-group">
                                        <label class="input-group-text" for="suppliersNames">Opciones</label>
                                        <select class="form-select" id="suppliersNames" name="productSupplier" required>
                                            <option value="<?= $product->productSupplier ?>"><?= $product->productSupplier ?></option>
                                            <?php
                                            $suppliers = $conn->query("SELECT * FROM suppliers");
                                            try { ?>
                                                <?php while ($supplier = mysqli_fetch_object($suppliers)) : ?>
                                                    <option value="<?=$supplier->supplierId?>"><?= $supplier->supplierName ?> <?= $supplier->supplierLastName ?></option>
                                                <?php endwhile; ?>
                                            <?php } catch (Exception $e) {
                                                $error_modal = true;
                                                $errormsg = $e->getMessage();
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Codigo de barras:<span class="red-text-modal">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-upc-scan" viewBox="0 0 16 16">
                                                <path d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1h-3zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5zM.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5zm15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5zM3 4.5a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0v-7zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0v-7zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0v-7zm2 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-7zm3 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0v-7z"></path>
                                            </svg>
                                        </span>
                                        <input class="form-control" type="text" name="productCodebar" value="<?= $product->productCodebar ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm">Imagen:</label>
                                    <div class="input-group">
                                        <input type="file" class="form-control" name="productImage">
                                    </div>
                                </div>
                                <input type="hidden" value="<?=$product->productImage?>" name="productImage">
                                <input type="hidden" value="<?=$product->productQuantity?>" name="productQuantity">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" value="<?= $product->productId ?>" name="productId">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" name="updateProduct" value="Add">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>