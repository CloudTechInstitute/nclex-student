<?php
session_start();
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    if (isset($_SESSION['studentID'])) {
        $userID = $_SESSION['studentID'];
        $searchQuery = $conn->real_escape_string($_GET['query']);

        $query = "SELECT * FROM quizzes WHERE title LIKE '%$searchQuery%' AND user_id='$userID'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $quizzes = [];
            while ($row = $result->fetch_assoc()) {
                $quizzes[] = $row;
            }
            http_response_code(200); // OK
            echo json_encode(['status' => 'success', 'data' => $quizzes]);
        } else {
            http_response_code(404); // Not Found
            echo json_encode(['status' => 'error', 'message' => 'No matching quizzes found']);
        }

        $conn->close();
    } else {
        http_response_code(401); // Unauthorized
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    }
} else {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Bad request']);
}
?>