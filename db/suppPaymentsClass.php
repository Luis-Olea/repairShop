<?php
date_default_timezone_set('America/Mexico_City');
class suppPaymentsClass
{
    function createSuppPayment($conn)
    {
        try {
            $sql = "SELECT * FROM users WHERE userEmail='" . $_SESSION['currentEmail'] . "'";
            $users = $conn->query($sql);
            while ($user = mysqli_fetch_object($users)) {
                $currentUser = $user->userId;
            }
        } catch (Exception $e) {
        }
        $currentTime = date('Y-m-d H:i:s');
        $sql = "INSERT INTO supppayments (paymentAmount, paymentDate, paymentSupplierId, paymentUserId) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dsii", $_SESSION['totalPaymentSupp'], $currentTime, $_SESSION['currentSupplierCart'], $currentUser);
        $stmt->execute();
        $supppaymentId = $conn->insert_id;
        foreach ($_SESSION['cartS'] as $product_id => $item) {
            $quantity = $item['quantity'];
            $purchasePrice = $item['price'];
            $idProduct = $product_id;
            try {
                $sql = "INSERT INTO supppaymentproducts (supppaymentId, suppProductId, purchasePrice, quantity) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iiid", $supppaymentId, $idProduct, $purchasePrice, $quantity);
                $stmt->execute();

            } catch (Exception $e) {
            
            } 
        }
        try {
            $sql = "UPDATE suppliers SET supplierDue = supplierDue + " .$_SESSION['totalPaymentSupp']. " WHERE supplierId = '" .$_SESSION['currentSupplierCart']. "' ";
            $conn->query($sql);
        } catch (Exception $e) {
        }
    }
    function getSuppPayments($conn)
    {
        $sql = "SELECT * FROM supppayments ORDER BY paymentDate DESC";
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