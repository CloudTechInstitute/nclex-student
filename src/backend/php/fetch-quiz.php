<?php
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT * FROM quizzes";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $quizzes = [];
        while ($row = $result->fetch_assoc()) {
            // Convert date format from Y-m-d to d-m-y
            $quizzes[] = $row;
        }
        echo json_encode(['status' => 'success', 'data' => $quizzes]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No quiz found']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>