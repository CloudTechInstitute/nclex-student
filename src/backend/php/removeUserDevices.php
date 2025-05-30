<?php
session_start();
include 'connection.php';
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['studentID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized or invalid request']);
    exit;
}

$userID = $_SESSION['studentID'];
$deviceID = $_POST['device_id'] ?? null;

if (!$deviceID) {
    echo json_encode(['status' => 'error', 'message' => 'Missing device ID']);
    exit;
}

// Ensure the device belongs to the logged-in user
$stmt = $conn->prepare("DELETE FROM user_devices WHERE id = ? AND user_id = ?");
$stmt->bind_param("is", $deviceID, $userID);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Device removed']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to remove device']);
}

$stmt->close();
$conn->close();
?>