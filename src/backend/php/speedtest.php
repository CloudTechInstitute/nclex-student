<?php
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch employee records
    $speedTestQuery = "SELECT * FROM questions ORDER BY RAND() LIMIT 3";
    $speedTestResult = $conn->query($speedTestQuery);

    if ($speedTestResult) {
        $questions = [];
        while ($row = $speedTestResult->fetch_assoc()) {
            $questions[] = $row;
        }

        echo json_encode([
            'status' => 'success',
            'questions' => $questions
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch questions']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>