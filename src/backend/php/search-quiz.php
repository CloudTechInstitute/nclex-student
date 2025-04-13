<?php
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $searchQuery = $conn->real_escape_string($_GET['query']);

    $query = "SELECT * FROM quizzes WHERE title LIKE '%$searchQuery%'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $quizzes = [];
        while ($row = $result->fetch_assoc()) {
            $quizzes[] = $row;
        }
        echo json_encode(['status' => 'success', 'data' => $quizzes]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No matching quizzes found']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>