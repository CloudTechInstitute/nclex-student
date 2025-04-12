<?php

session_start(); // Important!
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['uuid'])) {
        $userID = isset($_SESSION['studentID']) ? $_SESSION['studentID'] : null;
        $uuid = trim($_GET['uuid']);
        $note = isset($_POST['notes']) ? trim($_POST['notes']) : '';

        // Validate inputs
        if (empty($note)) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required note field']);
            exit;
        }

        if (!$userID) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
            exit;
        }

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO notes (`uuid`, `notes`, `user_id`) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $uuid, $note, $userID);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Note added successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Could not add note. Try again later.', 'error' => $stmt->error]);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing UUID in request']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>