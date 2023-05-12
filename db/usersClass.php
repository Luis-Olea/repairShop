<?php
class usersClass
{
    function createUser($conn)
    {
        $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (userName, userLastName, userAddress, userCellphone, userEmail, userPassword) " . "VALUES('" . $_POST['name'] . "','" . $_POST['last-name'] . "','" . $_POST['address'] . "','" . $_POST['mobileno'] . "','" . $_POST['email'] . "','" . $passwordHash . "')";
        return $conn->query($sql);
    }
    function getUsers($conn)
    {
        $sql = "SELECT * FROM users";
        return $conn->query($sql);
    }
    function getUserById($conn)
    {
        $sql = "SELECT * FROM users WHERE userId='" . $_POST['userId'] . "'";
        return $conn->query($sql);
    }
    function deleteuser($conn)
    {
        $sql = "DELETE FROM users WHERE userId='" . $_POST['userId'] . "'";
        return $conn->query($sql);
    }
    function updateUser($conn)
    {
        $sql = "UPDATE users SET userName='" . $_POST['userName'] . "',userLastName='" . $_POST['userLastName'] . "',userAddress='" . $_POST['userAdress'] . "', userCellphone='" . $_POST['userCellphone'] . "',userEmail='" . $_POST['userEmail'] . "' WHERE userId = '" . $_POST['userId'] . "' ";
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