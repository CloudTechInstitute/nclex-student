<?php
session_start();
include 'connection.php';
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['studentID'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized or invalid request']);
    exit;
}

$userID = $_SESSION['studentID'];
$deviceID = $_POST['device_id'] ?? null;

if (!$deviceID) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Missing device ID']);
    exit;
}

// Ensure the device belongs to the logged-in user
$stmt = $conn->prepare("DELETE FROM user_devices WHERE id = ? AND user_id = ?");
$stmt->bind_param("is", $deviceID, $userID);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        http_response_code(200); // OK
        echo json_encode(['status' => 'success', 'message' => 'Device removed']);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(['status' => 'error', 'message' => 'Device not found or does not belong to user']);
    }
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Failed to remove device']);
}

$stmt->close();
$conn->close();
?>