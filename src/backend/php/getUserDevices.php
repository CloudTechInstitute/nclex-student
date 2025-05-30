<?php
session_start();
include 'connection.php';
header("Content-Type: application/json");

if (!isset($_SESSION['studentID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$userID = $_SESSION['studentID'];
$currentDeviceHash = hash('sha256', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);

$stmt = $conn->prepare("SELECT id, ip_address, user_agent, login_time, last_used, device_hash FROM user_devices WHERE user_id = ?");
$stmt->bind_param("s", $userID);
$stmt->execute();
$result = $stmt->get_result();

$devices = [];
while ($row = $result->fetch_assoc()) {
    $row['is_current'] = ($row['device_hash'] === $currentDeviceHash);
    $devices[] = $row;
}

echo json_encode(['status' => 'success', 'devices' => $devices]);
$conn->close();
?>