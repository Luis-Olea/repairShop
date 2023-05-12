<?php
class ClientClass
{
    function createClient($conn)
    {
        $clientNIPHash = password_hash($_POST['clientNIP'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO clients (clientName, clientLastName, clientAddress, clientCellphone, clientEmail, clientNIP) " . "VALUES('" . $_POST['clientName'] . "','" . $_POST['clientLastName'] . "','" . $_POST['clientAddress'] . "','" . $_POST['clientCellphone'] . "','" . $_POST['clientEmail'] . "','" . $clientNIPHash . "')";
        return $conn->query($sql);
    }
    function getClients($conn)
    {
        $sql = "SELECT * FROM clients";
        return $conn->query($sql);
    }
    function getClientById($conn)
    {
        $sql = "SELECT * FROM clients WHERE clientId='" . $_POST['clientId'] . "'";
        return mysqli_query($conn, $sql);
    }
    function deleteClient($conn)
    {
        $sql = "DELETE FROM clients WHERE clientId='" . $_POST['clientId'] . "'";
        return $conn->query($sql);
    }
    function updateClient($conn)
    {
        $sql = "UPDATE clients SET clientName='" . $_POST['clientName'] . "',clientLastName='" . $_POST['clientLastName'] . "',clientAddress='" . $_POST['clientAddress'] . "', clientCellphone='" . $_POST['clientCellphone'] . "',clientEmail='" . $_POST['clientEmail'] . "',clientCreditLimit='" . $_POST['clientCreditLimit'] . "' WHERE clientId = '" . $_POST['clientId'] . "' ";
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