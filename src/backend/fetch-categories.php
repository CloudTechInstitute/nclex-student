<?php
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT * FROM categories";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            // Convert date format from Y-m-d to d-m-y
            $categories[] = $row;
        }
        echo json_encode(['status' => 'success', 'data' => $categories]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No categories found']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>