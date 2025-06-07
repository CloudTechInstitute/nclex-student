<?php

session_start();
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['uuid'])) {
        $userID = isset($_SESSION['studentID']) ? $_SESSION['studentID'] : null;
        $uuid = trim($_GET['uuid']);
        $note = isset($_POST['notes']) ? trim($_POST['notes']) : '';

        // Validate inputs
        if (empty($note)) {
            http_response_code(400); // Bad Request
            echo json_encode(['status' => 'error', 'message' => 'Missing required note field']);
            exit;
        }

        if (!$userID) {
            http_response_code(401); // Unauthorized
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
            exit;
        }

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO notes (`uuid`, `notes`, `user_id`) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $uuid, $note, $userID);

        if ($stmt->execute()) {
            http_response_code(201); // Created
            echo json_encode(['status' => 'success', 'message' => 'Note added successfully']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['status' => 'error', 'message' => 'Could not add note. Try again later.', 'error' => $stmt->error]);
        }

        $stmt->close();
        $conn->close();
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'Missing UUID in request']);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>