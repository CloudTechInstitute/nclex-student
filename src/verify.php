<?php
include 'backend/php/connection.php';

$uuid = $_GET['uuid'];

$stmt = $conn->prepare("UPDATE students SET status = 'active' WHERE uuid = ?");
$stmt->bind_param("s", $uuid);
$stmt->execute();
$stmt->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Account Verified</title>
    <meta http-equiv="refresh" content="5;url=login.php">
</head>

<body>
    <h2>Your account has been verified!</h2>
    <p>You will be redirected to the login page in 5 seconds...</p>
</body>

</html>