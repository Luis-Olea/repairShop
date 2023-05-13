<?php
date_default_timezone_set('America/Mexico_City');
class clientReceiptClass
{
    function createClientReceipt($conn)
    {
        try {
            $sql = "SELECT * FROM users WHERE userEmail='" . $_SESSION['currentEmail'] . "'";
            $users = $conn->query($sql);
            while ($user = mysqli_fetch_object($users)) {
                $currentUser = $user->userId;
            }
        } catch (Exception $e) {
            return $e;
        }
        $change = ($_SESSION['totalCart'] - $_SESSION['actualQuantity'] - $_SESSION['creditToUse']) - 2 * ($_SESSION['totalCart'] - $_SESSION['actualQuantity'] - $_SESSION['creditToUse']);
        $currentTime = date('Y-m-d H:i:s');
        $sql = "INSERT INTO clientreceipt (cReceiptTotal, cReceiptChange, cReceiptAmount, cReceiptCreditAmount, cReceiptDate, cReceiptClientId, cReceiptUserId) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ddddsii", $_SESSION['totalCart'], $change, $_SESSION['actualQuantity'], $_SESSION['creditToUse'], $currentTime, $_SESSION['cartClient'], $currentUser);
        $stmt->execute();
        $clientReceiptId = $conn->insert_id;
        foreach ($_SESSION['cart'] as $product_id => $item) {
            $quantity = $item['quantity'];
            $productPrice = $item['price'];
            $idProduct = $product_id;
            try {
                $sql = "INSERT INTO clientreceiptproducts (cReceiptId, cReceiptProductId, cReceiptPrice, cReceiptQuantity) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iidd", $clientReceiptId, $idProduct, $productPrice, $quantity);
                $stmt->execute();
            } catch (Exception $e) {
                return $e;
            }
            try {
                $sql = "UPDATE products SET productQuantity = productQuantity - ? WHERE productId = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $quantity, $idProduct);
                $stmt->execute();
            } catch (Exception $e) {
                return $e;
            }
        }
        try {
            $sql = "UPDATE clients SET clientDue = clientDue + " . $_SESSION['creditToUse'] . " WHERE clientId = '" . $_SESSION['cartClient'] . "' ";
            $conn->query($sql);
        } catch (Exception $e) {
            return $e;
        }
        return TRUE;
    }
    function getClientReceipts($conn)
    {
        $sql = "SELECT * FROM clientreceipt ORDER BY cReceiptDate DESC";
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