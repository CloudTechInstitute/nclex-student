<?php
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT * FROM tutorials";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $tutorials = [];
        while ($row = $result->fetch_assoc()) {
            $tutorials[] = $row;
        }
        http_response_code(200); // OK
        echo json_encode(['status' => 'success', 'data' => $tutorials]);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(['status' => 'error', 'message' => 'No tutorial found']);
    }

    $conn->close();
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>