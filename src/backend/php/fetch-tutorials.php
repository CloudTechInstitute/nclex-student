<?php
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT * FROM tutorials";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $tutorials = [];
        while ($row = $result->fetch_assoc()) {
            // Convert date format from Y-m-d to d-m-y
            $tutorials[] = $row;
        }
        echo json_encode(['status' => 'success', 'data' => $tutorials]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No tutorial found']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>