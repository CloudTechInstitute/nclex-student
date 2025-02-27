<?php
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $searchQuery = $conn->real_escape_string($_GET['query']);

    $query = "SELECT * FROM categories WHERE category LIKE '%$searchQuery%' OR description LIKE '%$searchQuery%'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        echo json_encode(['status' => 'success', 'data' => $categories]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No matching categories found']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>