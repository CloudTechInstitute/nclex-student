<?php
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $searchQuery = $conn->real_escape_string($_GET['query']);

    $query = "SELECT * FROM categories WHERE category LIKE '%$searchQuery%' ";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        http_response_code(200); // OK
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        echo json_encode(['status' => 'success', 'data' => $categories]);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(['status' => 'error', 'message' => 'No matching categories found']);
    }

    $conn->close();
} else {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>