<?php
date_default_timezone_set('America/Mexico_City');
class clientPaymentClass
{
    function createClientPayment($conn)
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
        $sql = "INSERT INTO clientpayments (cPaymentAmount, cPaymentDate, cPaymentClientId, cPaymentUserId) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dsii", $_SESSION['creditToUse'], $currentTime, $_SESSION['cartClient'], $currentUser);
        $stmt->execute();
        try {
            $sql = "UPDATE clients SET clientDue = clientDue - " . $_SESSION['creditToUse'] . " WHERE clientId = '" . $_SESSION['cartClient'] . "' ";
            $conn->query($sql);
        } catch (Exception $e) {
            return $e;
        }
        return TRUE;
    }
    function getClientPayments($conn)
    {
        $sql = "SELECT * FROM clientpayments ORDER BY cPaymentDate DESC";
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