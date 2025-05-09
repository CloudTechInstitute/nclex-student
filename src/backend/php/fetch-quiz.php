<?php
include 'connection.php';
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SESSION['studentID'])) {
        $userId = $_SESSION['studentID'];

        $query = "SELECT * FROM quizzes WHERE user_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("s", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            $quizzes = [];
            while ($row = $result->fetch_assoc()) {
                $quizzes[] = $row;
            }

            if (!empty($quizzes)) {
                echo json_encode(['status' => 'success', 'data' => $quizzes]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No quiz found']);
            }

            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to prepare query']);
        }

        $conn->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>