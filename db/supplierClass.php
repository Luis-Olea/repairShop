<?php
class supplierClass
{
    function createSuppplier($conn)
    {
        $sql = "INSERT INTO suppliers (supplierName, supplierLastName, supplierAddress, supplierCellphone, supplierEmail, supplierBrand) " . "VALUES('" . $_POST['name'] . "','" . $_POST['last-name'] . "','" . $_POST['address'] . "','" . $_POST['mobileno'] . "','" . $_POST['email'] . "','" . $_POST['brand'] . "')";
        return $conn->query($sql);
    }
    function getSupppliers($conn)
    {
        $sql = "SELECT * FROM suppliers";
        return $conn->query($sql);
    }
    function getSupplierById($conn)
    {
        $sql = "SELECT * FROM suppliers WHERE supplierId='" . $_POST['supplierId'] . "'";
        return mysqli_query($conn, $sql);
    }
    function deleteSupplier($conn)
    {
        $sql = "DELETE FROM suppliers WHERE supplierId='" . $_POST['supplierId'] . "'";
        return $conn->query($sql);
    }
    function updateSupplier($conn)
    {
        $sql = "UPDATE suppliers SET supplierName='" . $_POST['supplierName'] . "',supplierLastName='" . $_POST['supplierLastName'] . "',supplierAddress='" . $_POST['supplierAddress'] . "', supplierCellphone='" . $_POST['supplierCellphone'] . "',supplierEmail='" . $_POST['supplierEmail'] . "',supplierBrand='" . $_POST['supplierBrand'] . "' WHERE supplierId = '" . $_POST['supplierId'] . "' ";
        return $conn->query($sql);
    }
    function searchSupplier($conn)
    {
        $sql = "UPDATE suppliers SET supplierName='" . $_POST['supplierName'] . "',supplierLastName='" . $_POST['supplierLastName'] . "',supplierAddress='" . $_POST['supplierAddress'] . "', supplierCellphone='" . $_POST['supplierCellphone'] . "',supplierEmail='" . $_POST['supplierEmail'] . "',supplierBrand='" . $_POST['supplierBrand'] . "' WHERE supplierId = '" . $_POST['supplierId'] . "' ";
        return $conn->query($sql);
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