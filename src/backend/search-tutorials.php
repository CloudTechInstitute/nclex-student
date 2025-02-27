<?php
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $searchQuery = $conn->real_escape_string($_GET['query']);

    $query = "SELECT * FROM tutorials WHERE title LIKE '%$searchQuery%' OR description LIKE '%$searchQuery%'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $tutorials = [];
        while ($row = $result->fetch_assoc()) {
            $tutorials[] = $row;
        }
        echo json_encode(['status' => 'success', 'data' => $tutorials]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No matching tutorials found']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>