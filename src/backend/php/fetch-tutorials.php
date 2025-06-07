<?php
include 'connection.php';
session_start();
header('Content-Type: application/json');

function set_status($code)
{
    http_response_code($code);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SESSION['studentID'])) {
        $userId = $_SESSION['studentID'];

        $query = "SELECT * FROM tutorials WHERE user_id = ? ORDER BY id DESC";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("s", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            $tutorials = [];
            while ($row = $result->fetch_assoc()) {
                $tutorials[] = $row;
            }

            if (!empty($tutorials)) {
                set_status(200); // OK
                echo json_encode(['status' => 'success', 'data' => $tutorials]);
            } else {
                set_status(404); // Not Found
                echo json_encode(['status' => 'error', 'message' => 'No tutorial found']);
            }

            $stmt->close();
        } else {
            set_status(500); // Internal Server Error
            echo json_encode(['status' => 'error', 'message' => 'Failed to prepare query']);
        }

        $conn->close();
    } else {
        set_status(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    }
} else {
    set_status(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>