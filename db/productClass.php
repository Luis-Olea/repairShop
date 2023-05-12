<?php
class productClass
{
    function createProduct($conn)
    {
        $sql = "INSERT INTO products (productName, productBrand, productDescription, productCategory, productPrice, productSupplier, productCodebar, productImage) " ."VALUES('" .$_POST['productName']. "','" .$_POST['productBrand']. "','" .$_POST['productDescription'] . "','" .$_POST['productCategory']. "','" .$_POST['productPrice']. "','" .$_POST['productSupplier']. "','" .$_POST['productCodebar']. "','" .$_POST['productImage']. "')";
        return $conn->query($sql);
    }
    function getProducts($conn)
    {
        $sql = "SELECT * FROM products";
        return $conn->query($sql);
    }
    function getProductsById($conn)
    {
        $sql = "SELECT * FROM products WHERE productId='" .$_POST['productId']. "'";
        return mysqli_query($conn, $sql);
    }
    function deleteProduct($conn)
    {
        $sql = "DELETE FROM products WHERE productId='" . $_POST['productId'] . "'";
        return $conn->query($sql);
    }
    function updateProduct($conn)
    {
        $sql = "UPDATE products SET productName ='" . $_POST['productName'] . "',productBrand='" . $_POST['productBrand'] . "',productDescription='" . $_POST['productDescription'] . "',productCategory='" . $_POST['productCategory'] . "',productQuantity='" . $_POST['productQuantity'] . "',productPrice='" . $_POST['productPrice'] . "',productSupplier='" . $_POST['productSupplier'] . "',productCodebar='" . $_POST['productCodebar'] . "',productImage='" . $_POST['productImage'] . "' WHERE productId = '" . $_POST['productId'] . "' ";
        return $conn->query($sql);
    }
    function checkStock($conn, $quantity, $id) {
        $sql = "SELECT productQuantity FROM products WHERE productId = '".$id."'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $stock = $row['productQuantity'];
        
        if ($quantity > $stock) {
          return $stock;
        } else {
          return $quantity;
        }
      }
}
?>

<!-- Prevent data forwarding -->
<script type="text/javascript">
    /// DON'T DELETE.
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
