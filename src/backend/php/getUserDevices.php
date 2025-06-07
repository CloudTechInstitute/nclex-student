<?php
session_start();
include 'connection.php';
header("Content-Type: application/json");

if (!isset($_SESSION['studentID'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$userID = $_SESSION['studentID'];
$currentDeviceHash = hash('sha256', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);

$stmt = $conn->prepare("SELECT id, ip_address, user_agent, login_time, last_used, device_hash FROM user_devices WHERE user_id = ?");
$stmt->bind_param("s", $userID);

if (!$stmt->execute()) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Database query failed']);
    $conn->close();
    exit;
}

$result = $stmt->get_result();

$devices = [];
while ($row = $result->fetch_assoc()) {
    $row['is_current'] = ($row['device_hash'] === $currentDeviceHash);
    $devices[] = $row;
}

http_response_code(200); // OK
echo json_encode(['status' => 'success', 'devices' => $devices]);
$conn->close();
?>