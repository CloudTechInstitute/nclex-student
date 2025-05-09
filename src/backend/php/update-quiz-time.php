<?php
include 'connection.php';
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['uuid']) && isset($_SESSION['studentID']) && isset($_POST['time_taken'])) {
        $uuid = $_POST['uuid'];
        $userID = $_SESSION['studentID'];
        $formattedTime = $_POST['time_taken'];

        // Convert formatted time (e.g., "2m 30s") to seconds
        $timeParts = explode("m", $formattedTime);
        $minutes = (int) trim($timeParts[0]);
        $seconds = (int) trim($timeParts[1]);

        // Convert to total seconds
        $totalSeconds = ($minutes * 60) + $seconds;

        // Update the completed_mock table with the time_taken in seconds
        $updateStmt = $conn->prepare("UPDATE completed_quiz SET time_taken = ? WHERE quiz_id = ? AND user_id = ?");
        $updateStmt->bind_param("dss", $totalSeconds, $uuid, $userID);

        if ($updateStmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Time updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update time']);
        }

        $updateStmt->close();
        $conn->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}