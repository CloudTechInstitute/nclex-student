<?php
include 'connection.php';
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['uuid']) && isset($_SESSION['studentID'])) {
        $uuid = $_GET['uuid'];
        $userID = $_SESSION['studentID'];

        // First query: Count correct answers
        $stmt = $conn->prepare("SELECT COUNT(*) AS correct FROM mock_questions WHERE user_id = ? AND mock_uuid = ? AND is_correct = 1");
        $stmt->bind_param("ss", $userID, $uuid);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $correct = $row['correct'];
            $stmt->close();

            // Second query: Count total attempts
            $stmt2 = $conn->prepare("SELECT * FROM mock WHERE user_id = ? AND mock_uuid = ?");
            $stmt2->bind_param("ss", $userID, $uuid);

            if ($stmt2->execute()) {
                $result2 = $stmt2->get_result();
                $row2 = $result2->fetch_assoc();
                $total = $row2['total_questions'];
                $stmt2->close();

                // Insert into completed_quiz
                $insert = $conn->prepare("INSERT INTO completed_mock (mock_uuid, user_id, user_score, total_questions) VALUES (?, ?, ?, ?)");
                $insert->bind_param("ssss", $uuid, $userID, $correct, $total);

                if ($insert->execute()) {
                    echo json_encode([
                        'status' => 'success',
                        'correct_answers' => $correct,
                        'total' => $total,
                        'wrong_answers' => $total - $correct,
                        'student' => $userID,
                        'quiz' => $uuid
                    ]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to insert completion record']);
                }

                $insert->close();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to fetch total count']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch correct count']);
        }

        $conn->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing uuid or not logged in']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>