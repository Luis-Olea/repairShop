<?php
date_default_timezone_set('America/Mexico_City');
class supplierPaymentClass
{
    function createSupplierPayment($conn)
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
        $currentTime = date('Y-m-d H:i:s');
        $sql = "INSERT INTO supplierpayments (sPayAmount, sPayDate, sPaySupplierId, sPayUserId ) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dsii", $_SESSION['creditToPay'], $currentTime, $_SESSION['cartSupplier'], $currentUser);
        $stmt->execute();
        try {
            $sql = "UPDATE suppliers SET supplierDue = supplierDue - " . $_SESSION['creditToPay'] . " WHERE supplierId = '" . $_SESSION['cartSupplier'] . "' ";
            $conn->query($sql);
        } catch (Exception $e) {
            return $e;
        }
        return TRUE;
    }
    function getSupplierPayments($conn)
    {
        $sql = "SELECT * FROM supplierpayments ORDER BY sPayDate DESC";
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