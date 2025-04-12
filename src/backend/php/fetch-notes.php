<?php
session_start();
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get and sanitize inputs
    if (isset($_GET['uuid'])) {
        $uuid = $_GET['uuid'];
        $userID = isset($_SESSION['studentID']) ? $_SESSION['studentID'] : null;

        if ($uuid && $userID) {
            $query = "SELECT notes FROM notes WHERE uuid = '$uuid' AND user_id = '$userID'";
            $result = $conn->query($query);

            if ($result && $result->num_rows > 0) {
                $notes = [];
                while ($row = $result->fetch_assoc()) {
                    $notes[] = $row;
                }
                echo json_encode(['status' => 'success', 'data' => $notes]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No notes found for the given category and user']);
            }

            $conn->close();
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Missing category_uuid or user_uuid',
            ]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing uuid parameter']);
    }
}
?>