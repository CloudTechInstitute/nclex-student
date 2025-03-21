<?php
include 'backend/php/connection.php';
$uuid = $_GET['uuid'];

$stmt = $conn->prepare("UPDATE students SET status = 'active' WHERE uuid = ?");
$stmt->bind_param("s", $uuid);
$stmt->execute();

echo "Your account has been verified!";