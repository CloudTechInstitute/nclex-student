<?php
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch questions
    $speedTestQuery = "SELECT * FROM questions ORDER BY RAND() LIMIT 70";
    $speedTestResult = $conn->query($speedTestQuery);

    if ($speedTestResult) {
        $questions = [];
        while ($row = $speedTestResult->fetch_assoc()) {
            $questions[] = $row;
        }

        http_response_code(200); // OK
        echo json_encode([
            'status' => 'success',
            'questions' => $questions
        ]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch questions']);
    }

    $conn->close();
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>